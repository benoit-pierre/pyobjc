
from PyObjCTools.TestSupport import *
from AppKit import *

class TestNSStatusBar (TestCase):
    def testConstants(self):
        self.assertEqual(NSVariableStatusItemLength, -1.0)
        self.assertEqual(NSSquareStatusItemLength, -2.0)

    def testMethods(self):
        self.assertResultIsBOOL(NSStatusBar.isVertical)

if __name__ == "__main__":
    main()
