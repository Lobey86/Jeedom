#include "sensor.h"
#include "Wire.h"
#include "BlinkM_funcs.h"

Sensor::Sensor(int _portNumber, int *_type, int _sizeofType) : 
portNumber(_portNumber), type(_type),sizeofType(_sizeofType/sizeof _type[0]), eventValue(0), eventChanged(0), eventLastOn(0) {
}

Sensor::~Sensor() {
  if (port != 0)
    delete port;

  if (sht11 != 0)
    delete sht11;

  if (bmp085 != 0)
    delete bmp085;
}

boolean Sensor::isType(int _type) {
  for(int i=0;i< sizeofType;i++){
    if(type[i] == _type){
      return true; 
    }
  }
  return false; 
}

boolean Sensor::init() {
  lastValue[0][0] = 0;
  lastValue[1][0] = 0;
#if DEBUG 
  Serial.print("Initialisation du port ");
  Serial.print(portNumber);
  Serial.print(" de type ");
  for(int i=0;i< sizeofType;i++){
    Serial.print(type[i]);
    Serial.print(' ');
  }
  Serial.println();
  serialFlush();
#endif

  lastValue[0][0] = 0;
  lastValue[1][0] = 0;

  if (portNumber < 5) {
    if(isType(1) || isType(2) || isType(4) 
      || isType(51) || isType(52) || isType(53)
      || isType(101) || isType(102) || isType(103)){
      port = new Port(portNumber);
    }
    if(isType(1)){
      port->mode(INPUT);
    }
    if(isType(2)){
      port->mode2(INPUT);
      lastValue[0][0] = 'a';
    }
    if(isType(3)){
      sht11 = new SHT11(portNumber);
      lastValue[0][0] = 'h';
      lastValue[1][0] = 't';
    }
    if(isType(4)){
      lastValue[0][0] = 'a';
    }

    if(isType(51)){
      //Mise en place des interruption et event si necessaire
      port->digiWrite(EVENT_PULLUP);
#ifdef PCMSK2
      bitSet(PCMSK2, portNumber + 3);
      bitSet(PCICR, PCIE2);
#endif
    }

    if(isType(52)){
      //Mise en place des interruption et event si necessaire
      port->digiWrite(EVENT_PULLUP);
#ifdef PCMSK2
      bitSet(PCMSK2, portNumber + 3);
      bitSet(PCICR, PCIE2);
#endif
    }
    if(isType(53)){
      //Mise en place des interruption et event si necessaire
      port->digiWrite(EVENT_PULLUP);
#ifdef PCMSK2
      bitSet(PCMSK2, portNumber + 3);
      bitSet(PCICR, PCIE2);
#endif
      lastValue[0][0] = 'i';
      impTime = 0;
      lastValue[1][1] = 0;
      impValue = 0;
    }
    if(isType(101) || isType(103)){
      port->mode(INPUT);
    }
    if(isType(102)){
      port->mode2(INPUT);
      lastValue[0][0] = 'a';
    }
  }

  if (portNumber == 5) { //Port I2C
    if(isType(1)){
      BlinkM_begin();
      delay(100);
      BlinkM_off(0);

      if(isType(2)){
        portI2C = new PortI2C(portNumber);
        bmp085 = new BMP085(*portI2C, 3);
        bmp085->getCalibData();
      }    
    }
  }
}

