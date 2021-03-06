=========================================================
:mod:`PyObjCTools.AppHelper` -- Work with AppKit
=========================================================

.. module:: PyObjCTools.AppHelper
   :synopsis: Work with AppKit

This module exports functions that are useful when working with the
``AppKit`` framework (or more generally, run loops).

.. function:: callAfter(func, *args, **kwargs)

    Call a function on the main thread.  Returns immediately.

.. function:: callLater(delay, func, *args, **kwargs)

    Call a function on the main thread after a delay.  Returns immediately.

.. function:: endSheetMethod(method)

    Convert a method to a form that is suitable to use as the delegate callback
    for sheet methods.

    :rtype: objc.selector

.. function:: stopEventLoop()

    Stops the event loop (if started by :func:`runConsoleEventLoop`) or sends the
    ``NSApplication`` a ``terminate:`` message.

.. function:: runConsoleEventLoop(argv=None, installInterrupt=False, mode=NSDefaultRunLoopMode, maxTimeout=3.0)

    Run a ``NSRunLoop`` in a stoppable way (with ``stopEventLoop``). The
    maximum delay between a call to :func:`stopEventLoop` and actually stopping
    the runloop is *maxTimeout* seconds.

    .. versionadded: 3.1
       The *maxTimeout* parameter

.. function:: runEventLoop(argv=None, unexpectedErrorAlert=unexpectedErrorAlert, installInterrupt=None, pdb=None, main=NSApplicationMain)

    Run the event loop using ``NSApplicationMain`` and ask the user if we should
    continue if an exception is caught.

    This function doesn't return unless it throws an exception.
