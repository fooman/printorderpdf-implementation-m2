<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_PrintOrderPdf
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\PrintOrderPdf\Block\Adminhtml;

use Fooman\PhpunitBridge\AbstractBackendController;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea adminhtml
 */
class PrintOrderButtonTest extends AbstractBackendController
{
    public function setUp(): void
    {
        $this->resource = 'Magento_Sales::sales_order';
        $this->uri = 'backend/sales/order';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDataFixture prepareOrder
     */
    public function testPrintOrderButton()
    {
        $orderId = Bootstrap::getObjectManager()->create(Order::class)->loadByIncrementId('100000001')->getId();
        $this->dispatch('backend/sales/order/view/order_id/' . $orderId);
        $this->assertStringContainsString('<button id="fooman_print" title="Print"', $this->getResponse()->getBody());
    }

    public function testPrintOrdersMassaction()
    {
        $this->dispatch('backend/sales/order');
        $this->assertStringContainsString(
            '"type":"fooman_pdforders","label":"Print Orders"',
            $this->getResponse()->getBody()
        );
    }

    public function testStandardMassactionsShow()
    {
        $this->dispatch('backend/sales/order');
        $body = $this->getResponse()->getBody();
        $this->assertStringContainsString('"type":"cancel"', $body);
        $this->assertStringContainsString('"label":"Cancel"', $body);
        $this->assertStringContainsString('"label":"Print Shipping Labels"', $body);
        $this->assertStringContainsString('"type":"print_shipping_label"', $body);
    }

    public static function prepareOrder()
    {
        /** @var Order $order */
        $order = Bootstrap::getObjectManager()->create(Order::class);
        $order->loadByIncrementId('100000001');

        $shippingAddress = clone $order->getBillingAddress();
        $shippingAddress->setId(null)->setAddressType('shipping');
        $shippingAddress->setShippingMethod('flatrate_flatrate');

        $order->setShippingAddress($shippingAddress);
        $order->setShippingMethod('flatrate_flatrate');
        $order->save();
    }
}
