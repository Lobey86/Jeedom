//
//  CommandClassesPublic.h
//  Part of Z-Way.C library
//
//  Created by Alex Skalozub on 2/1/12.
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

#ifndef zway_command_classes_public_h
#define zway_command_classes_public_h


// Command Class Basic //

// Send Basic Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_basic_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Basic Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: value
// Value
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_basic_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE value, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Wakeup //

// Send Wakeup Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_wakeup_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Wakeup CapabilityGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_wakeup_capabilities_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Wakeup NoMoreInformation (Sleep)
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_wakeup_sleep(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Wakeup Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: interval
// Wakeup interval in seconds
//
// @param: notification_node_id
// Node Id to be notified about wakeup
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_wakeup_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int interval, ZWBYTE notification_node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class NoOperation //

// Send NoOperation empty packet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
// This function is not exported into C and JS. Please use z_way_device_sned_nop() instead
// ZWEXPORT ZWError zway_cc_nop_send(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Battery //

// Send Battery Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_battery_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class ManufacturerSpecific //

// Send ManufacturerSpecific Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_manufacturer_specific_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Configuration //

// Send Configuration Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: parameter
// Parameter number (from 1 to 255)
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_configuration_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE parameter, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Configuration Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: parameter
// Parameter number (from 1 to 255)
//
// @param: value
// Value to be sent (negative and positive values are accepted, but will be stripped to size)
//
// @param: size
// @default: 0
// Size of the value (1, 2 or 4 bytes). Use 0 to guess from previously reported value if any
// 0 means use size previously obtained Get
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_configuration_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE parameter, int value, ZWBYTE size, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Configuration SetDefault
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: parameter
// Parameter number to be set to device default
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_configuration_set_default(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE parameter, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class SensorBinary //

// Send SensorBinary Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: sensorType
// Type of sensor to query information for
// @default: -1
// 0xFF to query information for the first available sensor type
// -1 to query information for all supported sensor types
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_sensor_binary_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int sensorType, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Association //

// Send Association Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group_id
// @default: 0
// Group Id (from 1 to 255)
// 0 requests all groups
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_association_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Association Set (Add)
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group_id
// Group Id (from 1 to 255)
//
// @param: include_node
// Node to be added to the group
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_association_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group_id, ZWBYTE include_node, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Association Remove
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group_id
// Group Id (from 1 to 255)
//
// @param: exclude_node
// Node to be removed from the group
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_association_remove(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group_id, ZWBYTE exclude_node, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Association GroupingsGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_association_groupings_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Meter //

// Send Meter Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: scale
// @default: -1
// Desired scale
// -1 for all scales
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_meter_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int scale, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Meter Reset
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_meter_reset(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Meter SupportedGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_meter_supported(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class SensorMultilevel //

// Send SensorMultilevel Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: sensor_type
// @default: -1
// Type of sensor to be requested.
// -1 means all sensor types supported by the device
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_sensor_multilevel_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int sensor_type, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class SensorConfiguration //

// Send SensorConfiguration Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_sensor_configuration_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SensorConfiguration Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: mode
// Value set mode
//
// @param: value
// Value
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_sensor_configuration_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE mode, float value, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class SwitchAll //

// Send SwitchAll Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_all_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SwitchAll Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: mode
// SwitchAll Mode: see definitions below
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_all_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE mode, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

#define SWITCH_ALL_SET_EXCLUDED_FROM_THE_ALL_ON_ALL_OFF_FUNCTIONALITY            0x00
#define SWITCH_ALL_SET_EXCLUDED_FROM_THE_ALL_ON_FUNCTIONALITY_BUT_NOT_ALL_OFF    0x01
#define SWITCH_ALL_SET_EXCLUDED_FROM_THE_ALL_OFF_FUNCTIONALITY_BUT_NOT_ALL_ON    0x02
#define SWITCH_ALL_SET_INCLUDED_IN_THE_ALL_ON_ALL_OFF_FUNCTIONALITY              0xFF

// Send SwitchAll Set On
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_all_set_on(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SwitchAll Set Off
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_all_set_off(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class SwitchBinary //

// Send SwitchBinary Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_binary_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SwitchBinary Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: value
// Value
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_binary_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBOOL value, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class SwitchMultilevel //

// Send SwitchMultilevel Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_multilevel_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SwitchMultilevel Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: level
// Level to be set
//
// @param: duration
// @default: 0xff
// Duration of change:
//  0 instantly
//  0x01-0x7f in seconds
//  0x80-0xfe in minutes mapped to 1-127 (value 0x80=128 is 1 minute)
//  0xff use device factory default
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_multilevel_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE level, ZWBYTE duration, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SwitchMultilevel StartLevelChange
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: dir
// Direction of change: 0 to incrase, 1 to decrase
//
// @param: duration
// @default: 0xff
// Duration of change:
//  0 instantly
//  0x01-0x7f in seconds
//  0x80-0xfe in minutes mapped to 1-127 (value 0x80=128 is 1 minute)
//  0xff use device factory default
//
// @param: ignoreStartLevel
// @default: TRUE
// If set to True, device will ignore start level value and will use it's curent value
//
// @param: startLevel
// @default: 50
// Start level to change from
//
// @param: incdec
// @default: 0
// Increment/decrement type for step:
//  0 Increment
//  1 Decrement
//  2 Reserved
//  3 No Inc/Dec
//
// @param: step
// @default: 0xff
// Step to be used in level change in percentage
// 0-99 mapped to 1-100%
// 0xff uses device factory default
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_multilevel_start_level_change(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE dir, ZWBYTE duration, ZWBOOL ignoreStartLevel, ZWBYTE startLevel, ZWBYTE incdec, ZWBYTE step, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SwitchMultilevel StopLevelChange
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_switch_multilevel_stop_level_change(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class MultiChannelAssociation //

// Send MultiChannelAssociation Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group_id
// @default: 0
// Group Id (from 1 to 255)
// 0 requests all groups
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_association_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send MultiChannelAssociation Set (Add)
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group_id
// Group Id (from 1 to 255)
//
// @param: include_node
// Node to be added to the group
//
// @param: include_instance
// Instance of the node to be added to the group
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_association_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group_id, ZWBYTE include_node, ZWBYTE include_instance, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send MultiChannelAssociation Remove
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group_id
// Group Id (from 1 to 255)
//
// @param: exclude_node
// Node to be removed from the group
//
// @param: exclude_instance
// Instance of the node to be removed from the group
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_association_remove(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group_id, ZWBYTE exclude_node, ZWBYTE exclude_instance, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send MultiChannelAssociation GroupingsGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_association_groupings_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class MultiChannel //

// Send MultiChannel Get (MultiInstance V1 command)
// Reports number of channels supporting a defined Command Class
// Depricated by MutliChannel V2 - needed for old devices only
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: cc_id
// Command Class Id in question
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE cc_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send MultiChannel EndpointFind
// Note that MultiChannel EndpointFind Report is not supported as useless. But one can still trap the response packet in logs.
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: generic
// Generic type in search
//
// @param: specific 
// Specific type in search
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_endpoint_find(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE generic, ZWBYTE specific, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send MultiChannel EndpointGet
// Get the number of available endpoints
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_endpoint_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send MultiChannel CapabilitiesGet
// Request information about the specified endpoint
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: endpoint
// Endpoint in question
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_capabilities_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE endpoint, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send MultiChannel Encapsulate
// Encapsulate data for the specified endpoint
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: endpoint
// Endpoint in question
//
// @param: length
// Length of encapsulated data
//
// @param: data
// Encapsulated data
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_multichannel_encapsulate(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE endpoint, ZWBYTE length, const ZWBYTE *data, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Node Naming //

// Send NodeNaming GetName and GetLocation
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_node_naming_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send NodeNaming GetName
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_node_naming_get_name(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send NodeNaming GetLocation
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_node_naming_get_location(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send NodeNaming SetName
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: name
// Value
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_node_naming_set_name(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWCSTR name, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send NodeNaming SetLocation
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: location
// Value
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_node_naming_set_location(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWCSTR location, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Thermostat SetPoint //

// Send ThermostatSetPoint Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: mode
// @default: -1
// Thermostat Mode
// -1 requests for all modes
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_setpoint_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int mode, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ThermostatSetPoint Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: mode
// Thermostat Mode
//
// @param: value
// temperature
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_setpoint_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int mode, float value, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Thermostat Mode //

// Send ThermostatMode Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_mode_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ThermostatMode Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: mode
// Thermostat Mode
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_mode_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE mode, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Thermostat Fan Mode //

// Send ThermostatFanMode Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_fan_mode_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ThermostatFanMode Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: on
// TRUE to turn fan on (and set mode), FALSE to comletely turn off (mode is ignored)
// 
// @param: mode
// Thermostat Fan Mode
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_fan_mode_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBOOL on, ZWBYTE mode, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Thermostat Fan State //

// Send ThermostatFanState Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_fan_state_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Thermostat Operating State //

// Send ThermostatOperatingState Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_operating_state_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ThermostatOperatingState Logging Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: state
// State number to get logging for
// 0 to get log for all supported states
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_thermostat_operating_state_logging_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE state, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Alarm Sensor //

// Send AlarmSensor SupportedGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_alarm_sensor_supported_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send AlarmSensor Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: type
// @default: -1
// Alarm type to get
// -1 means get all types
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_alarm_sensor_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int type, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Door Lock //

// Send DoorLock Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_door_lock_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send DoorLock Configuration Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_door_lock_configuration_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send DoorLock Configuration Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: mode
// Lock mode
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_door_lock_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE mode, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send DoorLock Configuration Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: opType
// Operation type
//
// @param: outsideState
// State of outside door handle
//
// @param: insideState
// State of inside door handle
//
// @param: lockMin
// Lock after a specified time (minutes part)
//
// @param: lockSec
// Lock after a specified time (seconds part)
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_door_lock_configuration_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE opType, ZWBYTE outsideState, ZWBYTE insideState, ZWBYTE lockMin, ZWBYTE lockSec, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Door Lock Logging //

// Send DoorLockLogging Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: record
// @default: 0
// Record number to get, or 0 to get last records
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_door_lock_logging_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE record, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class User Code //

// Send UserCode Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: user
// @default: -1
// User index to get code for (1 ... maxUsers)
// -1 to get codes for all users
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_user_code_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int user, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send UserCode Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: user
// User index to set code for (1...maxUsers)
// 0 means set for all users
//
// @param: code
// Code to set (4...10 characters long)
//
// @param: status
// Code status to set
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_user_code_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int user, ZWCSTR code, ZWBYTE status, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Time //

// Send Time TimeGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_time_time_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Time DateGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_time_date_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Time TimeOffsetGet
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_time_offset_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Time Parameters //

// Send TimeParameters Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_time_parameters_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send TimeParameters Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_time_parameters_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Clock //

// Send Clock Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_clock_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Clock Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_clock_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Scene Activation //

// Send SceneActivation Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: sceneId
// Scene Id
//
// @param: dimmingDuration
// @default: 0xff
// Dimming duration
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_scene_activation_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE sceneId, ZWBYTE dimmingDuration, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Scene Controller Conf //

// Send SceneControllerConf Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group
// @default: 0
// Group Id
// 0 requests all groups
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_scene_controller_conf_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SceneControllerConf Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: group
// Group Id
//
// @param: scene
// Scene Id
//
// @param: duration
// @default: 0x0
// Duration
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_scene_controller_conf_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE group, ZWBYTE scene, ZWBYTE duration, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Scene Actuator Conf //

// Send SceneActuatorConf Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: scene
// @default: 0
// Scene Id
// 0 means get current scene
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_scene_actuator_conf_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE scene, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SceneActuatorConf Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: scene
// Scene Id
//
// @param: level
// Level
//
// @param: dimming
// @default: 0xff
// Dimming
//
// @param: override
// @default: TRUE
// If false then the current settings in the device is associated with the Scene Id. If true then the Level value is used
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_scene_actuator_conf_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE scene, ZWBYTE level, ZWBYTE dimming, ZWBOOL override, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Indicator //

// Send Indicator Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_indicator_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Indicator Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: val
// Value to set
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_indicator_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE val, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Protection //

// Send Protection Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_protection_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Protection Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: state
// Local control protection state
//
// @param: rfState
// @default: 0
// RF control protection state
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_protection_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE state, ZWBYTE rfState, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Protection Exclusive Control Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_protection_exclusive_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Protection Exclusive Control Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: controlNodeId
// Node Id to have exclusive control over destination node
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_protection_exclusive_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE controlNodeId, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Protection Timeout Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_protection_timeout_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Protection Timeout Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: timeout
// Timeout in seconds
// 0 is no timer set
// -1 is infinite timeout
// max value is 191 minute (11460 seconds)
// values above 1 minute are rounded to 1-minute boundary
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_protection_timeout_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int timeout, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Schedule Entry Lock //

// Send ScheduleEntryLock Enable(All)
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: user
// User to enable/disable schedule for
// 0 to enable/disable for all users
//
// @param: enable
// TRUE to enable schedule, FALSE otherwise
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_schedule_entry_lock_enable(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int user, ZWBOOL enable, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ScheduleEntryLock Weekday Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: user
// User to get schedule for
// 0 to get for all users
//
// @param: slot
// Slot to get schedule for
// 0 to get for all slots
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_schedule_entry_lock_weekday_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int user, ZWBYTE slot, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ScheduleEntryLock Weekday Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: user
// User to set schedule for
//
// @param: slot
// Slot to set schedule for
//
// @param: dayOfWeek
// Weekday number (0..6)
// 0 = Sunday
// ...
// 6 = Saturday
//
// @param: startHour
// Hour when schedule starts (0..23)
//
// @param: startMinute
// Minute when schedule starts (0..59)
//
// @param: stopHour
// Hour when schedule stops (0..23)
//
// @param: stopMinute
// Minute when schedule stops (0..59)
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_schedule_entry_lock_weekday_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int user, ZWBYTE slot, ZWBYTE dayOfWeek, ZWBYTE startHour, ZWBYTE startMinute, ZWBYTE stopHour, ZWBYTE stopMinute, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ScheduleEntryLock Year Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: user
// User to enable/disable schedule for
// 0 to get for all users
//
// @param: slot
// Slot to get schedule for
// 0 to get for all slots
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_schedule_entry_lock_year_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int user, ZWBYTE slot, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send ScheduleEntryLock Year Set
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: user
// User to set schedule for
//
// @param: slot
// Slot to set schedule for
//
// @param: startYear
// Year in current century when schedule starts (0..99)
//
// @param: startMonth
// Month when schedule starts (1..12)
//
// @param: startDay
// Day when schedule starts (1..31)
//
// @param: startHour
// Hour when schedule starts (0..23)
//
// @param: startMinute
// Minute when schedule starts (0..59)
//
// @param: stopYear
// Year in current century when schedule stops (0..99)
//
// @param: stopMonth
// Month when schedule stops (1..12)
//
// @param: stopDay
// Day when schedule stops (1..31)
//
// @param: stopHour
// Hour when schedule stops (0..23)
//
// @param: stopMinute
// Minute when schedule stops (0..59)
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_schedule_entry_lock_year_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, int user, ZWBYTE slot, ZWBYTE startYear, ZWBYTE startMonth, ZWBYTE startDay, ZWBYTE startHour, ZWBYTE startMinute, ZWBYTE stopYear, ZWBYTE stopMonth, ZWBYTE stopDay, ZWBYTE stopHour, ZWBYTE stopMinute, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Command Class ClimateControlSchedule //
// This class is a dummy and have no exported functions


// Command Class MeterTableMonitor //

// Send StatusTableMonitor Status Get for a range of dates
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: maxResults
// @default: 0
// Maximum number of entries to get from log
// 0 means all matching entries
//
// @param: startDate
// Start date and time (local UNIX time)
//
// @param: endDate
// End date and time (local UNIX time)
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_meter_table_monitor_status_date_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE maxResults, time_t startDate, time_t endDate, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send StatusTableMonitor Status Get for specified depth
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: maxResults
// @default: 0
// Number of entries to get from log
// 0 means current status only
// 0xFF means all entries
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_meter_table_monitor_status_depth_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE maxResults, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send StatusTableMonitor Current Data Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: setId
// @default: 0
// Index of dataset to get data for
// 0 to get data for all supported datasets
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_meter_table_monitor_current_data_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE setId, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send StatusTableMonitor Historical Data Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: setId
// @default: 0
// Index of dataset to get data for
// 0 to get data for all supported datasets
//
// @param: maxResults
// @default: 0
// Maximum number of entries to get from log
// 0 means all matching entries
//
// @param: startDate
// Start date and time (local UNIX time)
//
// @param: endDate
// End date and time (local UNIX time)
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_meter_table_monitor_historical_data_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE setId, ZWBYTE maxResults, time_t startDate, time_t endDate, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);


// Command Class Alarm //

// Send Alarm Get
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: type
// @default: 0
// Type of alarm to get level for
// 0 to get level for all supported alarms (v2 and higher)
// 0xFF to get level for first supported alarm (v2 and higher)
//
// @param: event
// @default: 0
// Notification event to get level for
// This argument is ignored prior to Notification v3
// Must be 0 if type is 0xFF
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_alarm_get(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE type, ZWBYTE event, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send Alarm Set (v2 and higher)
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id
//
// @param: instance_id
// Instance Id
//
// @param: type
// Type of alarm to set level for
//
// @param: level
// Level to set (0x0 = off, 0xFF = on, other values are reserved)
//
// @param: successCallback
// @default: NULL
// Custom function to be called on function success
// NULL if callback is not needed
//
// @param: failureCallback
// @default: NULL
// Custom function to be called on function failure
// NULL if callback is not needed
//
// @param: callbackArg
// Custom argument to be passed to custom function to be called on function success or failure
//
ZWEXPORT ZWError zway_cc_alarm_set(ZWay zway, ZWBYTE node_id, ZWBYTE instance_id, ZWBYTE type, ZWBYTE level, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

#endif
