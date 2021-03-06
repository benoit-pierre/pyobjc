# This file is generated by objective.metadata
#
# Last update: Tue Aug 19 22:14:11 2014

import objc, sys

if sys.maxsize > 2 ** 32:
    def sel32or64(a, b): return b
else:
    def sel32or64(a, b): return a
if sys.byteorder == 'little':
    def littleOrBig(a, b): return a
else:
    def littleOrBig(a, b): return b

misc = {
}
constants = '''$CBATTErrorDomain$CBAdvertisementDataIsConnectable$CBAdvertisementDataLocalNameKey$CBAdvertisementDataManufacturerDataKey$CBAdvertisementDataOverflowServiceUUIDsKey$CBAdvertisementDataServiceDataKey$CBAdvertisementDataServiceUUIDsKey$CBAdvertisementDataSolicitedServiceUUIDsKey$CBAdvertisementDataTxPowerLevelKey$CBCentralManagerOptionRestoreIdentifierKey$CBCentralManagerOptionShowPowerAlertKey$CBCentralManagerRestoredStatePeripheralsKey$CBCentralManagerRestoredStateScanOptionsKey$CBCentralManagerRestoredStateScanServicesKey$CBCentralManagerScanOptionAllowDuplicatesKey$CBCentralManagerScanOptionSolicitedServiceUUIDsKey$CBConnectPeripheralOptionNotifyOnConnectionKey$CBConnectPeripheralOptionNotifyOnDisconnectionKey$CBConnectPeripheralOptionNotifyOnNotificationKey$CBErrorDomain$CBPeripheralManagerOptionRestoreIdentifierKey$CBPeripheralManagerOptionShowPowerAlertKey$CBPeripheralManagerRestoredStateAdvertisementDataKey$CBPeripheralManagerRestoredStateServicesKey$CBUUIDAppearanceString$CBUUIDCharacteristicAggregateFormatString$CBUUIDCharacteristicExtendedPropertiesString$CBUUIDCharacteristicFormatString$CBUUIDCharacteristicUserDescriptionString$CBUUIDClientCharacteristicConfigurationString$CBUUIDDeviceNameString$CBUUIDGenericAccessProfileString$CBUUIDGenericAttributeProfileString$CBUUIDPeripheralPreferredConnectionParametersString$CBUUIDPeripheralPrivacyFlagString$CBUUIDReconnectionAddressString$CBUUIDServerCharacteristicConfigurationString$CBUUIDServiceChangedString$'''
enums = '''$CBATTErrorAttributeNotFound@10$CBATTErrorAttributeNotLong@11$CBATTErrorInsufficientAuthentication@5$CBATTErrorInsufficientAuthorization@8$CBATTErrorInsufficientEncryption@15$CBATTErrorInsufficientEncryptionKeySize@12$CBATTErrorInsufficientResources@17$CBATTErrorInvalidAttributeValueLength@13$CBATTErrorInvalidHandle@1$CBATTErrorInvalidOffset@7$CBATTErrorInvalidPdu@4$CBATTErrorPrepareQueueFull@9$CBATTErrorReadNotPermitted@2$CBATTErrorRequestNotSupported@6$CBATTErrorSuccess@0$CBATTErrorUnlikelyError@14$CBATTErrorUnsupportedGroupType@16$CBATTErrorWriteNotPermitted@3$CBAttributePermissionsReadEncryptionRequired@4$CBAttributePermissionsReadable@1$CBAttributePermissionsWriteEncryptionRequired@8$CBAttributePermissionsWriteable@2$CBCentralManagerStatePoweredOff@4$CBCentralManagerStatePoweredOn@5$CBCentralManagerStateResetting@1$CBCentralManagerStateUnauthorized@3$CBCentralManagerStateUnknown@0$CBCentralManagerStateUnsupported@2$CBCharacteristicPropertyAuthenticatedSignedWrites@64$CBCharacteristicPropertyBroadcast@1$CBCharacteristicPropertyExtendedProperties@128$CBCharacteristicPropertyIndicate@32$CBCharacteristicPropertyIndicateEncryptionRequired@512$CBCharacteristicPropertyNotify@16$CBCharacteristicPropertyNotifyEncryptionRequired@256$CBCharacteristicPropertyRead@2$CBCharacteristicPropertyWrite@8$CBCharacteristicPropertyWriteWithoutResponse@4$CBCharacteristicWriteWithResponse@0$CBCharacteristicWriteWithoutResponse@1$CBErrorAlreadyAdvertising@9$CBErrorConnectionTimeout@6$CBErrorInvalidHandle@2$CBErrorInvalidParameters@1$CBErrorNotConnected@3$CBErrorOperationCancelled@5$CBErrorOutOfSpace@4$CBErrorPeripheralDisconnected@7$CBErrorUUIDNotAllowed@8$CBErrorUnknown@0$CBPeripheralAuthorizationStatusAuthorized@3$CBPeripheralAuthorizationStatusDenied@2$CBPeripheralAuthorizationStatusNotDetermined@0$CBPeripheralAuthorizationStatusRestricted@1$CBPeripheralManagerConnectionLatencyHigh@2$CBPeripheralManagerConnectionLatencyLow@0$CBPeripheralManagerConnectionLatencyMedium@1$CBPeripheralManagerStatePoweredOff@4$CBPeripheralManagerStatePoweredOn@5$CBPeripheralManagerStateResetting@1$CBPeripheralManagerStateUnauthorized@3$CBPeripheralManagerStateUnknown@0$CBPeripheralManagerStateUnsupported@2$CBPeripheralStateConnected@2$CBPeripheralStateConnecting@1$CBPeripheralStateDisconnected@0$'''
misc.update({})
r = objc.registerMetaDataForSelector
objc._updatingMetadata(True)
try:
    r(b'CBCharacteristic', b'isBroadcasted', {'retval': {'type': b'Z'}})
    r(b'CBCharacteristic', b'isNotifying', {'retval': {'type': b'Z'}})
    r(b'CBMutableService', b'initWithType:primary:', {'arguments': {3: {'type': b'Z'}}})
    r(b'CBMutableService', b'isPrimary', {'retval': {'type': b'Z'}})
    r(b'CBMutableService', b'setIsPrimary:', {'arguments': {2: {'type': b'Z'}}})
    r(b'CBPeripheral', b'isConnected', {'retval': {'type': b'Z'}})
    r(b'CBPeripheral', b'setNotifyValue:forCharacteristic:', {'arguments': {2: {'type': b'Z'}}})
    r(b'CBPeripheralManager', b'isAdvertising', {'retval': {'type': b'Z'}})
    r(b'CBPeripheralManager', b'updateValue:forCharacteristic:onSubscribedCentrals:', {'retval': {'type': b'Z'}})
    r(b'CBService', b'isPrimary', {'retval': {'type': b'Z'}})
