#include <EtherCard.h>
#include <JeeLib.h>
#include <util/atomic.h>
#include <avr/sleep.h>

/*****************************Configuration reseau*****************************/
static byte mymac[] = {
  0x74, 0x69, 0x69, 0x2D, 0x30, 0x31};
static byte myip[] = {
  192, 168, 0, 11};
static byte gwip[] = {
  192,168,0,254 };
static byte dnsip[] = {
  192,168,0,254 };
static char addrServer[] PROGMEM = "TODO";
static byte hisip[] = { 
  192,168,0,2 }; // remote webserver

/*****************************Configuration master*****************************/
#define DEBUG               0
#define SID_MASTER          1                  //SID du master (unique)
#define FREQ_MASTER         RF12_868MHZ      //Frequence RF12 du jeenode RF12_433MHZ, RF12_868MHZ, RF12_915MHZ
#define GROUP_MASTER        212
#define DEBIT_SERIAL        57600            //Utile uniquement en debug
#define RADIO_SYNC_MODE     0
#define COLLECT             0x20 // collect mode, i.e. pass incoming without sending acks
#define RETRY_PERIOD        10  // how soon to retry if ACK didn't come in
#define RETRY_LIMIT         3   // maximum number of times to retry
#define ACK_TIME            100  // number of milliseconds to wait for an ack
#define TIMEOUT             500  // number of milliseconds to wait for for reponse of jeenode before send NR (no response)


/******************************************************************************/
byte Ethernet::buffer[800];
BufferFiller bfill;
MilliTimer timeout;
boolean waitJeenodeRep = false;
word pos;
Stash stash;
/*********************************FreeRam***************************************/
static int freeRam () {
  extern int __heap_start, *__brkval; 
  int v; 
  return (int) &v - (__brkval == 0 ? (int) &__heap_start : (int) __brkval); 
}

/**************************Generation de la page*******************************/
static word pageUptime(long milliseconde) {
  bfill = ether.tcpOffset();
  long t = milliseconde / 1000;
  word h = t / 3600;
  byte m = (t / 60) % 60;
  byte s = t % 60;

  bfill.emit_p(PSTR(
  "HTTP/1.0 200 OK\r\n"
    "Content-Type: text/html\r\n"
    "Pragma: no-cache\r\n"
    "\r\n"
    "$D$D:$D$D:$D$D")
    , h/10, h%10, m/10, m%10, s/10, s%10);
  return bfill.position();
}

static word page(char message[]) {
  bfill = ether.tcpOffset();
  bfill.emit_p(PSTR(
  "HTTP/1.0 200 OK\r\n"
    "Content-Type: text/html\r\n"
    "Pragma: no-cache\r\n"
    "\r\n"
    "$S"),
  message);
  return bfill.position();
}

static char* intToChar(int i){
  char buffer[4]; 
  sprintf(buffer, "%d", i);
  return buffer;
}

static word pageFavicon() {
  bfill = ether.tcpOffset();
  bfill.emit_p("HTTP/1.0 200 OK\r\n"
    "Content-Type: image/x-icon\r\n"
    "Content-length: 0\r\n\r\n");
  return bfill.position();
}


static void sendToJeedom(){
#if DEBUG 
  Serial.print("Sending event to server...");
  serialFlush();
#endif
  byte sd = stash.create();
  stash.print("api=TODO&type=jeenode");
  stash.print("&n=");
  stash.print(rf12_data[1]);
  stash.print("&p=");
  stash.print(rf12_data[2]);
  stash.print("&t=");
  stash.print(rf12_data[3]);
  stash.print("&v=");
  stash.print(rf12_data[6]);
  stash.save();
  Stash::prepare(PSTR("POST /core/api/jeeApi.php HTTP/1.0" "\r\n" 
    "Host: $F" "\r\n" 
    "Content-Type: application/x-www-form-urlencoded; charset=utf-8" "\r\n" 
    "Accept: application/xhtml+xml" "\r\n" 
    "Content-Length: $D" "\r\n" 
    "\r\n" 
    "$H"),
  addrServer, stash.size(), sd);
  ether.tcpSend();           
  if (stash.freeCount() <= 3) {   
    Stash::initMap(56); 
  } 
#if DEBUG 
  Serial.println("End");
  serialFlush();
#endif 
}
/***************************Get parametres**************************************/
static int getArg(const char* data, const char* key,char sortie[],int nbChar) {
  if (ether.findKeyVal(data + 6, sortie, nbChar, key) > 0){
    return 0;
  }
  return -1;
}

/***************************Envoi d'un message*********************************/
static byte waitForAck() {
  MilliTimer ackTimer;
  while (!ackTimer.poll(ACK_TIME)) {
    if (rf12_recvDone() && rf12_crc == 0 &&
      rf12_hdr == (RF12_HDR_DST | RF12_HDR_CTL | SID_MASTER))
      return 1;
    set_sleep_mode(SLEEP_MODE_IDLE);
    sleep_mode();
  }
  return 0;
}

static byte sendAck() {
  if (RF12_WANTS_ACK) {
    rf12_sendNow(RF12_ACK_REPLY, 0, 0);
    rf12_sendWait(RADIO_SYNC_MODE);
#if DEBUG 
    Serial.println("Send ack");
    serialFlush();
#endif 
  }    
}

