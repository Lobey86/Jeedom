#include <JeeLib.h>
#include <PortsSHT11.h>
#include <PortsBMP085.h>
#include <util/atomic.h>
#include "config.h"

#ifndef sensor_h
#define sensor_h

class Sensor {
private:
  int portNumber;
  int *type;
  Port *port;
  PortI2C *portI2C;
  BMP085 *bmp085;
  SHT11 *sht11;
  double impTime;
  int impValue;
  volatile byte eventValue;
  volatile boolean eventChanged;
  volatile uint32_t eventLastOn;
  int sizeofType;

  int smoothedAverage(int prev, int next, byte firstTime = 0);

public:
  Sensor(int _portNumber, int *_type, int _sizeofType);
  ~Sensor();

  boolean init();
  boolean isType(int _type);
  int lastValue[2][2];
  void getValue(int messageType,byte *_value);
  boolean setValue(int messageType, int value, int value1 = 0, int value2 = 0);
  boolean triggered();
  byte state();
  void poll();
  void doMeasure();
  int digiRead();
  int anaRead();
  int impRead();
  int ldrRead();
  int tempRead();
  int humiRead();
  int pressureI2CRead();
  boolean anaWrite(int value);
  boolean digiWrite(int value);
  boolean pwmWrite(int value);
  static int freeRam();
};
#endif




