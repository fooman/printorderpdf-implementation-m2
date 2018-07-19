<?php


require __DIR__ . '/../../../../../Magento/Sales/_files/order.php';

/** @var \Magento\Sales\Model\Order $order */
$order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
$order->loadByIncrementId('100000001');


$shippingAddress = clone $order->getBillingAddress();
$shippingAddress->setId(null)->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');


$order->setShippingAddress($shippingAddress);
$order->setShippingMethod('flatrate_flatrate');
$order->save();