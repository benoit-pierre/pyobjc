/*
 * Some utility functions...
 *
 * TODO: Documentation
 */

#include "pyobjc.h"

#import <Foundation/Foundation.h>

PyObject* PyObjCExc_Error;
PyObject* PyObjCExc_NoSuchClassError;
PyObject* PyObjCExc_InternalError;
PyObject* PyObjCExc_NoProtocol;
PyObject* PyObjCExc_UnInitDeallocWarning;

int 
PyObjCUtil_Init(PyObject* module)
{
#define NEW_EXC(identifier, name, base_class) \
	identifier = PyErr_NewException("objc."name, base_class, NULL); \
	if (identifier == NULL) return -1; \
	Py_INCREF(identifier); \
	if (PyModule_AddObject(module, name, identifier) < 0) return -1;

	NEW_EXC(PyObjCExc_Error, "error", NULL);
	NEW_EXC(PyObjCExc_NoSuchClassError, "nosuchclass_error", PyObjCExc_Error);
	NEW_EXC(PyObjCExc_InternalError, "internal_error", PyObjCExc_Error);
	NEW_EXC(PyObjCExc_NoProtocol, "ProtocolError", PyObjCExc_Error);
	NEW_EXC(PyObjCExc_UnInitDeallocWarning, "UninitializedDeallocWarning", PyExc_Warning);

	return 0;
}

static PyObject* 
ObjCErr_PyExcForName(const char* value)
{
	/* TODO: This table should be changeable from python */
	if (strcmp(value, "NSRangeException") == 0) {
		return PyExc_IndexError;
	}  else if (strcmp(value, "NSInvalidArgumentException") == 0) {
		return PyExc_ValueError;
	}  else if (strcmp(value, "NSMallocException") == 0) {
		return PyExc_MemoryError;
	}  else if (strcmp(value, "NSUnknownKeyException") == 0) {
		return PyExc_KeyError;
	}

	return PyObjCExc_Error;
}
	

void 
PyObjCErr_FromObjC(NSException* localException)
{
	NSDictionary* userInfo;
	PyObject*     dict;
	PyObject*     exception;
	PyObject*     v;
	PyObject*	  buf;
	PyObject*     exc_type;
	PyObject*     exc_value;
	PyObject*     exc_traceback;
	const char    *c_localException_name;
	const char    *c_localException_reason;

	PyGILState_STATE state;

	/* Use -UTF8String instead, or even: create PyObjCUnicode object */
	c_localException_name = [[localException name] cString];
	c_localException_reason = [[localException reason] cString];

	exception = ObjCErr_PyExcForName(c_localException_name);

	userInfo = [localException userInfo];
	if (userInfo) {
		id val;

		val = [userInfo objectForKey:@"__pyobjc_exc_type__"];
		if (val) {
			state = PyGILState_Ensure();
			exc_type = [val pyObject];
			exc_value = [[userInfo objectForKey:@"__pyobjc_exc_value__"]  pyObject];
			exc_traceback = [[userInfo objectForKey:@"__pyobjc_exc_traceback__"]  pyObject];

			/* -pyObject returns a borrowed reference and 
			 * PyErr_Restore steals one from us.
			 */
			Py_INCREF(exc_type);
			Py_XINCREF(exc_value);
			Py_XINCREF(exc_traceback);

			PyErr_Restore(exc_type, exc_value, exc_traceback);
			PyGILState_Release(state);
			return;
		}
	}

	state = PyGILState_Ensure();
	dict = PyDict_New();
	v = PyString_FromString(c_localException_name);
	PyDict_SetItemString(dict, "name", v);
	Py_DECREF(v);
	v = PyString_FromString(c_localException_reason);
	PyDict_SetItemString(dict, "reason",  v);
	Py_DECREF(v);
	if (userInfo) {
		v = PyObjCObject_New(userInfo);
		if (v != NULL) {
			PyDict_SetItemString(dict, "userInfo", v);
			Py_DECREF(v);
		} else { 
			PyErr_Clear();
		}
	} else {
		PyDict_SetItemString(dict, "userInfo", Py_None);
	}

	
	buf = PyString_FromFormat("%s - %s", 
		c_localException_name,
		c_localException_reason);

	PyErr_SetObject(exception, buf);
	PyErr_Fetch(&exc_type, &exc_value, &exc_traceback);
	if (!exc_value || !PyObject_IsInstance(exc_value, exc_type)) {
		PyErr_NormalizeException(&exc_type, &exc_value, &exc_traceback);
	}
	PyObject_SetAttrString(exc_value, "_pyobjc_info_", dict);
	Py_DECREF(dict); dict = NULL;
	v = PyString_FromString(c_localException_name);
	PyObject_SetAttrString(exc_value, "name", v);
	Py_DECREF(v); v = NULL;
	PyErr_Restore(exc_type, exc_value, exc_traceback);
	PyGILState_Release(state);
}

