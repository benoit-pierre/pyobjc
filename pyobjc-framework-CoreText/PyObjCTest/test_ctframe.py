
from PyObjCTools.TestSupport import *
from CoreText import *
from Quartz import *

import os

class TestCTFrame (TestCase):

    def testTypes(self):
        self.assertIsInstance(CTFrameRef, objc.objc_class)

    def testConstants(self):
        self.assertEqual(kCTFrameProgressionTopToBottom, 0)
        self.assertEqual(kCTFrameProgressionRightToLeft, 1)
        self.assertEqual(kCTFrameProgressionLeftToRight, 2)

        self.assertEqual(kCTFramePathFillEvenOdd, 0)
        self.assertEqual(kCTFramePathFillWindingNumber, 1)

        self.assertIsInstance(kCTFrameProgressionAttributeName, unicode)

    @min_os_level('10.7')
    def testConstants10_7(self):
        self.assertIsInstance(kCTFramePathFillRuleAttributeName, unicode)
        self.assertIsInstance(kCTFramePathWidthAttributeName, unicode)
        self.assertIsInstance(kCTFrameClippingPathsAttributeName, unicode)
        self.assertIsInstance(kCTFramePathClippingPathAttributeName, unicode)

    def testFunctions(self):
        v = CTFrameGetTypeID()
        self.assertIsInstance(v, (int, long))

        setter = CTFramesetterCreateWithAttributedString(
                CFAttributedStringCreate(None, b"hello".decode('latin1'), None))
        self.assertIsInstance(setter, CTFramesetterRef)

        path = CGPathCreateMutable()
        self.assertIsInstance(path, CGPathRef)
        CGPathAddRect(path, None, CGRect(CGPoint(0, 0), CGSize(400, 400)))
        attr = { 'foo': 42 }
        frame = CTFramesetterCreateFrame(setter, CFRange(0, 5),
                path, attr)
        self.assertIsInstance(frame, CTFrameRef)

        v = CTFrameGetStringRange(frame)
        self.assertIsInstance(v, CFRange)

        v = CTFrameGetVisibleStringRange(frame)
        self.assertIsInstance(v, CFRange)

        self.assertResultIsNotCFRetained(CTFrameGetPath)
        v = CTFrameGetPath(frame)
        self.assertIsInstance(v, CGPathRef)

        v = CTFrameGetFrameAttributes(frame)
        self.assertTrue(v is attr)

        v = CTFrameGetLines(frame)
        self.assertIsInstance(v, CFArrayRef)

        self.assertArgIsOut(CTFrameGetLineOrigins, 2)
        self.assertArgSizeInArg(CTFrameGetLineOrigins, 2, 1)
        v = CTFrameGetLineOrigins(frame, CFRange(0, 1), None)
        self.assertIsInstance(v, tuple)
        self.assertEqual(len(v), 1)
        self.assertIsInstance(v[0], CGPoint)

        url = CFURLCreateWithFileSystemPath(None,
                "/tmp/pyobjc.test.pdf", kCFURLPOSIXPathStyle, False)
        self.assertIsInstance(url, CFURLRef)
        ctx = CGPDFContextCreateWithURL(url, CGRect(CGPoint(0, 0), CGSize(1000, 1000)), None)
        self.assertIsInstance(ctx, CGContextRef)

        CTFrameDraw(frame, ctx)

        if os.path.exists("/tmp/pyobjc.test.pdf"):
            os.unlink("/tmp/pyobjc.test.pdf")


if __name__ == "__main__":
    main()
