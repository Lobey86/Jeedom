//
//  ZDefsPublic.h
//  Part of Z-Way.C library
//
//  Created by Alex Skalozub on 1/26/12.
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

#ifndef zway_defs_public_h
#define zway_defs_public_h

#include "ZPlatform.h"

#ifdef _WINDOWS
    #define ZWINLINE __inline
    #ifdef __cplusplus
        #define ZWEXPORT_PRIVATE extern "C"
        #if defined(ZWAY_STATIC)
            #define ZWEXPORT extern "C"
        #elif defined(ZWAY_EXPORTS)
            #define ZWEXPORT extern "C" __declspec(dllexport)
        #else
            #define ZWEXPORT extern "C" __declspec(dllimport)
        #endif
    #else
        #define ZWEXPORT_PRIVATE extern
        #if defined(ZWAY_STATIC)
            #define ZWEXPORT 
        #elif defined(ZWAY_EXPORTS)
            #define ZWEXPORT __declspec(dllexport)
        #else
            #define ZWEXPORT __declspec(dllimport)
        #endif
    #endif
#else
    #define ZWINLINE inline
    #ifdef __cplusplus
        #define ZWEXPORT_PRIVATE extern "C"
        #define ZWEXPORT extern "C"
    #else
        #define ZWEXPORT_PRIVATE 
        #define ZWEXPORT 
    #endif
#endif

#define LIB_VERSION_ID 1

// Broadcast Node Id
#define NODE_BROADCAST 0xFF
// Maximum allowed Node Id
#define NODE_MAX 0xE8

// Available logging levels
enum _ZWLogLevel 
{
    Debug = 0,
    Verbose = 1,
    Information = 2,
    Warning = 3,
    Error = 4,
    Critical = 5,
    Silent = 6
};

// Use this type to define logging level
typedef int ZWLogLevel;

// Use this type to save error codes returned by library functions
typedef int ZWError;


// ZWay defs //

// State of controller
enum _ZWControllerState
{
    Idle = 0,
    AddReady = 1,
    AddNodeFound = 2,
    AddLearning = 3,
    AddDone = 4,
    RemoveReady = 5,
    RemoveNodeFound = 6,
    RemoveLearning = 7,
    LearnStarted = 8,
    LearnReady = 9,
    LearnNodeFound = 10,
    LearnLearning = 11,
    LearnDone = 12,
    ShiftReady = 13,
    ShiftNodeFound =14,
    ShiftLearning = 15,
    ShiftDone = 16,
};

// Use this type to save controller state
typedef int ZWState;

// Z-Way instance holder
struct _ZWay;
typedef struct _ZWay *ZWay;

// Definition of termination callback function
ZWEXPORT_PRIVATE typedef void (*ZTerminationCallback)(const ZWay zway);


// ZJob defs //

// Definition of custom callback for Function Classes and Command Classes calls
ZWEXPORT_PRIVATE typedef void (*ZJobCustomCallback)(const ZWay zway, ZWBYTE functionId, void* arg);


// ZDataHolder defs //

// Available types of Data
enum _ZWDataType 
{
    Empty = 0,
    Boolean = 1,
    Integer = 2,
    Float = 3,
    String = 4,
    Binary = 5,
    ArrayOfInteger = 6,
    ArrayOfFloat = 7,
    ArrayOfString = 8
};

// Use this type to define Data type
typedef ZWBYTE ZWDataType;

// Z-Way Data holder 
struct _ZDataHolder;
typedef struct _ZDataHolder *ZDataHolder;

// Z-Way Data children iterator
struct _ZDataIterator
{
    ZDataHolder data;
};
typedef struct _ZDataIterator *ZDataIterator;

// Types of Data change events
enum _ZWDataChangeType
{
    // Mutually exclusive flags
    Updated = 0x01,       // Value updated or child created
    Invalidated = 0x02,   // Value invalidated
    Deleted = 0x03,       // Data holder deleted - callback is called last time before being deleted
    ChildCreated = 0x04,  // New direct child node created

    // TODO: other notification types
    
    // ORed flags
    PhantomUpdate = 0x40, // Data holder updated with same value (only updateTime changed)
    ChildEvent = 0x80     // Event from child node
};

// Use this type to store Data change event type
typedef int ZWDataChangeType;

// Definition of data change callback function
ZWEXPORT_PRIVATE typedef void (*ZDataChangeCallback)(const ZWay wzay, ZWDataChangeType type, ZDataHolder data, void *arg);

// Types of Device change events
enum _ZWDeviceChangeType
{
    DeviceAdded = 0x01,
    DeviceRemoved = 0x02,
    InstanceAdded = 0x04,
    InstanceRemoved = 0x08,
    CommandAdded = 0x10,
    CommandRemoved = 0x20,
    ZDDXSaved = 0x100, // this callback notifies on ZDDX data change (to allow main program to purge buffers to disk/flash). For this event node_id = instance_id = command_id = 0
};

// Use this type to store Device change event type
typedef int ZWDeviceChangeType;

// Definition of device change callback function
ZWEXPORT_PRIVATE typedef void (*ZDeviceCallback)(const ZWay wzay, ZWDeviceChangeType type, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE command_id, void *arg);

// ChooseXml enumerator
struct _ZGuessedProduct
{
    int score;
    char *vendor;
    char *product;
    char *image_url;
    char *file_name;
};
typedef struct _ZGuessedProduct *ZGuessedProduct;

// List of NULL terminated registered devices Node Id
typedef ZWBYTE * ZWDevicesList;

// List of NULL terminated registered instances Id for device
typedef ZWBYTE * ZWInstancesList;

// List of NULL terminated registered Command Classes Id for instance of device
typedef ZWBYTE * ZWCommandClassesList;

#endif
