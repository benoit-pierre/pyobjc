from PyObjCTools.TestSupport import *
import objc

from AppKit import *

class TestNSCell(TestCase):
    def testUnicode(self):
        u = b'\xc3\xbc\xc3\xb1\xc3\xae\xc3\xa7\xc3\xb8d\xc3\xa8'.decode('latin1')
        cell = NSCell.alloc().initTextCell_(u)
        cell.setStringValue_(u)
        self.assertEqual(cell.stringValue(), u)

    def testInt(self):
        i = 17
        cell = NSCell.alloc().initTextCell_(b"".decode('ascii'))
        cell.setIntValue_(i)
        self.assertEqual(cell.intValue(), i)

    def testFloat(self):
        f = 3.125
        cell = NSCell.alloc().initTextCell_(b"".decode('ascii'))
        cell.setFloatValue_(f)
        self.assertEqual(cell.floatValue(), f)

    def testMethods(self):
        self.assertResultIsBOOL(NSCell.prefersTrackingUntilMouseUp)
        self.assertResultIsBOOL(NSCell.isOpaque)
        self.assertResultIsBOOL(NSCell.isEnabled)
        self.assertArgIsBOOL(NSCell.setEnabled_, 0)
        self.assertResultIsBOOL(NSCell.isContinuous)
        self.assertArgIsBOOL(NSCell.setContinuous_, 0)
        self.assertResultIsBOOL(NSCell.isEditable)
        self.assertArgIsBOOL(NSCell.setEditable_, 0)
        self.assertResultIsBOOL(NSCell.isSelectable)
        self.assertArgIsBOOL(NSCell.setSelectable_, 0)
        self.assertResultIsBOOL(NSCell.isBordered)
        self.assertArgIsBOOL(NSCell.setBordered_, 0)
        self.assertResultIsBOOL(NSCell.isBezeled)
        self.assertArgIsBOOL(NSCell.setBezeled_, 0)
        self.assertResultIsBOOL(NSCell.isScrollable)
        self.assertArgIsBOOL(NSCell.setScrollable_, 0)
        self.assertResultIsBOOL(NSCell.isHighlighted)
        self.assertArgIsBOOL(NSCell.setHighlighted_, 0)
        self.assertResultIsBOOL(NSCell.wraps)
        self.assertArgIsBOOL(NSCell.setWraps_, 0)
        self.assertResultIsBOOL(NSCell.isEntryAcceptable_)
        self.assertArgIsBOOL(NSCell.setFloatingPointFormat_left_right_, 0)
        self.assertResultIsBOOL(NSCell.hasValidObjectValue)
        self.assertArgIsBOOL(NSCell.highlight_withFrame_inView_, 0)
        self.assertArgIsOut(NSCell.getPeriodicDelay_interval_, 0)
        self.assertArgIsOut(NSCell.getPeriodicDelay_interval_, 1)
        self.assertResultIsBOOL(NSCell.startTrackingAt_inView_)
        self.assertResultIsBOOL(NSCell.continueTracking_at_inView_)
        self.assertArgIsBOOL(NSCell.stopTracking_at_inView_mouseIsUp_, 3)
        self.assertResultIsBOOL(NSCell.trackMouse_inRect_ofView_untilMouseUp_)
        self.assertArgIsBOOL(NSCell.trackMouse_inRect_ofView_untilMouseUp_, 3)
        self.assertResultIsBOOL(NSCell.sendsActionOnEndEditing)
        self.assertArgIsBOOL(NSCell.setSendsActionOnEndEditing_, 0)
        self.assertResultIsBOOL(NSCell.allowsUndo)
        self.assertArgIsBOOL(NSCell.setAllowsUndo_, 0)
        self.assertResultIsBOOL(NSCell.truncatesLastVisibleLine)
        self.assertArgIsBOOL(NSCell.setTruncatesLastVisibleLine_, 0)
        self.assertResultIsBOOL(NSCell.refusesFirstResponder)
        self.assertArgIsBOOL(NSCell.setRefusesFirstResponder_, 0)
        self.assertResultIsBOOL(NSCell.acceptsFirstResponder)
        self.assertResultIsBOOL(NSCell.showsFirstResponder)
        self.assertArgIsBOOL(NSCell.setShowsFirstResponder_, 0)
        self.assertResultIsBOOL(NSCell.wantsNotificationForMarkedText)
        self.assertResultIsBOOL(NSCell.allowsEditingTextAttributes)
        self.assertArgIsBOOL(NSCell.setAllowsEditingTextAttributes_, 0)
        self.assertResultIsBOOL(NSCell.importsGraphics)
        self.assertArgIsBOOL(NSCell.setImportsGraphics_, 0)
        self.assertResultIsBOOL(NSCell.allowsMixedState)
        self.assertArgIsBOOL(NSCell.setAllowsMixedState_, 0)


    def testFunctions(self):
        self.assertArgIsBOOL(NSDrawThreePartImage, 4)
        self.assertArgIsBOOL(NSDrawThreePartImage, 7)
        self.assertArgIsBOOL(NSDrawNinePartImage, 12)

    def testConstants(self):
        self.assertIsInstance(NSControlTintDidChangeNotification, unicode)

        self.assertEqual(NSAnyType, 0)
        self.assertEqual(NSIntType, 1)
        self.assertEqual(NSPositiveIntType, 2)
        self.assertEqual(NSFloatType, 3)
        self.assertEqual(NSPositiveFloatType, 4)
        self.assertEqual(NSDoubleType, 6)
        self.assertEqual(NSPositiveDoubleType, 7)

        self.assertEqual(NSNullCellType, 0)
        self.assertEqual(NSTextCellType, 1)
        self.assertEqual(NSImageCellType, 2)

        self.assertEqual(NSCellDisabled, 0)
        self.assertEqual(NSCellState, 1)
        self.assertEqual(NSPushInCell, 2)
        self.assertEqual(NSCellEditable, 3)
        self.assertEqual(NSChangeGrayCell, 4)
        self.assertEqual(NSCellHighlighted, 5)
        self.assertEqual(NSCellLightsByContents, 6)
        self.assertEqual(NSCellLightsByGray, 7)
        self.assertEqual(NSChangeBackgroundCell, 8)
        self.assertEqual(NSCellLightsByBackground, 9)
        self.assertEqual(NSCellIsBordered, 10)
        self.assertEqual(NSCellHasOverlappingImage, 11)
        self.assertEqual(NSCellHasImageHorizontal, 12)
        self.assertEqual(NSCellHasImageOnLeftOrBottom, 13)
        self.assertEqual(NSCellChangesContents, 14)
        self.assertEqual(NSCellIsInsetButton, 15)
        self.assertEqual(NSCellAllowsMixedState, 16)

        self.assertEqual(NSNoImage, 0)
        self.assertEqual(NSImageOnly, 1)
        self.assertEqual(NSImageLeft, 2)
        self.assertEqual(NSImageRight, 3)
        self.assertEqual(NSImageBelow, 4)
        self.assertEqual(NSImageAbove, 5)
        self.assertEqual(NSImageOverlaps, 6)

        self.assertEqual(NSScaleProportionally, 0)
        self.assertEqual(NSScaleToFit, 1)
        self.assertEqual(NSScaleNone, 2)

        self.assertEqual(NSImageScaleProportionallyDown, 0)
        self.assertEqual(NSImageScaleAxesIndependently,1)
        self.assertEqual(NSImageScaleNone, 2)
        self.assertEqual(NSImageScaleProportionallyUpOrDown, 3)

        self.assertEqual(NSMixedState, -1)
        self.assertEqual(NSOffState,  0)
        self.assertEqual(NSOnState,  1)

        self.assertEqual(NSNoCellMask, 0)
        self.assertEqual(NSContentsCellMask, 1)
        self.assertEqual(NSPushInCellMask, 2)
        self.assertEqual(NSChangeGrayCellMask, 4)
        self.assertEqual(NSChangeBackgroundCellMask, 8)

        self.assertEqual(NSDefaultControlTint, 0)
        self.assertEqual(NSBlueControlTint, 1)
        self.assertEqual(NSGraphiteControlTint, 6)
        self.assertEqual(NSClearControlTint, 7)

        self.assertEqual(NSRegularControlSize, 0)
        self.assertEqual(NSSmallControlSize, 1)
        self.assertEqual(NSMiniControlSize, 2)

        self.assertEqual(NSCellHitNone, 0)
        self.assertEqual(NSCellHitContentArea, 1 << 0)
        self.assertEqual(NSCellHitEditableTextArea, 1 << 1)
        self.assertEqual(NSCellHitTrackableArea, 1 << 2)

        self.assertEqual(NSBackgroundStyleLight, 0)
        self.assertEqual(NSBackgroundStyleDark, 1)
        self.assertEqual(NSBackgroundStyleRaised, 2)
        self.assertEqual(NSBackgroundStyleLowered, 3)

    @min_os_level('10.6')
    def testMethods10_6(self):
        self.assertResultIsBOOL(NSCell.usesSingleLineMode)
        self.assertArgIsBOOL(NSCell.setUsesSingleLineMode_, 0)


if __name__ == '__main__':
    main( )
