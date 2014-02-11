//
//  ZPlatform.h
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

#ifndef zway_platform_h
#define zway_platform_h

#include <stdio.h>
#include <stdlib.h>
#include <stdarg.h>
#include <math.h>

#ifdef _WINDOWS

#include <Windows.h>
#include <shlwapi.h>
#include <io.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <WinSock.h>
#include <stdint.h>
#include <time.h>
#include <tchar.h>

// suppress warnings about deprecated POSIX function names
#pragma warning(disable: 4996)

typedef BYTE ZWBYTE;
typedef unsigned char ZWBOOL;

typedef TCHAR ZWCHAR;
typedef LPTSTR ZWSTR;
typedef LPCTSTR ZWCSTR;

typedef HANDLE ZWHANDLE;

#define copy_str(s) _tcsdup(s)
#define compare_str(s1, s2) _tcscmp(s1, s2)
#define compare_str_ci(s1, s2) _tcsicmp(s1, s2)
#define str_length(s) _tcslen(s)
#define strcasecmp(s1, s2) _stricmp(s1, s2)
#define strncasecmp(s1, s2, maxCount) _strnicmp(s1, s2, maxCount)


#define ZSTR(s) TEXT(s)

typedef HANDLE ZWTHREAD;
typedef CRITICAL_SECTION ZWMUTEX;
typedef DWORD ZWMUTEXATTR;

#define current_thread() GetCurrentThread()

// implementation of UNIX-only functions:

int asprintf(char **buffer, const char *format, ...);
int vasprintf(char **buffer, const char *format, va_list args);
#define snprintf(buffer, size, format, ...) _snprintf(buffer, size, format, __VA_ARGS__)
float roundf(float num);
double round(double num);

void flockfile(FILE *filehandle);
void funlockfile(FILE *filehandle);

#define sleep(sec) Sleep((sec) * 1000)
#define sleep_ms(ms) Sleep(ms)

struct timezone 
{
	int tz_minuteswest; /* minutes W of Greenwich */
	int tz_dsttime;     /* type of dst correction */
};

int gettimeofday(struct timeval *tv, struct timezone *tz);

#define O_NOCTTY 0

#else

#if defined(__linux__) && (defined(__i386__) || defined(__x86_64__))
#define __sd_linux_use_directIO__ 1
#include <sys/io.h>
#endif

#include <fcntl.h>
#include <sys/socket.h>
#include <netdb.h>
#include <unistd.h>
#ifndef __USE_UNIX98
  #define __USE_UNIX98 // to workaround PTHREAD_MUTEX_RECURSIVE and PTHREAD_MUTEX_RECURSIVE_NP
#endif
#include <pthread.h>
#include <sys/errno.h>
#include <sys/stat.h>
#include <sys/time.h>
#include <termios.h>
#include <memory.h>

#define SOCKET_ERROR -1
#define closesocket(x) close(x)
#define _sleep(x) usleep((x) * 1000)

#ifndef O_BINARY
#define O_BINARY 0
#endif

typedef unsigned char ZWBYTE;
typedef unsigned char ZWBOOL;

typedef char ZWCHAR;
typedef char* ZWSTR;
typedef const char* ZWCSTR;

typedef int ZWHANDLE;
typedef int SOCKET;

#define copy_str(s) strdup(s)
#define compare_str(s1, s2) strcmp(s1, s2)
#define compare_str_ci(s1, s2) stricmp(s1, s2)
#define str_length(s) strlen(s)

#define ZSTR(s) s

typedef pthread_t ZWTHREAD;
typedef pthread_mutex_t ZWMUTEX;
typedef pthread_mutexattr_t ZWMUTEXATTR;

#define current_thread() pthread_self()

#define TRUE 1
#define FALSE 0

#define sleep_ms(ms) do { \
		struct timeval wait = { 0, ms * 1000 }; \
		select(0, NULL, NULL, NULL, &wait); \
	} while(0)

#define MAX_PATH FILENAME_MAX

#endif

#define SET_BIT(bitmask, index) ((ZWBYTE*)(bitmask))[(index) / 8] |= (ZWBYTE)(1 << ((index) % 8))
#define CLR_BIT(bitmask, index) ((ZWBYTE*)(bitmask))[(index) / 8] &= (ZWBYTE)(~(1 << ((index) % 8)))
#define TEST_BIT(bitmask, index) (((ZWBYTE*)(bitmask))[(index) / 8] & (ZWBYTE)(1 << ((index) % 8)))

#ifdef __cplusplus
extern "C" {
#endif

ZWSTR sys_last_err_string();

void get_local_time(struct tm *hosttime, int *msec);

int remove_recursive(const char *dirname);

#ifdef __cplusplus
}
#endif
    
#endif
