/*
 *  This file defines support for "toll-free briging" between Python wrappers
 *  for CoreFoundation objecs and native Objective-C objects.
 */
#include "pyobjc.h"

#include <unistd.h>

#include "objc/objc.h"

#ifdef MACOSX
#import <Foundation/NSURL.h>

#include "pymactoolbox.h"

id 
PyObjC_CFTypeToID(PyObject* argument)
{
	/* Tollfree bridging of CF (some) objects 
	 * This list of conversion functions is the complete list of such
	 * functions in Python 2.3b1
	 */
	int r;
	id  val;


	r = CFObj_Convert(argument, (CFTypeRef*)&val);
	if (r) return val;
	PyErr_Clear();
	return NULL;
}

/* 
 * NOTE: CFObj_New creates a CF wrapper for any CF object, however we have
 * better information for at least some types: it is impossible to see the
 * difference between mutable and immutable collection types using only 
 * the CF API.
 */
PyObject* 
PyObjC_IDToCFType(id argument)
{
	[argument retain];

	if ([argument isKindOfClass:[NSMutableString class]]) {
		return CFMutableStringRefObj_New((CFMutableStringRef)argument);
	}

	if ([argument isKindOfClass:[NSString class]]) {
		return CFStringRefObj_New((CFStringRef)argument);
	}
	
	if ([argument isKindOfClass:[NSMutableArray class]]) {
		return CFMutableArrayRefObj_New((CFMutableArrayRef)argument);
	}

	if ([argument isKindOfClass:[NSArray class]]) {
		return CFArrayRefObj_New((CFArrayRef)argument);
	}

	if ([argument isKindOfClass:[NSDictionary class]]) {
		return CFDictionaryRefObj_New((CFDictionaryRef)argument);
	}

	if ([argument isKindOfClass:[NSMutableDictionary class]]) {
		return CFMutableDictionaryRefObj_New(
				(CFMutableDictionaryRef)argument);
	}

	if ([argument isKindOfClass:[NSURL class]]) {
		return CFURLRefObj_New((CFURLRef)argument);
	}

	/* Apple doesn't say as much, but as NSArray and CFArray are toll-free
	 * bridged NSObject must also be toll-free bridged to a CoreFoundation
	 * type.  As is is not document which type this is, we use the most
	 * generic one.
	 */
	return CFObj_New((CFTypeRef)argument);
}


#else

static const char dummy; /* Make sure this is valid C on GNUstep */

#endif /* MACOSX */