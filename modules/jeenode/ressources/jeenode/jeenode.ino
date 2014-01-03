#include <JeeLib.h>
#include <avr/sleep.h>
#include <util/atomic.h>
#include "Wire.h"
#include "config.h"
#include "sensor.h"

/***************************Creation des sensors***************************/
Sensor sensors[6] = {
  Sensor(0,0,0),
  Sensor(1,PORT_1,sizeof PORT_1),
  Sensor(2,PORT_2,sizeof PORT_2),
  Sensor(3,PORT_3,sizeof PORT_3),
  Sensor(4,PORT_4,sizeof PORT_4),
  Sensor(5,PORT_I2C,sizeof PORT_I2C)
  };

  /***************************Creation des variables interne***************************/

  // Use militimer (scheduler kill serial):
enum { 
  MEASURE,REPORT, TASK_END };
static word schedbuf[TASK_END];
Scheduler scheduler (schedbuf, TASK_END);
static int reportCount;
// has to be defined because we're using the watchdog for low-power waiting
ISR(WDT_vect) { 
  Sleepy::watchdogEvent(); 
}

ISR(PCINT2_vect) {
  if(catchEvent){
    for(int i = 1;i<5;i++){
      if(sensors[i].isType(51) || sensors[i].isType(52) || sensors[i].isType(53)){  
        sensors[i].poll();
      }
    }
  }
}
/***************************Envoi d'un message*********************************/

static byte waitForAck() {
  MilliTimer ackTimer;
  while (!ackTimer.poll(ACK_TIME)) {
    if (rf12_recvDone() && rf12_crc == 0 &&
      rf12_hdr == (RF12_HDR_DST | RF12_HDR_CTL | SID_JEENODE))
      return 1;
    set_sleep_mode(SLEEP_MODE_IDLE);
    sleep_mode();
  }
  return 0;
}


static void sendToMaster() {
  for (byte i = 0; i < RETRY_LIMIT; ++i) {
#if PASSIF
    rf12_sleep(RF12_WAKEUP);
#endif
#if DEBUG
    Serial.print("Sending...");
    serialFlush();
#endif
    rf12_sendNow(RF12_HDR_ACK, &message, sizeof message);
    rf12_sendWait(RADIO_SYNC_MODE);
#if DEBUG
    Serial.print("Send...");
#endif
    byte acked = waitForAck();
#if PASSIF
    rf12_sleep(RF12_SLEEP);
#endif
    if (acked) {
#if DEBUG
      Serial.println("Ack du master");
      serialFlush();
#endif
#if PASSIF
      scheduler.timer(MEASURE, MEASURE_PERIOD);
#endif
      return;
    }
    delay(RETRY_PERIOD * 100);
  }
#if PASSIF
  scheduler.timer(MEASURE, MEASURE_PERIOD);
#endif
#if DEBUG
  Serial.println("Aucun ack du master! Eteint?");
  serialFlush();
#endif
}

#if !PASSIF
static byte sendAck() {
  if (RF12_WANTS_ACK) {
    rf12_sendStart(RF12_ACK_REPLY, 0, 0);
    rf12_sendWait(RADIO_SYNC_MODE);
#if DEBUG 
    Serial.println("Send ack\n");
    serialFlush();
#endif 
  }    
}
#endif
/*************************************Do report*************************************************/

static void doReport() {
  catchEvent=false;
#if DEBUG  
  Serial.println("Begin report to master");
  serialFlush();
#endif
  message.event = 1;
  message.mode = '?';

  for(int i=1;i<5;i++){ //Envoie du rapport
    message.port = i;
    if(sensors[i].isType(51) || sensors[i].isType(52)){
      message.type = 'd';
      message.value = sensors[i].state();
      sendToMaster();
    }
    if(sensors[i].isType(53)){
      message.type = 'i';
      message.value = sensors[i].impRead();
      sendToMaster();
    }
    for(int j = 0;j<2;j++){
      if(sensors[i].lastValue[j][0] > 0){
        message.type = sensors[i].lastValue[j][0];
        message.value = sensors[i].lastValue[j][1];
        sendToMaster();
      }
    }
  }
#if DEBUG  
  Serial.println("End report\n");
  serialFlush();
#endif
  catchEvent=true;
}