#if !PASSIF
void Sensor::getValue(int messageType,byte *_value) {
#if DEBUG 
  Serial.print("Demande pour le port ");
  Serial.print(portNumber);
  Serial.println();
  serialFlush();
#endif   
  if (portNumber == 0) {
    if (messageType == 114) { //114 = char r
#if DEBUG 
      Serial.println("Ask for free ram");
      serialFlush();
#endif  
      _value[0] = freeRam();
    }
    if (messageType == 117) { //117 = char u
#if DEBUG 
      Serial.println("Ask for uptime");
      serialFlush();
#endif  
      unsigned long uptime = millis();    
      _value[0] = (int) ((uptime & 0xFF000000) >> 24);
      _value[1] = (int) ((uptime & 0x00FF0000) >> 16);
      _value[2] = (int) ((uptime & 0x0000FF00) >> 8);
      _value[3] = (int) ((uptime & 0X000000FF)); 
    }
  } 
  else if (portNumber < 5) {
    if(isType(1) && messageType == 100){ //100 = char d
      _value[0] = digiRead();
    }
    if(isType(2) && messageType == 97) { //97 = char a
      _value[0] = anaRead();
    }
    if(isType(3)){
      if (messageType == 104) { //104 = char h, 63 = char ?
        _value[0] = humiRead();
      }
      if (messageType == 116) { //116 = char t, 63 = char ?
        _value[0] = tempRead();
      }
    }
    if(isType(4) && messageType == 97){ //97 = char a
      _value[0] = ldrRead();
    }
    if(isType(51) && messageType == 100){ //100 = char d
      _value[0] = state();
    }
    if(isType(53) && messageType == 105) { //100 = char d
      _value[0] = impRead();
    }
  }
  else if(portNumber == 5){
    if(isType(1) && messageType == 99) { //100 = char p
      BlinkM_fadeToRGB(0x09, _value[0] , _value[1] , _value[2] );
    }
    if(isType(2) && messageType == 112) { //100 = char p
      _value[0] = pressureI2CRead();
    }
  }
}
#endif 

int Sensor::humiRead() {
#if DEBUG 
  Serial.print("Demande au capteur SH11 humidite : ");
  serialFlush();
#endif 
  sht11->measure(SHT11::HUMI,shtDelay);
  float humi, temp;
  sht11->calculate(humi, temp);
#if DEBUG 
  Serial.println(humi);
  serialFlush();
#endif    
  return humi;
}

int Sensor::tempRead() {
#if DEBUG 
  Serial.print("Demande au capteur SH11 temperature : ");
  serialFlush();
#endif 
  sht11->measure(SHT11::TEMP,shtDelay);
  float humi, temp;
  sht11->calculate(humi, temp);
#if DEBUG 
  Serial.println(temp);
  serialFlush();
#endif  
  return temp;
}

int Sensor::digiRead() {
#if DEBUG 
  Serial.println("Lecture de DIO");
  serialFlush();
#endif
  return port->digiRead();
}

int Sensor::anaRead() {
#if DEBUG 
  Serial.println("Lecture de AIO");
  serialFlush();
#endif
  return port->anaRead();
}

int Sensor::impRead() {
#if DEBUG 
  Serial.print("Lecture du nombre d'impulsion : ");
  serialFlush();
#endif
  double timelife = (millis() - impTime) / 1000;
  if (timelife < IMPULSE_PERIOD) {
    impValue += lastValue[1][1];
    timelife += IMPULSE_PERIOD;
  }
  lastValue[1][1] = (impValue / timelife) * IMPULSE_PERIOD;
  impTime = millis();
  impValue = 0;
#if DEBUG 
  Serial.println(lastValue[1][1]);
  serialFlush();
#endif    
  return lastValue[1][1];
}

int Sensor::ldrRead() {
#if DEBUG 
  Serial.print("Demande au capteur LDR : ");
  serialFlush();
#endif
  port->digiWrite2(1); // activation de la pin AIO
  byte value = ~port->anaRead() >> 2;  
  port->digiWrite2(0); // desactivation de la pin AIO (economie de batterie)
#if DEBUG 
  Serial.println(value);
  serialFlush();
#endif  
  return value;
}

int Sensor::pressureI2CRead() {
#if DEBUG 
  Serial.print("Demande au capteur BMP085 pression : ");
  serialFlush();
#endif 
  bmp085->startMeas(BMP085::PRES);
  delay(32);
  bmp085->getResult(BMP085::PRES);
  int32_t press;
  int16_t temp;
  bmp085->calculate(temp, press);
#if DEBUG 
  Serial.println(press);
  serialFlush();
#endif  
  return press;
}

