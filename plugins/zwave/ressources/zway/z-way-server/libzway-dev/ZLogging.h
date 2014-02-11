//
//  ZLogging.h
//  Part of Z-Way.C library
//
//  Created by Alex Skalozub on 1/6/12.
//  Based on Z-Way source code written by Christian Paetz and Poltorak Serguei
//
//  Copyright (c) 2012 Z-Wave.Me
//  All rights reserved
//  info@z-wave.me
//
//  This source file is subject to the terms and conditions of the
//  Z-Wave.Me Software License Agreement which restricts the manner
//  in which it may be used.
//

#ifndef zway_logging_h
#define zway_logging_h

#include "ZDefsPublic.h"

ZWEXPORT void zway_log(const ZWay zway, ZWLogLevel level, ZWCSTR message, ...);

ZWEXPORT const char *zway_strerror(ZWError err);

ZWEXPORT void zway_log_error(const ZWay zway, ZWLogLevel level, ZWCSTR message, ZWError err);

ZWEXPORT void zway_dump(const ZWay zway, ZWLogLevel level, ZWCSTR prefix, size_t length, const ZWBYTE *buffer);

#endif // zway_logging_h