void 
PyObjCErr_ToObjC(void)
{
	PyObjCErr_ToObjCWithGILState(NULL);
}


NSException* 
PyObjCErr_AsExc(void)
{
	PyObject* exc_type;
	PyObject* exc_value;
	PyObject* exc_traceback;
	PyObject* args;
	PyObject* repr;
	PyObject* typerepr;
	NSException* val;
	NSMutableDictionary* userInfo;

	PyErr_Fetch(&exc_type, &exc_value, &exc_traceback);
	if (exc_type == NULL) {
		return nil;
	}

	PyErr_NormalizeException(&exc_type, &exc_value, &exc_traceback);

	args = PyObject_GetAttrString(exc_value, "_pyobjc_info_");
	if (args == NULL) {
		PyErr_Clear();
	} else {
		/* This may be an exception that started out in 
		 * Objective-C code.
		 */
		PyObject* v;
		char* reason = NULL;
		char* name = NULL;

		v = PyDict_GetItemString(args, "reason"); 
		if (v && PyString_Check(v)) {
			reason = PyString_AsString(v);
		} else {
			PyErr_Clear();
		}

		v = PyDict_GetItemString(args, "name"); 
		if (v && PyString_Check(v)) {
			name = PyString_AsString(v);
		} else {
			PyErr_Clear();
		}

		v = PyDict_GetItemString(args, "userInfo");
		if (v && PyObjCObject_Check(v)) {
			userInfo = PyObjCObject_GetObject(v);
		} else {
			userInfo = nil;
			PyErr_Clear();
		}

		if (name && reason) {
			id oc_name;
			id oc_reason;

			oc_name = [NSString stringWithCString:name];
			oc_reason = [NSString stringWithCString:reason];
			val = [NSException exceptionWithName:oc_name
				     reason:oc_reason
				     userInfo:userInfo];
			Py_DECREF(args);
			Py_XDECREF(exc_type);
			Py_XDECREF(exc_value);
			Py_XDECREF(exc_traceback);
			
			return val;
		}
	}

	repr = PyObject_Str(exc_value);
	typerepr = PyObject_Str(exc_type);
	userInfo = [NSMutableDictionary dictionaryWithCapacity: 3];
	[userInfo setObject:
		[OC_PythonObject newWithObject:exc_type]
		forKey:@"__pyobjc_exc_type__"];
	if (exc_value != NULL)
		[userInfo setObject:
			[OC_PythonObject newWithObject:exc_value]
			forKey:@"__pyobjc_exc_value__"];
	if (exc_traceback != NULL)
		[userInfo setObject:
			[OC_PythonObject newWithObject:exc_traceback]
			forKey:@"__pyobjc_exc_traceback__"];

	val = [NSException 
		exceptionWithName:@"OC_PythonException"
		reason:[NSString stringWithFormat:@"%s: %s", PyString_AS_STRING(typerepr), PyString_AS_STRING(repr)]
		userInfo:userInfo];

	Py_DECREF(typerepr);
	Py_DECREF(repr);

	if (PyObjC_VerboseLevel) {
		PyErr_Restore(exc_type, exc_value , exc_traceback);
		NSLog(@"PyObjC: Converting exception to Objective-C:");
		PyErr_Print();
	} else {
		Py_DECREF(exc_type);
		Py_XDECREF(exc_value);
		Py_XDECREF(exc_traceback);
	}
	return val;
}

void 
PyObjCErr_ToObjCWithGILState(PyGILState_STATE* state)
{
	NSException* exc = PyObjCErr_AsExc();

	if (state) {
		PyGILState_Release(*state);
	}
	[exc raise];
}


char* 
PyObjCUtil_Strdup(const char* value)
{
	int len;
	char* result;

	len = strlen(value);
	result = PyMem_Malloc(len+1);
	if (result == NULL) return NULL;

	memcpy(result, value, len);
	result[len] = 0;
	return result;
}


NSMapTableKeyCallBacks PyObjCUtil_PointerKeyCallBacks = {
	NULL,
	NULL,
	NULL,
	NULL,
	NULL,
	NULL,
};

NSMapTableValueCallBacks PyObjCUtil_PointerValueCallBacks = {
	NULL,
	NULL,
	NULL,
};

#define SHOULD_FREE 0
#define SHOULD_IGNORE 1

