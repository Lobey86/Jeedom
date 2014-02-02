#define DEBUG                   0
#define SID_MASTER              1               //SID du master (ecoute uniquement celui-ci)
#define SID_JEENODE             2               //SID du Jeenode (doit etre unique)
#define FREQ_JEENODE            RF12_868MHZ     //FrÃ©quence RF12 du jeenode RF12_433MHZ, RF12_868MHZ, RF12_915MHZ
#define GROUP_JEENODE           212
#define DEBIT_SERIAL            57600           //Utile uniquement en debug
#define RADIO_SYNC_MODE         2
#define REPORT_EVERY            5               // report every N measurement cycles
#define MEASURE_PERIOD          1               // how often to measure, in minutes
#define RETRY_PERIOD            10             // how soon to retry if ACK didn't come in milliseconds
#define RETRY_LIMIT             3               // maximum number of times to retry
#define ACK_TIME                10             // number of milliseconds to wait for an ack
#define PASSIF                  1 
#define IMPULSE_PERIOD          60              // Impulsion par IMPULSE_PERIOD en seconde
#define EVENT_HOLD_TIME         60              // hold EVENT value this many seconds after change
#define EVENT_PULLUP            1               // set to one to pull-up the EVENT input pin
#define EVENT_INVERTED          0               // 0 or 1, to match EVENT reporting high or low  - check on your PIR if there are resitor between + and data if => 1
#define SMOOTH                  3               // smoothing factor used for running averages

#ifndef config_h
#define config_h
/***************************Configuration des ports****************************/
/*
 * 2 : entrée analogique activée
 * 3 : entrée SH11
 * 4 : entrée LDR
 
 ************************Event*****************
 * 51 : PIR
 * 52 : entrée digital activée (event)
 * 53 : Compteur d'impulsion
 
 ************************Action*****************
 * 101 :  sortie digital activée
 * 102 :  sortie analogique (0 ou 1) activée
 * 103 :  sortie pwm
 */
/**************PORT 1****************/
static int PORT_1[2]= {
  4,51};

/**************PORT 2****************/
static int PORT_2[1] = {
  0};

/**************PORT 3****************/
static int PORT_3[1] = {
  0};

/**************PORT 4****************/
static int PORT_4[1] = {
  3};

/**************PORT I2C****************/
/*
 * 0 : aucun systeme connecté au port
 * 1 : blinkM
 * 1 : Pressure
 */
static int PORT_I2C[1] = {
  0};
/***************************Fin configuration port I2C***************************/


static struct message {
  byte dest;
  byte expe;
  byte port;
  byte type;
  byte event;
  byte mode;
  byte value;
  byte value1;
  byte value2;
  byte value3;
} 
message;

static boolean catchEvent = true;


static void shtDelay () {
    Sleepy::loseSomeTime(32); // must wait at least 20 ms
}


#if DEBUG
static void serialFlush () {
#if ARDUINO >= 100
  Serial.flush();
#endif  
  delay(2); // make sure tx buf is empty before going back to sleep
}
#endif
#endif
