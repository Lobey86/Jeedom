//
//  ZDataPublic.h
//  Part of Z-Way.C library
//
//  Created by Alex Skalozub on 1/30/12.
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

#ifndef zway_data_public_h
#define zway_data_public_h

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// Don't forget to use locks when updating and reading data:
// zway_data_acquire_lock(zway) and zway_data_release_lock(zway) have to wrap ALL dataholder operations.
// Note that dataholder/devices/instance/commandclasses change/add/delete callbacks do already have lock aquired, so it is optional to add locks in callback handlers.
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// Invalidates a value in DataHolder
//
// @param: zway
// ZWay object instance
//
// @param: data
// Data object instance
//
// @param: invalidate_children
// Will invalidate all child Data holders if set to True
//
ZWEXPORT ZWError zway_data_invalidate(const ZWay zway, ZDataHolder data, ZWBOOL invalidate_children);

// Attach callback function for Data holder change event
// Supress add of duplicate address of function callback
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: callback
// Function to be called on change event
// Call sytax is: callback(const ZWay zway, ZWDataChangeType type, ZDataHolder data, void *arg);
//
// @param: watch_children
// Will be trigger on child events too
//
// @param: arg
// Additional arguments passed to callback function
// NB! Important not to reffer here to this or other dataholders or their members by pointers!
//
ZWEXPORT ZWError zway_data_add_callback(const ZWay zway, ZDataHolder data, ZDataChangeCallback callback, ZWBOOL watch_children, void *arg);

// Detach callback function from Data holder change event
// Delete all callbacks with same address of function callback
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: callback
// Function to be detached from change event
//
ZWEXPORT ZWError zway_data_remove_callback(const ZWay zway, ZDataHolder data, ZDataChangeCallback callback);

// Attach callback function for Data holder change event
// Allows many callbacks with same address of callback function (but supress duplicate callback/arg pair)
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: callback
// Function to be called on change event
// Call sytax is: callback(const ZWay zway, ZWDataChangeType type, ZDataHolder data, void *arg);
//
// @param: watch_children
// Will be trigger on child events too
//
// @param: arg
// Additional arguments passed to callback function
// NB! Important not to reffer here to this or other dataholders or their members by pointers!
//
ZWEXPORT ZWError zway_data_add_callback_ex(const ZWay zway, ZDataHolder data, ZDataChangeCallback callback, ZWBOOL watch_children, void *arg);

// Detach callback function from Data holder change event
// Remove only once callback reffered as functio address and argument pair
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: callback
// Function to be detached from change event
//
// @param: arg
// Additional arguments passed to callback function
//
ZWEXPORT ZWError zway_data_remove_callback_ex(const ZWay zway, ZDataHolder data, ZDataChangeCallback callback, void *arg);

// Set empty value of Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
ZWEXPORT ZWError zway_data_set_empty(const ZWay zway, ZDataHolder data);

// Set value of Data holder (boolean, integer, float)
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// New value
//
ZWEXPORT ZWError zway_data_set_boolean(const ZWay zway, ZDataHolder data, ZWBOOL value);
ZWEXPORT ZWError zway_data_set_integer(const ZWay zway, ZDataHolder data, int value);
ZWEXPORT ZWError zway_data_set_float(const ZWay zway, ZDataHolder data, float value);

// Set string value of Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// New value
//
// @param: copy
// Copy from user buffer to internal memory if True
//
ZWEXPORT ZWError zway_data_set_string(const ZWay zway, ZDataHolder data, ZWCSTR value, ZWBOOL copy);

// Set string value of Data holder using ptrintf format
// Parsed value is copied to internal memory
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: format
// Format string in printf style
//
// @param: ...
// Additional parameters parsed in printf style using format string
//
ZWEXPORT ZWError zway_data_set_string_fmt(const ZWay zway, ZDataHolder data, ZWCSTR format, ...);

// Set array value of Data holder (integer of float)
// Array value is copied to internal memory
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// New array value
//
// @param: count
// Size of the array
//
ZWEXPORT ZWError zway_data_set_integer_array(const ZWay zway, ZDataHolder data, int *value, size_t count);
ZWEXPORT ZWError zway_data_set_float_array(const ZWay zway, ZDataHolder data, float *value, size_t count);

// Set string array value of Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// New array value
//
// @param: length
// Size of the array
//
// @param: copy
// Copy from user buffer to internal memory if True
//
ZWEXPORT ZWError zway_data_set_binary(const ZWay zway, ZDataHolder data, const ZWBYTE *value, size_t length, ZWBOOL copy);

// Set binary value of Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// New value
//
// @param: count
// Size (in bytes) of the value
//
// @param: copy
// Copy from user buffer to internal memory if True
//
ZWEXPORT ZWError zway_data_set_string_array(const ZWay zway, ZDataHolder data, ZWCSTR *value, size_t count, ZWBOOL copy);