void
PyObjC_FreeCArray(int code, void* array)
{
	if (code == SHOULD_FREE) {
		PyMem_Free(array);
	}
}

static PyTypeObject* array_type = NULL;

static inline PyTypeObject* 
fetch_array_type(void)
{
	PyObject* mod;
	PyObject* name;

	if (array_type != NULL) return array_type;

	name = PyString_FromString("array");
	if (name == NULL) {
		return NULL;
	}

	mod = PyImport_Import(name);
	Py_DECREF(name);
	if (mod == NULL) {
		return NULL;
	}

	array_type = (PyTypeObject*)PyObject_GetAttrString(mod, "ArrayType");
	Py_DECREF(mod);
	if (array_type == NULL) {
		return NULL;
	}

	/* XXX: check if array_type is realy a type! */

	return array_type;
}

#define array_check(obj) PyObject_TypeCheck(obj, fetch_array_type())

static char 
array_typestr(PyObject* array)
{
	PyObject* typecode;
	char res;

	typecode = PyObject_GetAttrString(array, "typecode");
	if (typecode == NULL) {
		return '\0';
	}

	if (!PyString_Check(typecode)) {
		PyErr_SetString(PyExc_TypeError, "typecode not a string");
		return '\0';
	}

	switch (*PyString_AS_STRING(typecode)) {
	case 'c': res = _C_CHR; break;
	case 'b': res = _C_CHR; break;
	case 'B': res = _C_UCHR; break;
	case 'u': res = _C_SHT; break;
	case 'h': res = _C_SHT; break;
	case 'H': res = _C_USHT; break;
	case 'i': res = _C_INT; break;
	case 'I': res = _C_UINT; break;
	case 'l': res = _C_LNG; break;
	case 'L': res = _C_ULNG; break;
	case 'f': res = _C_FLT; break;
	case 'd': res = _C_DBL; break;
	default: 
		PyErr_SetString(PyExc_TypeError, "unsupported typecode");
		res = '\0';
	}
	Py_DECREF(typecode);
	
	return res;
}

static int 
buffer_get(PyObject* obj, void** bufptr, int* sizeptr)
{
	int r;

	r = PyArg_Parse(obj, "s#", bufptr, sizeptr);
	if (!r) {
		return -1;
	}

	return 0;
}

static char struct_elem_code(const char* typestr);

static char
array_elem_code(const char* typestr)
{
	char res = '\0';
	char tmp;

	if (*typestr++ != _C_ARY_B) {
		return '\0';
	}
	while (isdigit(*typestr)) typestr++;

	if (*typestr == _C_ARY_E) {
		return '\0';
	}

	while (typestr && *typestr != _C_ARY_E) {
		switch(*typestr) {
		case _C_ARY_B:
			tmp = array_elem_code(typestr);
			if (tmp == '\0') return '\0';
			if (res == '\0') {
				res = tmp;
			} else if (tmp != res) {
				return '\0';
			}
			break;
		case _C_STRUCT_B:
			tmp = struct_elem_code(typestr);
			if (tmp == '\0') return '\0';
			if (res == '\0') {
				res = tmp;
			} else if (tmp != res) {
				return '\0';
			}
			break;
		default:
			if (res != '\0' && *typestr != res) return '\0';
			res = *typestr;
		}

		typestr = PyObjCRT_SkipTypeSpec(typestr);
	}
	return res;
}

static char
struct_elem_code(const char* typestr)
{
	char res = '\0';
	char tmp;

	if (*typestr++ != _C_STRUCT_B) {
		return '\0';
	}

	while (*typestr != '=' && *typestr != _C_STRUCT_E) {
		typestr++;
	}

	if (*typestr == _C_STRUCT_E) {
		return '\0';
	}
	typestr++;

	while (typestr && *typestr != _C_STRUCT_E) {
		switch(*typestr) {
		case _C_ARY_B:
			tmp = array_elem_code(typestr);
			if (tmp == '\0') return '\0';
			if (res == '\0') {
				res = tmp;
			} else if (tmp != res) {
				return '\0';
			}
			break;
		case _C_STRUCT_B:
			tmp = struct_elem_code(typestr);
			if (tmp == '\0') return '\0';
			if (res == '\0') {
				res = tmp;
			} else if (tmp != res) {
				return '\0';
			}
			break;
		default:
			if (res != '\0' && *typestr != res) return '\0';
			res = *typestr;
		}

		typestr = PyObjCRT_SkipTypeSpec(typestr);
	}
	return res;
}
		

