<?
    $title = "An introduction to PyObjC";
    $cvs_author = '$Author: ronaldoussoren $';
    $cvs_date = '$Date: 2003/07/05 14:59:47 $';

    include "header.inc";
?>
<div class="document" id="an-introduction-to-pyobjc">
<!-- :authors: Ronald Oussoren, Bob Ippolito
:contact: pyobjc-dev@lists.sourceforge.net
:URL: http://pyobjc.sourceforge.net/
:copyright: 2003-2005 The PyObjC Project -->
<div class="contents topic" id="contents">
<p class="topic-title first"><a name="contents">Contents</a></p>
<ul class="simple">
<li><a class="reference" href="#preface" id="id5" name="id5">Preface</a></li>
<li><a class="reference" href="#objective-c-for-pyobjc-users" id="id6" name="id6">Objective-C for PyObjC users</a></li>
<li><a class="reference" href="#overview-of-the-bridge" id="id7" name="id7">Overview of the bridge</a><ul>
<li><a class="reference" href="#classes" id="id8" name="id8">Classes</a></li>
<li><a class="reference" href="#messages-and-functions" id="id9" name="id9">Messages and Functions</a></li>
<li><a class="reference" href="#reference-counting" id="id10" name="id10">Reference counting</a></li>
<li><a class="reference" href="#informal-protocols" id="id11" name="id11">(Informal) protocols</a></li>
<li><a class="reference" href="#cocoa-bindings" id="id12" name="id12">Cocoa Bindings</a></li>
<li><a class="reference" href="#categories" id="id13" name="id13">Categories</a></li>
</ul>
</li>
<li><a class="reference" href="#cocoa-for-python-programmers" id="id14" name="id14">Cocoa for Python programmers</a></li>
<li><a class="reference" href="#notes-on-specific-tasks" id="id15" name="id15">Notes on specific tasks</a><ul>
<li><a class="reference" href="#working-with-threads" id="id16" name="id16">Working with threads</a></li>
<li><a class="reference" href="#finalizers" id="id17" name="id17">Finalizers</a></li>
</ul>
</li>
<li><a class="reference" href="#building-applications" id="id18" name="id18">Building applications</a><ul>
<li><a class="reference" href="#py2app-setup-py" id="id19" name="id19">&quot;py2app&quot; :  setup.py</a></li>
<li><a class="reference" href="#ide-approach-xcode" id="id20" name="id20">&quot;IDE approach&quot; : Xcode</a></li>
</ul>
</li>
</ul>
</div>
<div class="section" id="preface">
<h1><a class="toc-backref" href="#id5" name="preface">Preface</a></h1>
<p>PyObjC is a bridge between Python and Objective-C.  It allows you to write 
Python scripts that use and extend existing Objective-C class libraries, 
most importantly the <a class="reference" href="http://developer.apple.com/referencelibrary/API_Fundamentals/Cocoa-api-date.html">Cocoa libraries</a> by <a class="reference" href="http://www.apple.com/">Apple</a>.</p>
<p>This document describes how to use Objective-C class libraries from Python
scripts and how to interpret the documentation of those libraries from the 
point of view of a Python programmer.</p>
</div>
<div class="section" id="objective-c-for-pyobjc-users">
<h1><a class="toc-backref" href="#id6" name="objective-c-for-pyobjc-users">Objective-C for PyObjC users</a></h1>
<p>It is recommended that you take the time to understand a little bit about
Objective-C before jumping into PyObjC development.  The class libraries
that you will be using from Cocoa are not documented in Python, and their
documentation will be confusing without a grasp on the semantics and syntax
of Objective-C.</p>
<p>Objective-C is an object-oriented programming language implemented as a
superset of C that borrows heavily in concept and syntax from Smalltalk.
of C and borrows heavily from Smalltalk.  It features single inheritance with
dynamic dispatch and (in theory) multiple root classes.  This is basically the
same as Python with single inheritance.</p>
<p>An important difference between Python and Objective-C is that the latter is
not a pure object-oriented language.  Some values are not objects, but values
of plain C types, such as <tt class="literal"><span class="pre">int</span></tt> and <tt class="literal"><span class="pre">double</span></tt>.  These basic C types can 
be used as the types of arguments and the return value of methods.</p>
<p>Object allocation and initialization are explicit and separate actions in 
Objective-C. The former is done by the class-method <tt class="literal"><span class="pre">alloc</span></tt>, while the
latter is done by instance methods whose name customarily starts with <tt class="literal"><span class="pre">init</span></tt>.</p>
<p>Objective-C code looks just like plain C code, with some easily recognizable
Smalltalk-like extensions for the object-oriented parts of the language.  An
example class declaration (usually found in <tt class="literal"><span class="pre">.h</span></tt> files) and implementation
(usually found in <tt class="literal"><span class="pre">.m</span></tt> files) are listed below.  Class declarations are easily
recognized as blocks of code between <tt class="literal"><span class="pre">&#64;interface</span></tt> and <tt class="literal"><span class="pre">&#64;end</span></tt>, and similarly
the implementation is between <tt class="literal"><span class="pre">&#64;implementation</span></tt> and <tt class="literal"><span class="pre">&#64;end</span></tt>.  An expression
enclosed in brackets in Objective-C is called a message, and is the equivalent
to an instance method invocation in Python.  For example, this Objective-C code:</p>
<pre class="literal-block">
[aMutableArray addObject:&#64;&quot;constant string&quot;];
</pre>
<p>Is equivalent in intent to the following in Python:</p>
<pre class="literal-block">
aList.append(u&quot;constant string&quot;)
</pre>
<p>Objective-C messages have three components: a target, a selector, and zero or
more arguments.  The target, <tt class="literal"><span class="pre">aMutableArray</span></tt>, is the object or class
receiving the message.  The selector, <tt class="literal"><span class="pre">addObject:</span></tt> uniquely identifies the
kind of message that is being sent.  Finally, the arguments,
<tt class="literal"><span class="pre">&#64;&quot;constant</span> <span class="pre">string&quot;</span></tt> are used by the implementation of the method upon
receipt of the message.  The syntax of Objective-C message dispatch is
deceptively similar to keyword arguments in Python, but they are actually
quite different.  Objective-C messages can not have default arguments, and all
arguments are passed in a specific order.  The components of a selector may not
be reordered.  Syntactically, one argument must be interleaved at every colon in
the selector.  The message:</p>
<pre class="literal-block">
[anArray indexOfObject:someObject inRange:someRange]
</pre>
<dl>
<dt>Target:</dt>
<dd><tt class="literal"><span class="pre">anArray</span></tt></dd>
<dt>Selector:</dt>
<dd><tt class="literal"><span class="pre">indexOfObject:inRange:</span></tt></dd>
<dt>Arguments:</dt>
<dd><tt class="literal"><span class="pre">someObject</span></tt>, <tt class="literal"><span class="pre">someRange</span></tt></dd>
</dl>
<p>As you'll see later, the straightforward translation of such a message to
Python is:</p>
<pre class="literal-block">
anArray.indexOfObject_inRange_(someObject, someRange)
</pre>
<p>This may be awkward and &quot;unpythonic&quot; at first, however this syntax is necessary
to preserve the semantics of Objective-C message dispatch.</p>
<p>A class declaration:</p>
<pre class="literal-block">
&#64;interface MyClass : MySuperClass
{
    id  anInstanceVariable;
    int anotherInstanceVariable;
}

// A class method that returns an initialized instance of this class.
// Similar to an implementation of __call__ on the metaclass.
+instanceWithObject:(id)anObject andInteger:(int)anInteger;

// An instance method, the designated initializer for MyClass.
// Similar to an implementation of __new__ on MyClass.
-initWithObject:(id)anObjct andInteger:(int)anInteger;

// An accessor, instance variables (attributes) are in a separate
// namespace and are considered &quot;private&quot; in Objective-C.  Conventionally,
// there is nothing similar to this in Python.
-(int)anotherInstanceVariable;
&#64;end
</pre>
<p>A class implementation:</p>
<pre class="literal-block">
&#64;implementation MyClass

// Note that a type is not declared for the return value.  Undeclared types
// are assumed to be &quot;id&quot;, which means any kind of instance.
+instanceWithObject:(id)anObject andInteger:(int)anInteger
{
    // 1. Create an instance of MyClass.
    // 2. Initialize it with its designated initializer
    //    &quot;initWithObject:andInteger:&quot;.
    // 3. Autorelease it, so that it does not leak memory.
    // 4. Return the new instance.
    //
    // NOTE:
    //   By convention,initializers (such as +new, -init, -copy)
    //   are the only methods that should return retained objects.
    //
    // NOTE:
    //   Since this is a class method, &quot;self&quot; refers to the class!
    //
    // Very roughly similar to:
    //   return self.__new__(anObject, anInteger)
    return [[[self alloc] initWithObject:anObject andInteger:anInteger] autorelease];
}

// Note that a type is not declared for the return value.  Undeclared types
// are assumed to be &quot;id&quot;, which means any kind of instance.
-initWithObject:(id)anObjct andInteger:(int)anInteger
{
    // Call the designated initializer of the superclass.
    // Similar to:
    //     self = super(MyClass, self).__new__()
    self = [super init];

    // Bail if initialization of the superclass failed.
    // Similar to:
    //     if self is None:
    //         return None
    if (!self) {
        return nil;
    }

    // Set the instance variable (attribute).  The argument must be
    // retained, since it will persist as long as the instance does.
    // Similar to:
    //     # Reference counting is automatic in Python
    //     self.anInstanceVariable = anObject
    anInstanceVariable = [anObject retain];

    // Set the other instance variable.  Note that since anInteger is
    // a primitive &quot;C&quot; type, not an object, no reference counting takes
    // place.
    // Similar to:
    //     # Everything is an object in Python
    //     self.anotherInstanceVariable = anInteger
    anotherInstanceVariable = anInteger;

    // Like __new__ in Python, initializers in Objective-C must
    // explicitly return self.  Note that this is different from
    // __init__.
    // Similar to:
    //     return self
    return self;
}
    

// an accessor, instance variables (attributes) are in a separate
// namespace and are considered &quot;private&quot;
-(int)anotherInstanceVariable
{
    return anotherInstanceVariable;
}

// Since objects were retained as instance variables on this object,
// they must be freed when the object is.  This is similar to an
// implementation of __del__ in Python.  Since Objective-C has no
// cyclic garbage collection, this isn't discouraged like it is in
// Python.
-(void)dealloc
{
    // Very roughly similar to:
    //     del self.instanceVariable
    [instanceVariable release];

    // Very roughly similar to:
    //     super(MyClass, self).__del__()
    [super dealloc];
}

&#64;end
</pre>
<p>Objective-C also features exceptions, but they are typically only used for
disaster recovery, not error handling, so you will not encounter them very
often.  Read <a class="reference" href="http://developer.apple.com/documentation/Cocoa/Conceptual/ObjectiveC/index.html">The Objective-C Programming Language</a> if you want to
know more about exceptions in Objective-C.</p>
<p>One thing to keep in mind when translating Objective-C snippets to Python is
that any message can be sent to <tt class="literal"><span class="pre">nil</span></tt>, and the return value of that message
will be <tt class="literal"><span class="pre">nil</span></tt>.  PyObjC translates <tt class="literal"><span class="pre">nil</span></tt> to <tt class="literal"><span class="pre">None</span></tt> when crossing the
bridge, so any such attempt will raise an <tt class="literal"><span class="pre">AttributeError</span></tt>.</p>
<p>For more information about Objective-C see:</p>
<ul class="simple">
<li><a class="reference" href="http://developer.apple.com/documentation/Cocoa/Conceptual/ObjectiveC/index.html">The Objective-C Programming Language</a> at <a class="reference" href="http://www.apple.com/">Apple</a>.</li>
</ul>
</div>
<div class="section" id="overview-of-the-bridge">
<h1><a class="toc-backref" href="#id7" name="overview-of-the-bridge">Overview of the bridge</a></h1>
<div class="section" id="classes">
<h2><a class="toc-backref" href="#id8" name="classes">Classes</a></h2>
<p>Objective-C classes are visible as (new-style) Python classes and can be 
subclassed just like normal Python classes.  All the usual introspection
mechanisms work as well, as do <tt class="literal"><span class="pre">__slots__</span></tt> and descriptors.  The major 
differences between normal Python classes and Objective-C classes are the way 
that instances are created and initialized, and the fact that Objective-C
selectors look strange when translated to Python methods.</p>
<p>You can use multiple inheritance when subclassing an Objective-C class, so
long as the Objective-C class is the first base class and there is only one
Objective-C base class.  The Objective-C runtime does not support multiple
inheritance.  These mix-in classes should not contain different
implementations for Objective-C methods.  To achieve this behavior, Categories
should be used instead.</p>
<p>Another thing to keep in mind is that the names of Objective-C classes must
be globally unique per process, including across Python modules.  That is,
it is <em>not</em> possible to have two Python modules that define a class with the
same name.  It is conventional to choose class names with a short prefix that
uniquely identify your project or company.  For example, Apple uses <tt class="literal"><span class="pre">NS</span></tt>
as the prefix for all classes in the <a class="reference" href="http://developer.apple.com/referencelibrary/API_Fundamentals/Cocoa-api-date.html">Cocoa libraries</a>.  Note that the <tt class="literal"><span class="pre">NS</span></tt>
prefix made much more sense when it was called NeXTstep, but persists to this
day for compatibility reasons.</p>
<p>As described in <a class="reference" href="#objective-c-for-pyobjc-users">Objective-C for PyObjC users</a> the creation of Objective-C 
objects is a two-stage process.  To initialize objects, you first call a
class method to allocate the memory (typically <tt class="literal"><span class="pre">alloc</span></tt>), and then call an
initializer (typically starts with <tt class="literal"><span class="pre">init</span></tt>).  Some classes have class methods
which perform this behind the scenes, especially classes that create cached,
immutable, or singleton instances.</p>
</div>
<div class="section" id="messages-and-functions">
<h2><a class="toc-backref" href="#id9" name="messages-and-functions">Messages and Functions</a></h2>
<p>Objective-C methods are bridged to Python methods.  Because Objective-C
message dispatch syntax can not be translated directly to Python, a few
simple translations must take place.  The rules for these translations are:</p>
<ol class="arabic">
<li><p class="first">Replace all colons in the selector with underscores:</p>
<blockquote>
<ul class="simple">
<li><tt class="literal"><span class="pre">someMethod:withFoo:andBar:</span></tt> translates to <tt class="literal"><span class="pre">someMethod_withFoo_andBar_</span></tt></li>
</ul>
</blockquote>
</li>
<li><p class="first">If the result <tt class="literal"><span class="pre">class</span></tt> or <tt class="literal"><span class="pre">raise</span></tt> (Python keywords), append two underscores:</p>
<blockquote>
<ul class="simple">
<li><tt class="literal"><span class="pre">class</span></tt> translates to <tt class="literal"><span class="pre">class__</span></tt></li>
<li><tt class="literal"><span class="pre">raise</span></tt> translates to <tt class="literal"><span class="pre">raise__</span></tt></li>
</ul>
</blockquote>
</li>
<li><p class="first">Use this translated selector as you would a normal Python method.
The arguments must be passed in the same order, and the number of
arguments passed will normally be equal to the number of underscores
in the method name; exceptions to this rule and the behavior of &quot;result&quot;
are mentioned below.</p>
<blockquote>
<ul class="simple">
<li><tt class="literal"><span class="pre">result</span> <span class="pre">=</span> <span class="pre">[someObject</span> <span class="pre">someMethod:firstArg</span> <span class="pre">withFoo:foo</span> <span class="pre">andBar:bar];</span></tt>
translates to
<tt class="literal"><span class="pre">result</span> <span class="pre">=</span> <span class="pre">someObject.someMethod_withFoo_andBar_(firstArg,</span> <span class="pre">foo,</span> <span class="pre">bar)</span></tt></li>
</ul>
</blockquote>
</li>
</ol>
<p>Note that it is currently not possible to support methods with a variable
number of arguments from Python.  These selectors must be wrapped by
custom Objective-C code in order to be accessible by Python.</p>
<p>Wrapped/bridged methods (and functions) have the same number of arguments
as the corresponding Objective-C method or function, unless otherwise noted
in the documentation (<a class="reference" href="api-notes-macosx.html">Notes on supported APIs and classes on Mac OS X</a> for
Cocoa on Mac OS X).</p>
<p>Most methods or functions that take or return pointers to values will be an
exception to this rule if it is callable from Python at all.  In Objective-C
terminology, there are three kinds of pointers that can be used in a method:</p>
<dl>
<dt><tt class="literal"><span class="pre">in</span></tt>:</dt>
<dd>Used to pass data by reference to the function.  This is not a special
case from Python.</dd>
<dt><tt class="literal"><span class="pre">out</span></tt>:</dt>
<dd>Used to pass data from the function (e.g. an additional return value).
From Python, these values will be missing from the argument list (there
will be more underscores than arguments passed).  See below for notes on
how <tt class="literal"><span class="pre">out</span></tt> arguments change the return value.</dd>
<dt><tt class="literal"><span class="pre">inout</span></tt>:</dt>
<dd>A combination of in and out (a value is passed by reference, and mutated
upon return).  Unlike <tt class="literal"><span class="pre">out</span></tt>, these arguments remain in the argument list,
and thus do not have an effect on the number of arguments a method expects.
See below for notes on how <tt class="literal"><span class="pre">inout</span></tt> arguments change the return value.</dd>
</dl>
<p>In order to determine what the return value of such an exceptional message will
look like, you must &quot;make a list&quot; of the return values with the following rules:</p>
<ol class="arabic simple">
<li>If the return type of the method or function is not <tt class="literal"><span class="pre">void</span></tt>, add it to the
list.</li>
<li>For each argument in the method or function, add it to the list if it is
<tt class="literal"><span class="pre">out</span></tt> or <tt class="literal"><span class="pre">inout</span></tt>.</li>
</ol>
<p>After creating this list, you will have one of three cases:</p>
<dl>
<dt>Empty:</dt>
<dd>The return value of this call will always be <tt class="literal"><span class="pre">None</span></tt>.</dd>
<dt>One element:</dt>
<dd>The return value of this call will correspond to the one element of the list.</dd>
<dt>More than one element:</dt>
<dd>The return value of this call will be a tuple in the same order as the list.</dd>
</dl>
<p>The rules for pass by reference arguments may look quite complicated, but
it turns out this is very straightforward when working with them.</p>
<p>As an example of a method with two output arguments, <tt class="literal"><span class="pre">NSMatrix</span></tt> implements a
selector named <tt class="literal"><span class="pre">getNumberOfRows:columns:</span></tt> with the following signature:</p>
<pre class="literal-block">
(void)getNumberOfRows:(int *)rowCount columns:(int *)columnCount
</pre>
<p>You use this method in python like this:</p>
<pre class="literal-block">
rowCount, columnCount = matrix.getNumberOfRows_columns_()
</pre>
<p>When a function or method has an array of values and the length of that array
as arguments, you can pass <tt class="literal"><span class="pre">None</span></tt> as the length.  The length of the sequence
that is used for the array of values is passed to Objective-C as the length
argument.</p>
<p>Python's <tt class="literal"><span class="pre">array.array</span></tt> type may be used to represent a C array if the
typestr and size match what is expected by the selector.  Numeric, numarray,
and other third party array types are not supported in PyObjC 1.2.</p>
<p>When you define methods in a subclass of an Objective-C class, the bridge has
to tell the Objective-C runtime what the signature of those methods is.  The
basic rule is that all arguments as well as the return value are objects (just
like with normal Python methods).  The bridge will automatically pick a better 
signature when it has more information available.  Specifically, if you 
override an existing method the bridge will assume you want to use the same
method signature.  Furthermore, if you implement a method in an (informal)
protocol known to the bridge it will use the signature from the corresponding
method in that signature.</p>
<p>The end result is that you rarely have to add information about the
signature of methods.  For the two most common cases where this is necessary,
we have provided convenience decorators (used like <tt class="literal"><span class="pre">staticmethod</span></tt> or
<tt class="literal"><span class="pre">classmethod</span></tt>):</p>
<dl>
<dt><tt class="literal"><span class="pre">objc.accessor</span></tt>:</dt>
<dd>Use this to wrap a <a class="reference" href="http://developer.apple.com/documentation/Cocoa/Conceptual/KeyValueCoding/">Key-Value Coding</a> or <a class="reference" href="http://developer.apple.com/documentation/Cocoa/Conceptual/KeyValueObserving/">Key-Value Observing</a> compliant
accessor.</dd>
<dt><tt class="literal"><span class="pre">PyObjCTools.AppHelper.endSheetMethod</span></tt>:</dt>
<dd>Use this to wrap the implementation of a sheet's &quot;didEndSelector&quot; callback.</dd>
</dl>
<p>For complete control of the mapping to Objective-C you can use the function
<tt class="literal"><span class="pre">objc.selector</span></tt> to create custom descriptors. See the documentation of the
<tt class="literal"><span class="pre">objc</span></tt> module for the arguments you can use with this function. It is
normally used like this:</p>
<pre class="literal-block">
class MyObject (NSObject):
        def someMethod_(self, arg):
                pass

        someMethod_ = objc.selector(someMethod_, ...)
</pre>
</div>
<div class="section" id="reference-counting">
<h2><a class="toc-backref" href="#id10" name="reference-counting">Reference counting</a></h2>
<p>The <a class="reference" href="http://developer.apple.com/referencelibrary/API_Fundamentals/Cocoa-api-date.html">Cocoa libraries</a>, and most (if not all) other class libraries for 
Objective-C use explicit reference counting to manage memory. The methods
<tt class="literal"><span class="pre">retain</span></tt>, <tt class="literal"><span class="pre">release</span></tt> and <tt class="literal"><span class="pre">autorelease</span></tt> are used to manage these 
reference counts. You won't have to manage reference counts in Python, the
bridge does all that work for you (but see <a class="reference" href="api-notes-macosx.html">Notes on supported APIs and classes 
on Mac OS X</a> for some advanced issues).</p>
<p>The only reasons reference counts are mentioned at all are to tell you about
ignoring them, and more importantly to introduce you to some issues w.r.t. 
reference counting.</p>
<p>It turns out that Cocoa uses a primitive form of <a class="reference" href="http://www.python.org/doc/current/lib/module-weakref.html">weak references</a>. Those 
are not true <a class="reference" href="http://www.python.org/doc/current/lib/module-weakref.html">weak references</a> as in Python, but use-cases where an object 
stores a reference to another object without increasing the reference count
for that other object. The bridge cannot solve the issues this introduces
for you, which means that you get hard crashes when you're not careful when
dealing with those <a class="reference" href="http://www.python.org/doc/current/lib/module-weakref.html">weak references</a>.</p>
<p>The basic rule to deal with weak references is: make sure objects stays
alive as long as someone might have a weak reference to them. Due to the way
the bridge works, this means that you must make sure that you don't create
weak references from Objective-C to a plain Python object. The Python
object stays alive, but the proxy object as seen by the Objective-C code is
actually an autoreleased object that will be cleaned up unless the Objective-C
code increases its reference count.</p>
<p>The document <a class="reference" href="api-notes-macosx.html">Notes on supported APIs and classes on Mac OS X</a> contains 
information about classes that work with weak references. The most important
are notification centers and <tt class="literal"><span class="pre">NSOutlineView</span></tt>, to be exact: the outline view
stores weak references to the objects return by the method 
<tt class="literal"><span class="pre">outlineView:child:ofItem:</span></tt> of its data source. The easiest way to avoid
crashes with outline views is to make sure that you model for the view uses
subclasses of <tt class="literal"><span class="pre">NSObject</span></tt> to represent the nodes in the outline view.</p>
<p>Another gotcha is that <tt class="literal"><span class="pre">obj.setDelegate_()</span></tt> often does <em>not</em> retain the
delegate, so a reference should be maintained elsewhere.</p>
</div>
<div class="section" id="informal-protocols">
<h2><a class="toc-backref" href="#id11" name="informal-protocols">(Informal) protocols</a></h2>
<p>Cocoa defines a number of formal and informal protocols that specify methods
that should be implemented by a class if it is to be used in a specific role,
such as the data source for an <tt class="literal"><span class="pre">NSTableView</span></tt>.</p>
<p>Those protocols are represented by instances of <tt class="literal"><span class="pre">objc.informal_protocol</span></tt>. The
only ones that have to care about these objects are the maintainers of 
wrappers around Objective-C frameworks: they have to keep these protocol
wrappers up-to-date.</p>
<p>PyObjC will automatically use the information in the <tt class="literal"><span class="pre">informal_protocol</span></tt> 
objects to add the right method signatures to methods, and to warn about
classes that partially implement a protocol.</p>
</div>
<div class="section" id="cocoa-bindings">
<h2><a class="toc-backref" href="#id12" name="cocoa-bindings">Cocoa Bindings</a></h2>
<p>In Mac OS X 10.3 Apple introduced <a class="reference" href="http://developer.apple.com/documentation/Cocoa/Conceptual/CocoaBindings/">Cocoa Bindings</a>, a method to make it easier
to create and use <em>Controller</em> objects using <a class="reference" href="http://developer.apple.com/documentation/Cocoa/Conceptual/KeyValueObserving/">Key-Value Observing</a> and <a class="reference" href="http://developer.apple.com/documentation/Cocoa/Conceptual/KeyValueCoding/">Key-Value
Coding</a>.  In order to create accessors compatible with this, you
must use <tt class="literal"><span class="pre">objc.accessor</span></tt> to create an appropriate selector descriptor.</p>
</div>
<div class="section" id="categories">
<h2><a class="toc-backref" href="#id13" name="categories">Categories</a></h2>
<p>Objective-C has a mechanism for modularize a class definition, it is possible
to add methods to an existing class in a separate compilation unit and even
a separate library. This mechanism is named categories and is used to enhance
existing classes, for splitting classes in several parts and to document
informal protocols.</p>
<p>An example of a category definition:</p>
<pre class="literal-block">
&#64;interface NSObject (MyCategory)
- (NSSize)objectFootprint;
&#64;end
</pre>
<p>This declares an additional category on <tt class="literal"><span class="pre">NSObject</span></tt>. This category contains
a single method.</p>
<p>The function <tt class="literal"><span class="pre">objc.classAddMethods</span></tt> can be used to get the same effect in
Python:</p>
<pre class="literal-block">
def objectFootprint(self):
        pass

objc.classAddMethods(NSObject, [objectFootprint])
</pre>
<p>This is not very clear, PyObjC therefore also provides the following 
mechanism, implemented on top of <tt class="literal"><span class="pre">objc.classAddMethods</span></tt>:</p>
<pre class="literal-block">
class NSObject (objc.Category(NSObject)):
        def objectFootprint(self):
                pass
</pre>
<p>To make it clear that <tt class="literal"><span class="pre">objc.Category</span></tt> performs a special task the name in
the class definition must be the same as the <tt class="literal"><span class="pre">__name__</span></tt> of the argument
to <tt class="literal"><span class="pre">objc.Category</span></tt>.</p>
</div>
</div>
<div class="section" id="cocoa-for-python-programmers">
<h1><a class="toc-backref" href="#id14" name="cocoa-for-python-programmers">Cocoa for Python programmers</a></h1>
<p>Cocoa frameworks are mapped onto Python packages with the same name; that is
the classes, constants and functions from the AppKit framework are available
after you import <tt class="literal"><span class="pre">AppKit</span></tt> in your Python script.</p>
<p>These helper modules contain <em>only</em> functions, constants and classes that 
wrap items in the corresponding framework. All utility functions and classes 
are located in the <tt class="literal"><span class="pre">PyObjCTools</span></tt> package and <tt class="literal"><span class="pre">objc</span></tt> module. Note that it
is possible to use <tt class="literal"><span class="pre">pydoc</span></tt> (or the <tt class="literal"><span class="pre">help()</span></tt>) function with the framework
wrappers, but that this is not very useful for the entire module due to the
size of these modules.</p>
<p>This makes it easier to find documentation for an item: if you import it 
from the wrapper module for an Objective-C framework the documentation for
that item can be found in the documentation for the framework; otherwise the
item is documented in the PyObjC documentation.</p>
<p>The module <tt class="literal"><span class="pre">PyObjCTools.NibClassBuilder</span></tt> can be used to make working with 
NIB files more convenient. This module can be used to extract information 
about classes from NIB files, both as a standalone tool generating source code 
and during runtime. See the online documentation for this module for more
information.</p>
<p>PyObjC includes a number of examples that show how to use Cocoa from
Python. The <a class="reference" href="../Examples/00ReadMe.html">PyObjC Example index</a> contains an overview of those examples.</p>
<p>More information on Cocoa programming can be found at:</p>
<ul class="simple">
<li><a class="reference" href="http://developer.apple.com/documentation/Cocoa/Cocoa.html">Cocoa documentation at the Apple developer website</a></li>
<li><a class="reference" href="http://developer.apple.com/samplecode/Cocoa/index.html">Cocoa examples at the Apple developer website</a></li>
<li><a class="reference" href="http://www.stepwise.com/">stepwise.com</a></li>
<li>Your local bookstore or library</li>
</ul>
</div>
<div class="section" id="notes-on-specific-tasks">
<h1><a class="toc-backref" href="#id15" name="notes-on-specific-tasks">Notes on specific tasks</a></h1>
<div class="section" id="working-with-threads">
<h2><a class="toc-backref" href="#id16" name="working-with-threads">Working with threads</a></h2>
<p>When you create a thread and want to use PyObjC from that thread you will
have to create an <tt class="literal"><span class="pre">NSAutoreleasePool</span></tt> in that thread and clean it up when
you're done. The easiest way to that is to create an instance of that class
bound to a local variable. If the thread is long-lived you may want to arrange
for recycling the pool once in a while.</p>
</div>
<div class="section" id="finalizers">
<h2><a class="toc-backref" href="#id17" name="finalizers">Finalizers</a></h2>
<p>In Python you can use the method <tt class="literal"><span class="pre">__del__</span></tt> to clean up resources when your
object is garbage collected. In Objective-C/Cocoa this is done with a method 
named <tt class="literal"><span class="pre">dealloc</span></tt>.</p>
<p>In PyObjC you should always use the <tt class="literal"><span class="pre">__del__</span></tt> method, the <tt class="literal"><span class="pre">dealloc</span></tt> method
can safely be ignored and the bridge will complain when you try to override
this method.</p>
</div>
</div>
<div class="section" id="building-applications">
<h1><a class="toc-backref" href="#id18" name="building-applications">Building applications</a></h1>
<p>There are two different ways to build applications with PyObjC. py2app
should be the preferred method, however using the Xcode template can
be convenient for development.</p>
<div class="section" id="py2app-setup-py">
<h2><a class="toc-backref" href="#id19" name="py2app-setup-py">&quot;py2app&quot; :  setup.py</a></h2>
<p>The PyObjC installer includes a copy of the <tt class="literal"><span class="pre">py2app</span></tt> package. This package
offers a way to build distutils scripts for building (standalone)
applications and plugin bundles.</p>
<p>An example <tt class="literal"><span class="pre">setup.py</span></tt> script:</p>
<pre class="literal-block">
from distutils.core import setup
import py2app

setup(
    app = [&quot;iClass.py&quot;],
    data_files = [&quot;English.lproj&quot;],
)
</pre>
<p>During development you typically invoke it from the command line like this:</p>
<pre class="literal-block">
python setup.py py2app -A
</pre>
<p>This will build an application bundle in a folder named <tt class="literal"><span class="pre">dist</span></tt> in the
current folder. The <tt class="literal"><span class="pre">-A</span></tt> option tells <tt class="literal"><span class="pre">py2app</span></tt> to add symbolic
links for data folders and files and an Alias to your main script,
allowing you quickly rebuild the application without doing a full dependency
scan, with the additional bonus that you can edit them without rebuild. To
build a standalone application, simply do not use the <tt class="literal"><span class="pre">-A</span></tt> option.
Note that if you are using a version of Python shipped with your operating
system, it will not be included in the application.  Otherwise, your
application will include stripped down version of the Python runtime that
you ran setup.py with.</p>
<p>For more information about <tt class="literal"><span class="pre">py2app</span></tt> usage, read through some of the
<tt class="literal"><span class="pre">setup.py</span></tt> scripts used by the examples in the <a class="reference" href="../Examples/00ReadMe.txt">Examples</a> folder.
On any <tt class="literal"><span class="pre">setup.py</span></tt> script that imports <tt class="literal"><span class="pre">py2app</span></tt>, you can use the
following command to see the list of options:</p>
<pre class="literal-block">
python setup.py py2app --help
</pre>
</div>
<div class="section" id="ide-approach-xcode">
<h2><a class="toc-backref" href="#id20" name="ide-approach-xcode">&quot;IDE approach&quot; : Xcode</a></h2>
<p>PyObjC includes a number of Xcode templates that can be used to 
develop applications. Those templates are used like any other
Xcode template. The only non-obvious detail is that you have to
add your sources as resources, but Project Builder usually does the right
thing when you add a new file.</p>
<p>The templates will build an application that makes use of the installed copy
<tt class="literal"><span class="pre">/System/Library/Frameworks/Python.framework</span></tt> (e.g. the one shipped by
Apple in Mac OS X 10.3) and will copy the PyObjC modules into the application
bundle. This means that this application bundle <em>should</em> be useable on any
Mac OS X 10.3 system, if you do not have any additional dependencies.  Using
py2app to deploy your application is a more reliable method, and builds more
compact bundles.</p>
<p>See <a class="reference" href="Xcode-Templates.html">the documentation for the templates</a> for more details.</p>
</div>
</div>
</div>
<?
    include "footer.inc";
?>