#if !PASSIF
boolean Sensor::setValue(int messageType, int value, int value1, int value2) {
#if DEBUG 
  Serial.print("Ecriture pour le port ");
  Serial.print(portNumber);
  Serial.print("Type : ");
  Serial.print( messageType);
  Serial.println();
  serialFlush();
#endif
  if (portNumber < 5) {
    if (isType(101) && messageType == 100) { //100 = char d
      return digiWrite(value);
    }
    if (isType(102) && messageType == 97) { //97 = char a
      return anaWrite(value);
    }
    if (isType(103) && messageType == 112) { //112 = char p
      return pwmWrite(value);
    }
  } 
  else if (portNumber == 5) {
    if(isType(1) && messageType == 99){
#if DEBUG 
      Serial.print("Ecriture rgb ");
      Serial.println();
      serialFlush();
#endif
      BlinkM_fadeToRGB(0x09, value, value1, value2);
    }
  }
  return false;
}

boolean Sensor::anaWrite(int value) {
#if DEBUG 
  Serial.print("Changement etat AIO a ");
  Serial.println(value);
  serialFlush();
#endif 
  port->digiWrite2(value);
  return true;
}

boolean Sensor::digiWrite(int value) {
#if DEBUG 
  Serial.print("Changement etat DIO a ");
  Serial.println(value);
  serialFlush();
#endif 
  port->digiWrite(value);
  return true;
}

boolean Sensor::pwmWrite(int value) {
#if DEBUG 
  Serial.print("Changement etat DIO [PWM] a ");
  Serial.println(value);
  serialFlush();
#endif 
  port->anaWrite(value);
  return true;
}
#endif

int Sensor::freeRam() {
  extern int __heap_start, *__brkval;
  int v;
  return (int) &v - (__brkval == 0 ? (int) &__heap_start : (int) __brkval);
}

boolean Sensor::triggered() {
  byte f = eventChanged;    
  eventChanged = 0;
  return f; 
}

byte Sensor::state() {
  byte f = eventValue;
  if (isType(52)) {
    return f;
  }
  if (eventLastOn > 0){
    ATOMIC_BLOCK(ATOMIC_RESTORESTATE) {
      if (((millis() - eventLastOn) / 1000) < EVENT_HOLD_TIME){
        f = 1;
      } 
    }
  }
  return f;
}

void Sensor::poll() {
  if (isType(51)) {
    byte pin = port->digiRead() ^ EVENT_INVERTED;
    if (pin) {
      if (!state()) {
        eventChanged = true;
      }
      eventLastOn = millis();
    }
    eventValue = pin;
  }
  else {
    byte pin = port->digiRead();
    if (eventValue != pin) {
      ATOMIC_BLOCK(ATOMIC_RESTORESTATE) {
        pin = port->digiRead();
        if (eventValue != pin) {
          eventValue = pin;
          if(isType(53)){
            impValue++;
          }
          else{
            eventChanged = true;
          }
        }
      }
    }
  } 
}

void Sensor::doMeasure() {
  byte firstTime = lastValue[0][1] == 0; // special case to init running avg
  if(isType(3)){
    lastValue[0][1] = smoothedAverage(lastValue[0][1], humiRead(), firstTime);
    lastValue[1][1] = smoothedAverage(lastValue[1][1], tempRead(), firstTime);
  }
  if(isType(4)){
    lastValue[0][1] = smoothedAverage(lastValue[0][1], ldrRead(), firstTime);
  }
  if(isType(2)){
    lastValue[0][1] = smoothedAverage(lastValue[0][1], anaRead(), firstTime);
  }
}

int Sensor::smoothedAverage(int prev, int next, byte firstTime) {
  if (firstTime)
    return next;
  return ((SMOOTH - 1) * prev + next + SMOOTH / 2) / SMOOTH;
}

































