/*
 * Convert a Python object to an array of 'elementType'. The array should
 * contain 'pythonCount' elements, Py_None or NULL is accepted and will result
 * in converting the entire Python sequence.
 *
 * The pythonList should either be a python sequence with appropriate entries,
 * an array.array whose element-types match the element-types of the 
 * 'elementType' or an appropriatly typed and shaped numeric array.
 * 
 * XXX: Numeric arrays are not yet supported.
 */
int	
PyObjC_PythonToCArray(
	const char* elementType,
	PyObject* pythonList,
	PyObject* pythonCount,
	void** array,
	int*   size)
{
	int eltsize = PyObjCRT_SizeOfType(elementType);
	int eltcount;
	int i, r;

	if (eltsize == -1) {
		return -1;
	}

	if (eltsize == 1) {
		/* A simple byte-array */
		char* buf;
		int bufsize;

		if (buffer_get(pythonList, (void**)&buf, &bufsize) == -1) {
			return -1;
		}
		if (pythonCount == Py_None || pythonCount == NULL) {
			*array = buf;
			*size = bufsize;
		} else {
			r = depythonify_c_value(@encode(int), pythonCount, &eltcount);
			if (r == -1) {
				return -1;
			}
			if (eltcount > bufsize) {
				PyErr_Format(PyExc_ValueError,
					"Requesting buffer of %d, have buffer "
					"of %d", eltcount, bufsize);
				return -1;
			}
			*array = buf;
			*size = eltcount;
		}
		return SHOULD_IGNORE;
	} 

	/* A more complex array */

	if (array_check(pythonList)) {
		/* An array.array. Only convert if the typestr describes an
		 * simple type of the same type as the array, or a struct/array
		 * containing only elements of the type of the array.
		 */
		char* buf;
		int bufsize;
		char code = array_typestr(pythonList);
		if (code == *elementType) {
			/* Simple array, ok */
		} else if (*elementType == _C_ARY_B) {
			/* Array of arrays, 'code' must be the same as the
			 * element-type of the array.
			 */
			if (code != array_elem_code(elementType)) {
				PyErr_Format(PyExc_ValueError, 
					"type mismatch between array.array "
					"of %c and and C array of %s",
					code, elementType);
				return -1;
			}

		} else if (*elementType == _C_STRUCT_B) {
			/* Array of structs, 'code' must be the same as the
			 * the field-types of the structs (that is, the struct
			 * must contain one or more fields of type 'code').
			 */
			if (code != struct_elem_code(elementType)) {
				PyErr_Format(PyExc_ValueError, 
					"type mismatch between array.array "
					"of %c and and C array of %s",
					code, elementType);
				return -1;
			}
		} else {
			PyErr_Format(PyExc_ValueError, 
				"type mismatch between array.array "
				"of %c and and C array of %s",
				code, elementType);
			return -1;
		}

		if (buffer_get(pythonList, (void**)&buf, &bufsize) == -1) {
			return -1;
		}
		if ((bufsize % eltsize) != 0) {
			PyErr_SetString(PyExc_ValueError, 
					"Badly shaped array.array");
			return -1;
		}

		*array = buf;

		if (pythonCount == Py_None || pythonCount == NULL) {
			*size = bufsize / eltsize;
		} else {

			r = depythonify_c_value(@encode(int), pythonCount, &eltcount);
			if (r == -1) {
				return -1;
			}
			bufsize /= eltsize;

			if (eltcount > bufsize) {
				PyErr_Format(PyExc_ValueError,
					"Requesting buffer of %d, have buffer "
					"of %d", eltcount, bufsize);
				return -1;
			}
			*array = buf;
			*size = eltcount;
		}
		return SHOULD_IGNORE;

#ifdef PyObjC_ENABLE_NUMARRAY

# error "Please implement Numarray/Numeric support"

	} else if (...){
		/* TODO: Convert the numeric array (or return a pointer to it's
		 * data), but only if it is the right type:
		 * - If typestr is a basic type, the array must be a 1D array
		 *   of that type
		 * - If typestr is a structred type, the array must be a 2D
		 *   array where rows match the structured type
		 *
		 * XXX: I have no idea if this is feasable, not having used
		 * numarray/numeric myself.
		 */
#endif /* PyObjC_ENABLE_NUMARRAY */
	} else {
		int seqlen;
		int pycount;
		PyObject* seq = PySequence_Fast(pythonList, 
					"converting to a C array");
		if (seq == NULL) {
			return -1;
		}

		seqlen = PySequence_Fast_GET_SIZE(seq);
		if (pythonCount == Py_None || pythonCount == NULL) {
			pycount = seqlen;
		} else {
			r = depythonify_c_value(
					@encode(int), pythonCount, &pycount);
			if (r == -1) {
				Py_DECREF(seq);
				return -1;
			}
		}

		if (seqlen < pycount) {
			Py_DECREF(seq);
			PyErr_Format(PyExc_ValueError,
					"too few values (%d) expecting at "
					"least %d", seqlen, pycount);
			return -1;
		}
		*array = PyMem_Malloc(eltsize * pycount);
		if (*array == NULL) {
			Py_DECREF(seq);
			PyErr_NoMemory();
			return -1;
		}
		*size = pycount;

		for (i = 0; i < pycount; i++) {
			PyObject* item = PySequence_Fast_GET_ITEM(seq, i);

			r = depythonify_c_value(elementType, item,
					((char*)*array)+(i*eltsize));
			if (r == -1) {
				Py_DECREF(seq);
				PyMem_Free(*array); *array = NULL;
				return -1;
			}
		}
		return SHOULD_FREE;
	}
	
}

