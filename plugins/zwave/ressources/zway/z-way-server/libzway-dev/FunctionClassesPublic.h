//
//  FunctionClassesPublic.h
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

#ifndef zway_function_classes_public_h
#define zway_function_classes_public_h

// Request Serial API capabilities
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_get_serial_api_capabilities(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Set Serial API timeouts
//
// @param: zway
// ZWay object instance
//
// @param: ackTimeout
// Time for the stick to wait for ACK (in 10ms units)
//
// @param: byteTimeout
// Time for the stick to assemble a full packet (in 10ms units)
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
ZWEXPORT ZWError zway_fc_serial_api_set_timeouts(ZWay zway, ZWBYTE ackTimeout, ZWBYTE byteTimeout, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request initial information about devices in network
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_serial_api_get_init_data(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Set controller node information
//
// @param: zway
// ZWay object instance
//
// @param: listening
// Listening flag
//
// @param: optional
// Optional flag (set if device supports more CCs than described as mandatory for it's Device Type)
//
// @param: flirs1000
// FLiRS 1000 flag (hardware have to be based on FLiRS library to support it)
//
// @param: flirs250
// FLiRS 250 flag (hardware have to be based on FLiRS library to support it)
//
// @param: generic_class
// Generic Device Type
//
// @param: specific_class
// Specific Device Type
//
// @param: nif_size
// Size of the new NIF
//
// @param: nif
// New NIF
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
ZWEXPORT ZWError zway_fc_serial_api_application_node_info(ZWay zway, ZWBOOL listening, ZWBOOL optional, ZWBOOL flirs1000, ZWBOOL flirs250, ZWBYTE generic_class, ZWBYTE specific_class, ZWBYTE nif_size, const ZWBYTE *nif, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Start WatchDog
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_watchdog_start(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Stop WatchDog
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_watchdog_stop(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request Home Id and controller Node Id
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_get_home_id(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request controller capabilities (primary role, SUC/SIS availability)
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_get_controller_capabilities(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request controller hardware version
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_get_version(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request SUC Node Id
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_get_suc_node_id(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Enable or disable SUC/SIS functionality of the controller
//
// @param: zway
// ZWay object instance
//
// @param: enable
// True to enable functionality, False to disable
//
// @param: sis
// True to enable SIS functionality, False to enable SUC only
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
ZWEXPORT ZWError zway_fc_enable_suc(ZWay zway, ZWBOOL enable, ZWBOOL sis, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Assign new SUC/SIS or disable existing
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id to be assigned/disabled as SUC/SIS
//
// @param: enable
// True to enable, False to disable
//
// @param: sis
// True to assign SIS role, False to enable SUC role only
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
ZWEXPORT ZWError zway_fc_set_suc_node_id(ZWay zway, ZWBYTE node_id, ZWBOOL enable, ZWBOOL sis, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Read single byte from EEPROM
//
// @param: zway
// ZWay object instance
//
// @param: offset
// Offset in application memory in EEPROM
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
ZWEXPORT ZWError zway_fc_memory_get_byte(ZWay zway, unsigned short offset, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Read multiple bytes from EEPROM
//
// @param: zway
// ZWay object instance
//
// @param: offset
// Offset in application memory in EEPROM
//
// @param: length
// Number of byte to be read
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
ZWEXPORT ZWError zway_fc_memory_get_buffer(ZWay zway, unsigned short offset, ZWBYTE length, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Write single byte to EEPROM
//
// @param: zway
// ZWay object instance
//
// @param: offset
// Offset in application memory in EEPROM
//
// @param: data
// Byte to be written
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
ZWEXPORT ZWError zway_fc_memory_put_byte(ZWay zway, unsigned short offset, ZWBYTE data, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Write multiple bytes to EEPROM
//
// @param: zway
// ZWay object instance
//
// @param: offset
// Offset in application memory in EEPROM
//
// @param: length
// Length of byte sequence to be written
//
// @param: data
// Bytes to be written
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
ZWEXPORT ZWError zway_fc_memory_put_buffer(ZWay zway, unsigned short offset, ZWBYTE length, const ZWBYTE *data, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Checks if node is failed
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id to be checked
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
ZWEXPORT ZWError zway_fc_is_failed_node(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Abort send data
// Note that this function works unpredictably in multi callback environment !
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_send_data_abort(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Soft reset
// Restarts Z-Wave chip
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_serial_api_soft_reset(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send data
// Packets are sent in AUTO_ROUTE mode with EXPLRER_FRAME enabled for listening devices (ignored if not supported by the hardware [based on 5.0x branch])
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id (NODE_BROADCAST to send non-routed broadcast packet)
//
// @param: length
// Packet size
//
// @param: data
// Paket payload
//
// @param: description
// @default: NULL
// Packet description for queue inspector and logging
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
ZWEXPORT ZWError zway_fc_send_data(ZWay zway, ZWBYTE node_id, ZWBYTE length, const ZWBYTE *data, ZWCSTR description, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Get node protocol info
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id of the device in question
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
ZWEXPORT ZWError zway_fc_get_node_protocol_info(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Get routing table line
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id of the device in question
//
// @param: remove_bad
// @default: FALSE
// Exclude failed nodes from the listing
//
// @param: remove_repeaters
// @default: FALSE
// Exclude repeater nodes from the listing
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
ZWEXPORT ZWError zway_fc_get_routing_table_line(ZWay zway, ZWBYTE node_id, ZWBOOL remove_bad, ZWBOOL remove_repeaters, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Assign return route to specified node
// Get Serial API capabilities
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id of the device that have to store new route
//
// @param: dest_id
// Destination Node Id of the route
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
ZWEXPORT ZWError zway_fc_assign_return_route(ZWay zway, ZWBYTE node_id, ZWBYTE dest_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Assign return route to SUC
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id of the device that have to store route to SUC
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
ZWEXPORT ZWError zway_fc_assign_suc_return_route(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Delete return route
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id of the device that have to delete all assigned return routes
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
ZWEXPORT ZWError zway_fc_delete_return_route(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Delete return route to SUC
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id of the device that have to delete route to SUC
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
ZWEXPORT ZWError zway_fc_delete_suc_return_route(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Reset the controller
// Note: this function will delete ALL data from the Z-Wave chip and restore it to factory default !
// Sticks based on 4.5x and 6.x SDKs will also generate a new Home Id.
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_set_default(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send SUC Node Id
// Informs portable and static controllers about new or deleted SUC/SIS
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id of the device that have to be informed about new or deleted SIC/SIS
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
ZWEXPORT ZWError zway_fc_send_suc_node_id(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Send NIF of the stick
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Destination Node Id (NODE_BROADCAST to send non-routed broadcast packet)
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
ZWEXPORT ZWError zway_fc_send_node_information(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request NIF of a device
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id to be requested for a NIF
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
ZWEXPORT ZWError zway_fc_request_node_information(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Remove failed node from network
// Before removing SDK will check that the device is really unreachable
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id to be removed from network
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
ZWEXPORT ZWError zway_fc_remove_failed_node(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Replace failed node with a new one
// Be ware that a failed node can be replaced by a node of another type. This can lead to probles!
// Always request device NIF and force re-interview after successful replace process.
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id to be replaced by new one
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
ZWEXPORT ZWError zway_fc_replace_failed_node(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request network topology update from SUC/SIS
// Note that this process may also fail due more than 64 changes from last sync. In this case a re-inclusion of the controller (self) is required.
//
// @param: zway
// ZWay object instance
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
ZWEXPORT ZWError zway_fc_request_network_update(ZWay zway, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Request neighbours update for specific node
//
// @param: zway
// ZWay object instance
//
// @param: node_id
// Node Id to be requested for it's neighbours
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
ZWEXPORT ZWError zway_fc_request_node_neighbour_update(ZWay zway, ZWBYTE node_id, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Set/stop Learn mode
//
// @param: zway
// ZWay object instance
//
// @param: startStop
// Start Learn mode if True, stop if False
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
ZWEXPORT ZWError zway_fc_set_learn_mode(ZWay zway, ZWBOOL startStop, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Start/stop Inclusion of a new node
// Available on primary and inclusion controllers
//
// @param: zway
// ZWay object instance
//
// @param: startStop
// Start inclusion mode if True, stop if False
//
// @param: highPower
// @default: TRUE
// Use full power during this operation if True. On False use low power mode.
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
ZWEXPORT ZWError zway_fc_add_node_to_network(ZWay zway, ZWBOOL startStop, ZWBOOL highPower,  ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Start/stop exclusion of a node
// Note that this function can be used to exclude a device from previous network before including in ours.
// Available on primary and inclusion controllers
//
// @param: zway
// ZWay object instance
//
// @param: startStop
// Start exclusion mode if True, stop if False
//
// @param: highPower
// @default: FALSE
// Use full power during this operation if True. On False use low power mode.
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
ZWEXPORT ZWError zway_fc_remove_node_from_network(ZWay zway, ZWBOOL startStop, ZWBOOL highPower, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Set new primary controller (also known as Controller Shift)
// Same as Inclusion, but the newly included device will get the role of primary.
// Available only on primary controller.
//
// @param: zway
// ZWay object instance
//
// @param: startStop
// Start controller shift mode if True, stop if False
//
// @param: highPower
// @default: TRUE
// Use full power during this operation if True. On False use low power mode.
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
ZWEXPORT ZWError zway_fc_controller_change(ZWay zway, ZWBOOL startStop, ZWBOOL highPower, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Create new primary controller by SUC controller
// Same as Inclusion, but the newly included device will get the role of primary.
// Available only on SUC.
// Be careful not to create two primary controllers! This can lead to network malfunction!
//
// @param: zway
// ZWay object instance
//
// @param: startStop
// Start create new primary mode if True, stop if False
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
ZWEXPORT ZWError zway_fc_create_new_primary(ZWay zway, ZWBOOL startStop, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

// Change Z-Wave.Me Z-Stick 4 frequency
// This function is specific for Z-Wave.Me hardware
//
// @param: zway
// ZWay object instance
//
// @param: freq
// 0 for EU, 1 for RU
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
ZWEXPORT ZWError zway_fc_zme_freq_change(ZWay zway, ZWBYTE freq, ZJobCustomCallback successCallback, ZJobCustomCallback failureCallback, void* callbackArg);

#endif
