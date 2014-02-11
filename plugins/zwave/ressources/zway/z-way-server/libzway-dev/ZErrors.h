//
//  ZErrors.h
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

#ifndef zway_errors_h
#define zway_errors_h

//
// List of possible error codes returned by Z-Way library functions
//
enum _ZWError 
{
    NoError = 0,           // No error
    
    InvalidArg = -1,       // Bad arguments
    BadAllocation = -2,    // malloc errors
    NotImplemented = -3,   // Not implemented by the library
    NotSupported = -4,     // Function class not supported by the hardware
    AccessDenied = -5,     // -- not used
    ThreadingError = -6,   // Threading error
    InvalidOperation = -7, // -- not used
    InternalError = -8,    // Internal consistency error
    BadData = -9,          // Wrong packet from Z-Wave network or Discovery got bad data (stick communication failed)
    InvalidType = -10,     // Wrong data Data type requested
    InvalidThread = -12,   // Object accessed from invalid thread
    
    InvalidPort = -20,     // Can not open port
    InvalidConfig = -21,   // Error reading configuration and transation files
    
    // other errors
    NotPrimary = -25,      // Controller is not primary
    
    // internal codes - never exposed to outside
    JobNotFound = -30,     // Can not remove job from queue: job not found
    JobAlreadyAdded = -31, // Job is already in the queue
    DuplicateObject = -32, // Queueing of duplicate job. New job removed, callbacks merged with old one.
    
    PacketTooBig = -40,    // Packet is too big to be encapsulated in MultiCmd - sending non-encapsulated
};

#endif