/*************************************Setup*************************************************/
void setup () {
#if DEBUG
  Serial.begin(DEBIT_SERIAL);
  Serial.println("***************************\nJeenode for jeedom");
  serialFlush();
#endif

  rf12_initialize(SID_JEENODE, FREQ_JEENODE, GROUP_JEENODE);
  //rf12_encrypt(RF12_EEPROM_EKEY);
  message.expe = SID_JEENODE;
  message.dest = SID_MASTER; 

#if PASSIF
  rf12_sleep(RF12_SLEEP); // power down
#if DEBUG
  Serial.println("\nPassive mode");
  serialFlush();
#endif
#endif

#if DEBUG
  Serial.println("RF12 configuration : ");
  Serial.print("SID Jeenode : ");
  Serial.println(SID_JEENODE);
  Serial.print("Freq Jeenode : ");
  Serial.println(FREQ_JEENODE);
  Serial.print("Group Jeenode : ");
  Serial.println(GROUP_JEENODE);
  Serial.print("Jeenode Master's : ");
  Serial.println(SID_MASTER);
  serialFlush();
#endif

  delay(20);//Wait for complete init of RF12
  for(int i=1;i<6;i++){ //Initialisation de tous les ports
    sensors[i].init();
  }

  reportCount = REPORT_EVERY;     // report right away for easy debugging

#if PASSIF
  scheduler.timer(MEASURE, 0);
#endif

#if DEBUG  
  Serial.print("Free ram : ");
  Serial.println(Sensor::freeRam());
  serialFlush();
#endif

#if DEBUG  
  Serial.println("***********************************");
  serialFlush();
#endif
}

/*************************************Boucle principale*************************************************/
void loop () {
  for(int i=1;i<5;i++){ //Verification sur tous les ports s'il y eu un event
    if ((sensors[i].isType(51) || sensors[i].isType(52)) && sensors[i].triggered()) {
#if DEBUG 
      Serial.print("Alerte sur le port ");
      Serial.println(i);
      serialFlush();
#endif 
      message.port = i;
      message.type = 'd';
      message.event = 1;
      message.value = sensors[i].state();
      sendToMaster();
    }
  }

  switch (scheduler.pollWaiting()) {
  case MEASURE:
    // reschedule these measurements periodically
    scheduler.timer(MEASURE, MEASURE_PERIOD * 600);
    for(int i=1;i<5;i++){ //Recuperation valeur des ports
      sensors[i].doMeasure();
    }
    // every so often, a report needs to be sent out
    if (reportCount++ >= REPORT_EVERY) {
      reportCount = 0;
      scheduler.timer(REPORT, 0);
    }
    break;
  case REPORT:
    doReport();
    break;
  }
  /********************Si le jeenode est pas passif*******************************/
#if !PASSIF
  if (rf12_recvDone() && rf12_crc == 0 && rf12_data[0] == SID_JEENODE && rf12_data[1] == SID_MASTER) { 
#if DEBUG 
    Serial.println("J'ai recu un message");
    serialFlush();
#endif 

    sendAck();

    message.event = 0;    
    message.port = rf12_data[2];
    message.type = rf12_data[3];
    message.mode = rf12_data[5];
    message.value = rf12_data[6];
    message.value1 = rf12_data[7];
    message.value2 = rf12_data[8];
    message.value3 = rf12_data[9];

    if(message.port <= 5){       
      if(message.mode==63){
        sensors[message.port].getValue(message.type,&message.value);
#if DEBUG 
        Serial.print("J'ai comme valeur : ");
        Serial.print(message.value);
        Serial.print(" | ");
        Serial.print(message.value1);
        Serial.print(" | ");
        Serial.print(message.value2);
        Serial.print(" | ");
        Serial.println(message.value3);
        serialFlush();
#endif 
        sendToMaster();
      }
      if(message.mode==33){
        sensors[message.port].setValue(message.type,message.value,message.value1,message.value2);
        sendToMaster();
      }
    }
  }
#endif 
} 





