finally:
    objc._updatingMetadata(False)
r = objc.registerMetaDataForSelector
objc._updatingMetadata(True)
try:
    r(b'NSObject', b'centralManager:didConnectPeripheral:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'centralManager:didDisconnectPeripheral:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'centralManager:didDiscoverPeripheral:advertisementData:RSSI:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}, 5: {'type': b'@'}}})
    r(b'NSObject', b'centralManager:didFailToConnectPeripheral:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'centralManager:didRetrieveConnectedPeripherals:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'centralManager:didRetrievePeripherals:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'centralManager:willRestoreState:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'centralManagerDidUpdateState:', {'required': True, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didDiscoverCharacteristicsForService:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didDiscoverDescriptorsForCharacteristic:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didDiscoverIncludedServicesForService:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didDiscoverServices:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didModifyServices:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didUpdateNotificationStateForCharacteristic:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didUpdateValueForCharacteristic:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didUpdateValueForDescriptor:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didWriteValueForCharacteristic:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheral:didWriteValueForDescriptor:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheralDidInvalidateServices:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}}})
    r(b'NSObject', b'peripheralDidUpdateName:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}}})
    r(b'NSObject', b'peripheralDidUpdateRSSI:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManager:central:didSubscribeToCharacteristic:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManager:central:didUnsubscribeFromCharacteristic:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManager:didAddService:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}, 4: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManager:didReceiveReadRequest:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManager:didReceiveWriteRequests:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManager:willRestoreState:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManagerDidStartAdvertising:error:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}, 3: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManagerDidUpdateState:', {'required': True, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}}})
    r(b'NSObject', b'peripheralManagerIsReadyToUpdateSubscribers:', {'required': False, 'retval': {'type': b'v'}, 'arguments': {2: {'type': b'@'}}})
finally:
    objc._updatingMetadata(False)
expressions = {}

# END OF FILE
