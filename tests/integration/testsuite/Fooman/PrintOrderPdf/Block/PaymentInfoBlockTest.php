<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_PrintOrderPdf
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PrintOrderPdf\Block;

use Fooman\PhpunitBridge\BaseUnitTestCase;
use Fooman\PrintOrderPdf\Model\Pdf\Order;
use Magento\Framework\Module\Manager;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\TestFramework\Helper\Bootstrap;

class PaymentInfoBlockTest extends BaseUnitTestCase
{

    private $objectManager;

    private $helper;

    private $pdf;

    private $moduleManager;

    public function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->pdf = $this->objectManager->create(Order::class);

        $this->moduleManager = $this->objectManager->create(Manager::class);
        $this->helper = $this->objectManager->get(Data::class);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoAppArea     adminhtml
     */
    public function testToPdfAdmin()
    {
        $order = $this->prepareOrder();
        $paymentInfo = $this->helper->getInfoBlock($order->getPayment())->setIsSecureMode(true);
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $paymentInfo->setFoomanThemePath('frontend/Magento/blank');
        }
        self::assertStringContainsString('Check / Money order', $paymentInfo->toPdf());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoAppArea     frontend
     */
    public function testToPdfFrontend()
    {
        $order = $this->prepareOrder();
        $paymentInfo = $this->helper->getInfoBlock($order->getPayment())->setIsSecureMode(true);
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $paymentInfo->setFoomanThemePath('frontend/Magento/blank');
        }
        self::assertStringContainsString('Check / Money order', $paymentInfo->toPdf());
    }

    /**
     * @return mixed
     */
    protected function prepareOrder()
    {
        return Bootstrap::getObjectManager()->create(MagentoOrder::class)->loadByIncrementId('100000001');
    }
}
