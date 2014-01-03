#include <JeeLib.h>
#define PIR_PORT 1
#define PIR_PULLUP 1
#define PIR_INVERTED 0

Port pir (PIR_PORT);

int lastValue = 0;
int value = 0;

void setup (){
  Serial.begin(57600);
  Serial.println("\n[PIR tester]");
  pir.digiWrite(PIR_PULLUP);
}

void loop(){
  value = pir.digiRead()^PIR_INVERTED;
  if(value != lastValue){
      lastValue=value;
      Serial.print("PIR changed to ");
      Serial.println(value);
  }
}