// Get type of Data holder value
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: type
// Returned type
//
ZWEXPORT ZWError zway_data_get_type(const ZWay zway, ZDataHolder data, ZWDataType *type);


// Get Data holder value
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// Returned value
//
ZWEXPORT ZWError zway_data_get_boolean(const ZWay zway, ZDataHolder data, ZWBOOL *value);
ZWEXPORT ZWError zway_data_get_integer(const ZWay zway, ZDataHolder data, int *value);
ZWEXPORT ZWError zway_data_get_float(const ZWay zway, ZDataHolder data, float *value);
ZWEXPORT ZWError zway_data_get_string(const ZWay zway, ZDataHolder data, ZWCSTR *value);

// Get Data holder value
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// Returned value
//
// @param: count
// Size of the returned array
//
ZWEXPORT ZWError zway_data_get_integer_array(const ZWay zway, ZDataHolder data, const int **value, size_t *count);
ZWEXPORT ZWError zway_data_get_float_array(const ZWay zway, ZDataHolder data, const float **value, size_t *count);
ZWEXPORT ZWError zway_data_get_string_array(const ZWay zway, ZDataHolder data, const ZWCSTR **value, size_t *count);

// Get Data holder value
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: value
// Returned value
//
// @param: length
// Size of the returned value
//
ZWEXPORT ZWError zway_data_get_binary(const ZWay zway, ZDataHolder data, const ZWBYTE **value, size_t *length);

// Search a Data holder by name starting from a defined Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: root
// Root object instance
//
// @param: path
// Path to search for (dot separated)
//
ZWEXPORT ZDataHolder zway_find_data(const ZWay zway, const ZDataHolder root, const char *path);

// Search a Data holder by name in controller Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: path
// Path to search for (dot separated)
//
ZWEXPORT ZDataHolder zway_find_controller_data(const ZWay zway, const char *path);

// Search a Data holder by name in device Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: device_id
// Node Id
//
// @param: path
// Path to search for (dot separated)
//
ZWEXPORT ZDataHolder zway_find_device_data(const ZWay zway, ZWBYTE device_id, const char *path);

// Search a Data holder by name in instance Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: device_id
// Node Id
//
// @param: instance_id
// Instance Id
//
// @param: path
// Path to search for (dot separated)
//
ZWEXPORT ZDataHolder zway_find_device_instance_data(const ZWay zway, ZWBYTE device_id, ZWBYTE instance_id, const char *path);

// Search a Data holder by name in Command Class Data holder
//
// @param: zway
// Z-Way object instance
//
// @param: device_id
// Node Id
//
// @param: instance_id
// Instance Id
//
// @param: cc_id
// Command Class Id
//
// @param: path
// Path to search for (dot separated)
//
ZWEXPORT ZDataHolder zway_find_device_instance_cc_data(const ZWay zway, ZWBYTE device_id, ZWBYTE instance_id, ZWBYTE cc_id, const char *path);

// Checks if dataholder holds empty value
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
ZWEXPORT ZWBOOL zway_data_is_empty(const ZWay zway, ZDataHolder data);

// Returns DataHolder path starting from it's root
// Important: Should be freed by caller !!
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
ZWEXPORT char *zway_data_get_path(const ZWay zway, const ZDataHolder data);

// Returns DataHolder local name
// Important: Returns a real value, not a copy. SHOULD NOT be freed !!
//
// @param: zway
// Z-Way object instance
// 
// @param: data
// Data object instance
//
ZWEXPORT const char *zway_data_get_name(const ZWay zway, const ZDataHolder data);

// Returns DataHolder first child embedded in iterator structure
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
ZWEXPORT ZDataIterator zway_data_first_child(const ZWay zway, const ZDataHolder data);

// Returns DataHolder next child embedded in iterator structure
//
// @param: child
// Previous child Data object instance
//
ZWEXPORT ZDataIterator zway_data_next_child(ZDataIterator child);

// Removes child DataHolder from the parent
// Note that child is freed, so it is no longer valid after this call
//
// @param: zway
// Z-Way object instance
//
// @param: data
// Data object instance
//
// @param: child
// Immediate child data object instance
//
ZWEXPORT ZWError zway_data_remove_child(ZWay zway, ZDataHolder data, ZDataHolder child);

// Returns DataHolder update time
// 
// @param data
// Data object instance
//
ZWEXPORT time_t zway_data_get_update_time(const ZDataHolder data);

// Returns DataHolder invalidate time
// 
// @param data
// Data object instance
//
ZWEXPORT time_t zway_data_get_invalidate_time(const ZDataHolder data);

// Acquires/releases data lock for several sequential operations with DataHolder(s)
//
// @param zway
// Z-Way object instance
//
ZWEXPORT void zway_data_acquire_lock(ZWay zway);
ZWEXPORT void zway_data_release_lock(ZWay zway);

#endif