struct message {
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

static int sendMessage() {
  for (byte i = 0; i < RETRY_LIMIT; ++i) {
    rf12_sendNow(RF12_HDR_ACK, &message, sizeof message);
    rf12_sendWait(RADIO_SYNC_MODE);
    byte acked = waitForAck();
    if (acked) {
      return 1;
    }
    delay(RETRY_PERIOD * 100);
  }
  return 0;     
}


#if DEBUG
void serialFlush () {
#if ARDUINO >= 100
  Serial.flush();
#endif  
  delay(2); // make sure tx buf is empty before going back to sleep
}
#endif

/************************Counfiguration du master******************************/
void setup() {
#if DEBUG
  Serial.begin(DEBIT_SERIAL);
  Serial.println("***********************************");
  Serial.println("Master pour jeedom");
  serialFlush();
#endif

  rf12_initialize(SID_MASTER, FREQ_MASTER, GROUP_MASTER);
  //rf12_encrypt(RF12_EEPROM_EKEY);
  message.expe = SID_MASTER;

#if DEBUG
  Serial.println("RF12 configuration : ");
  Serial.print("SID Master : ");
  Serial.println(SID_MASTER);
  Serial.print("Freq Master : ");
  Serial.println(FREQ_MASTER);
  Serial.print("Group Master : ");
  Serial.println(GROUP_MASTER);
  serialFlush();
#endif

  if (ether.begin(sizeof Ethernet::buffer, mymac) == 0){
#if DEBUG
    Serial.println("Failed to access Ethernet controller");
#endif  
  }

  ether.staticSetup(myip,gwip,dnsip);
#if DEBUG
  Serial.println();
  Serial.println("Ethernet configuration : ");
  ether.printIp("IP:  ", ether.myip);
  ether.printIp("GW:  ", ether.gwip);  
  ether.printIp("DNS: ", ether.dnsip);
  serialFlush();
#endif 

  ether.copyIp(ether.hisip, hisip);
#if DEBUG    
  ether.printIp("SRV: ", ether.hisip);
  Serial.println("***********************************\n");
  serialFlush();
#endif
}


/****************************Boucle principale*********************************/
void loop() {   
  /************************Listen RF12******************************/
  if (rf12_recvDone() && rf12_crc == 0 && rf12_data[0] == SID_MASTER) {
    sendAck(); 
#if DEBUG
    Serial.println("Message recu");
    serialFlush();
#endif  
    if(rf12_data[4] == 1){                
      sendToJeedom();  
    }
    else{
      timeout.set(0); //On a eu une reponse on desarme le timeout
      waitJeenodeRep = false;
      if(rf12_data[3] == 117){ //117 = char u
        unsigned long uptime;
        uptime  = ((unsigned long) rf12_data[6]) << 24;
        uptime |= ((unsigned long) rf12_data[7]) << 16;
        uptime |= ((unsigned long) rf12_data[8]) << 8;
        uptime |= ((unsigned long) rf12_data[9]);
        ether.httpServerReply(pageUptime(uptime));
      }
      else{
        ether.httpServerReply(page(intToChar(rf12_data[6])));
      } 
    }

  }   
  /************************Listen HTTP******************************/
  if(!waitJeenodeRep){ // Tant qu'on a pas de reponse du jeenode on prend pas de nouvelle requete
    pos = ether.packetLoop(ether.packetReceive());
    if (pos) {
      char* data = (char *) Ethernet::buffer + pos;
      if (data[5] == '?') {
        char nodeID[3];
        getArg(data,"n",nodeID,sizeof nodeID);
        char port[2];
        getArg(data,"p",port,sizeof port);
        char type[2]; 
        getArg(data,"t",type,sizeof type);
        char mode[2]; 
        getArg(data,"m",mode,sizeof mode);
        char value[4]; 
        getArg(data,"v",value,sizeof value);
        char value1[4]; 
        getArg(data,"v1",value1,sizeof value1);
        char value2[4]; 
        getArg(data,"v2",value2,sizeof value2);
        char value3[4]; 
        getArg(data,"v3",value3,sizeof value3);

        if(atoi(nodeID)!=SID_MASTER){
          message.dest = atoi(nodeID);   
          message.port = atoi(port);
          message.event = 0;
          message.type = type[0];
          message.mode = mode[0];
          message.value = atoi(value);
          message.value1 = atoi(value1);
          message.value2 = atoi(value2);
          message.value3 = atoi(value3);

          if(sendMessage() != 1){
            ether.httpServerReply(page("NR"));
          }
          else{
            timeout.set(TIMEOUT); //Armement du timer de timeout
            waitJeenodeRep = true;
          }
        }
        else{              
          if(atoi(port) == 0){              
            if(type[0] == 'r'){
#if DEBUG          
              Serial.println("Ask for free ram");
              serialFlush();
#endif 
              ether.httpServerReply(page(intToChar(freeRam())));
            }
            if(type[0] == 'u'){
#if DEBUG          
              Serial.println("Ask for uptime");
              serialFlush();
#endif 
              ether.httpServerReply(pageUptime(millis()));
            }
          }
        }          
      }
      else if(data[5] == 'f'){
#if DEBUG          
        Serial.println("Ask for favicon");
        serialFlush();
#endif 
        ether.httpServerReply(pageFavicon());
      }
      else{
#if DEBUG          
        Serial.println("Unknow page");
        serialFlush();
#endif 
        ether.httpServerReply(page("NR"));
      }
    }
  }

  if(timeout.poll()){
#if DEBUG          
    Serial.println("Timeout");
    serialFlush();
#endif 
    ether.httpServerReply(page("NR"));
    waitJeenodeRep = false;
  }
}









