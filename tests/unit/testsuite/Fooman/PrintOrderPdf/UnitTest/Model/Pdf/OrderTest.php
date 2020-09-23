<?php

namespace Fooman\PrintOrderPdf\UnitTest\Model\Pdf;

use Fooman\PhpunitBridge\BaseUnitTestCase;
use Fooman\PrintOrderPdf\Model\Pdf\Order;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Sales\Model\Order\Pdf\Total\Factory;
use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Sales\Model\Order\Pdf\ItemsFactory;
use Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Payment;
use Magento\Directory\Model\Currency;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\Filesystem;
use Magento\Payment\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for
 * @see Fooman\PrintOrderPdf\Model\Pdf\Order
 */
class OrderTest extends BaseUnitTestCase
{
    /**
     * @var Order
     */
    protected $object;

    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $pdfConfigMock = $this->getPdfConfigMock();
        $directoryMock = $this->getDirectoryMock();
        $filesystemMock = $this->getFileSystemMock($directoryMock);

        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->any())->method('getBaseUrl')->willReturn('/');
        $storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($storeMock);

        $scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $localeDataMock = $this->createMock(TimezoneInterface::class);
        $inlineTranslationMock = $this->createMock(StateInterface::class);
        $appEmulationMock = $this->createPartialMock(
            Emulation::class,
            ['startEnvironmentEmulation', 'stopEnvironmentEmulation']
        );

        $appEmulationMock->expects($this->once())->method('startEnvironmentEmulation')->willReturnSelf();
        $appEmulationMock->expects($this->once())->method('stopEnvironmentEmulation')->willReturnSelf();

        $paymentDataMock = $this->getPaymentDataMock();

        $pdfTotalFactoryMock = $this->createPartialMock(
            Factory::class,
            ['create']
        );

        $pdfTotalFactoryMock->expects($this->any())->method('create')->willReturn(
            $objectManager->getObject(DefaultTotal::class)
        );

        $pdfItemsFactoryMock = $this->createPartialMock(
            ItemsFactory::class,
            ['get']
        );
        $pdfItemsFactoryMock->expects($this->any())->method('get')->willReturn(
            $objectManager->getObject(DefaultInvoice::class)
        );

        $orderConstructorArgs = [
            'paymentData'       => $paymentDataMock,
            'string'            => $objectManager->getObject(StringUtils::class),
            'scopeConfig'       => $scopeConfigMock,
            'filesystem'        => $filesystemMock,
            'pdfConfig'         => $pdfConfigMock,
            'pdfTotalFactory'   => $pdfTotalFactoryMock,
            'pdfItemsFactory'   => $pdfItemsFactoryMock,
            'localeDate'        => $localeDataMock,
            'inlineTranslation' => $inlineTranslationMock,
            'storeManager'      => $storeManagerMock,
            'appEmulation'      => $appEmulationMock,
            []
        ];

        $this->object = $objectManager->getObject(Order::class, $orderConstructorArgs);
    }

    public function testGetPdf()
    {
        $orderMock = $this->createPartialMock(
            \Magento\Sales\Model\Order::class,
            [
                'getBillingAddress',
                'getShippingAddress',
                'getStore',
                'getPayment',
                'getOrderCurrency',
                'getAllItems',
                'getStoreId'
            ]
        );

        $orderMock->expects($this->any())->method('getStoreId')->willReturn(
            Store::DISTRO_STORE_ID
        );

        $orderItemMock = $this->createPartialMock(
            Item::class,
            ['getProductType', 'getSku', 'getName']
        );
        $orderItemMock->expects($this->any())->method('getProductType')->willReturn(
            'default'
        );
        $orderItemMock->expects($this->any())->method('getSku')->willReturn(
            'Item SKU'
        );
        $orderItemMock->expects($this->any())->method('getName')->willReturn(
            'Item Name'
        );

        $orderParentItemMock = $this->createPartialMock(
            Item::class,
            ['getParentItem', 'getSku', 'getName']
        );
        $orderParentItemMock->expects($this->any())->method('getParentItem')->willReturn(
            true
        );
        $orderParentItemMock->expects($this->any())->method('getSku')->willReturn(
            'Parent Item SKU'
        );
        $orderParentItemMock->expects($this->any())->method('getName')->willReturn(
            'Parent Item Name'
        );

        $orderMock->expects($this->any())->method('getAllItems')->willReturn(
            [$orderParentItemMock, $orderItemMock]
        );

        $addressMock = $this->createPartialMock(
            Address::class,
            ['format']
        );
        $addressMock->expects($this->any())->method('format')->willReturn(
            'Street Line 1 with a very long Street name and number 1234567890|Street Line 2|City|Country'
        );
        $orderMock->expects($this->any())->method('getBillingAddress')->willReturn($addressMock);
        $orderMock->expects($this->any())->method('getShippingAddress')->willReturn($addressMock);

        $paymentMock = $this->createMock(Payment::class);
        $currencyMock = $this->createMock(Currency::class);

        $orderMock->expects($this->any())->method('getPayment')->willReturn($paymentMock);
        $orderMock->expects($this->any())->method('getOrderCurrency')->willReturn($currencyMock);

        $pdf = $this->object->getPdf([$orderMock]);
        $this->assertInstanceOf('Zend_Pdf', $pdf);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPdfConfigMock()
    {
        $pdfConfigMock = $this->createPartialMock(
            Config::class,
            ['getRenderersPerProduct', 'getTotals']
        );
        $pdfConfigMock->expects($this->any())->method('getRenderersPerProduct')->willReturn(
            ['default' => '>\Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice']
        );

        $pdfConfigMock->expects($this->any())->method('getTotals')->willReturn(
            ['grand_total' => ['source_field' => 'grand_total']]
        );
        return $pdfConfigMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDirectoryMock()
    {
        $directoryMock = $this->createPartialMock(
            Write::class,
            ['getAbsolutePath']
        );
        $directoryMock->expects($this->any())->method('getAbsolutePath')->willReturnCallback(
            function ($argument) {
                if (strpos($argument, 'lib/internal/LinLibertineFont/') === 0
                    || strpos($argument, 'lib/internal/GnuFreeFont/') === 0) {
                    $argument = str_replace('lib/internal/', '', $argument);
                    return __DIR__ . '/_files/' . $argument;
                }
                return dirname(__DIR__, 8) . '/' . $argument;
            }
        );
        return $directoryMock;
    }

    /**
     * @param $directoryMock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFileSystemMock($directoryMock)
    {
        $filesystemMock = $this->createPartialMock(
            Filesystem::class,
            ['getDirectoryRead','getDirectoryWrite']
        );
        $filesystemMock->expects($this->any())->method('getDirectoryRead')->willReturn($directoryMock);
        $filesystemMock->expects($this->any())->method('getDirectoryWrite')->willReturn($directoryMock);
        return $filesystemMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPaymentDataMock()
    {
        $paymentDataMock = $this->createPartialMock(
            Data::class,
            ['getInfoBlock']
        );

        $blockMock = $this->createPartialMock(
            Template::class,
            ['toPdf']
        );
        $blockMock->expects($this->any())->method('toPdf')->willReturn('PAYMENT INFO');

        $paymentDataMock->expects($this->any())->method('getInfoBlock')->willReturn($blockMock);
        return $paymentDataMock;
    }
}