PyObject* 
PyObjC_CArrayToPython(
	const char* elementType,
	void* array,
	int   size)
{
	PyObject* result;
	int i;
	int eltsize;

	result = PyTuple_New(size);
	if (result == NULL) {
		return NULL;
	}

	eltsize = PyObjCRT_SizeOfType(elementType);

	for (i = 0; i < size; i++) {
		PyObject* elt = pythonify_c_value(elementType, array);
		if (elt == NULL) {
			Py_DECREF(result);
			return NULL;
		}

		PyTuple_SET_ITEM(result, i, elt);
		array = ((char*)array) + eltsize;
	}

	return result;
}

int
PyObjC_IsPythonKeyword(const char* word)
{
	/*
	 * We cheat a little: this list only contains those keywords that
	 * are actually used in Cocoa.
	 *
	 * XXX: If we ever add the complete list here we should optimize
	 * this function.
	 */
	static const char* keywords[] = {
		"class",
		"raise",
		NULL
	};
	const char** cur;

	for (cur = keywords; *cur != NULL; cur++) {
		if (strcmp(word, *cur) == 0) {
			return 1;
		}
	}
	return 0;
}

int
PyObjCRT_SimplifySignature(char* signature, char* buf, size_t buflen)
{
	char* cur;
	char* end;
	char* next;

	cur = signature;
	*buf = '\0';

	while (*cur != '\0') {
		next = end = (char*)PyObjCRT_SkipTypeSpec(cur);
		end -= 1;
		while (end != cur && isdigit(*end)) {
			end --;
		}
		end++;

		if ((size_t)(end - cur) > buflen) {
			return -1;
		}

		memcpy(buf, cur, end-cur);
		buflen -= (end-cur);
		buf += (end-cur);
		*buf = '\0';
		cur = next;
	}
	return 0;
}

int
PyObjCObject_Convert(PyObject* object, void* pvar)
{
	int r;
	r = depythonify_c_value(@encode(id), object, (id*)pvar);
	if (r == -1) {
		return 0;
	} else {
		return 1;
	}
}

int 
PyObjC_ConvertBOOL(PyObject* object, void* pvar)
{
    BOOL* pbool = (BOOL*)pvar;

    if (PyObject_IsTrue(object)) {
        *pbool = YES;
    } else {
        *pbool = NO;
    }

    return 1;
}

int 
PyObjC_ConvertChar(PyObject* object, void* pvar)
{
    char* pchar = (char*)pvar;

    if (!PyString_Check(object)) {
        PyErr_SetString(PyExc_TypeError, "Expecting string of len 1");
        return 0;
    }

    if (PyString_Size(object) != 1) {
        PyErr_SetString(PyExc_TypeError, "Expecting string of len 1");
        return 0;
    }

    *pchar = *PyString_AsString(object);
    return 1;
}

int 
PyObjCSelector_Convert(PyObject* object, void* pvar)
{ 
    int r;

    if (object == Py_None) {
        *(SEL*)pvar = NULL;
        return 1;
    }
    if (PyObjCSelector_Check(object)) {
        *(SEL*)pvar = PyObjCSelector_GetSelector(object);
        return 1;
    }
    if (!PyString_Check(object)) {
        PyErr_SetString(PyExc_TypeError, "Expected string");
        return 0;
    }

    r = depythonify_c_value(@encode(SEL), object, pvar);
    if (r == -1) {
           return 0;
    }   
    return 1;
}

int 
PyObjCClass_Convert(PyObject* object, void* pvar)
{
    if (!PyObjCClass_Check(object)) {
        PyErr_SetString(PyExc_TypeError, "Expected objective-C class");
        return 0;
    }

    *(Class*)pvar = PyObjCClass_GetClass(object);
    if (*(Class*)pvar == NULL) return 0;
    return 1;
}

