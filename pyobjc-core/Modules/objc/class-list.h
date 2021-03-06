#ifndef PyObjC_CLASS_LIST_H
#define PyObjC_CLASS_LIST_H

/*!
 * PYOBJC_EXPECTED_CLASS_COUNT: Hint about the number of classes to expect
 *
 * Loading Quartz results close to 5K classes on OSX 10.8
 */
#define PYOBJC_EXPECTED_CLASS_COUNT 10000

/*!
 * @header class-list.h
 * @abstract Get the list of classes in the Objective-C runtime
 * @discussion
 *    This module defines a function that returns the list of classes
 *    in the Objetive-C runtime (converted to Python)
 */

Py_ssize_t PyObjC_ClassCount(void);


/*!
 * @function PyObjC_GetClassList
 * @result Returns a list of Objective-C classes
 * @discussion
 *     This function returns a list containing the wrappers for all classes
 *     in the Objective-C runtime.
 */
PyObject* PyObjC_GetClassList(void);

#endif /* PyObjC_CLASS_LIST_H */
