<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_PrintOrderPdf
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\PrintOrderPdf\Controller\Adminhtml\Order;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Sales\Model\Order;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class PdfordersTest extends AbstractBackendController
{
    public function setUp(): void
    {
        $this->resource = 'Magento_Sales::sales_order';
        $this->uri = 'backend/fooman_printorderpdf/order/pdforders';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_list.php
     */
    public function testDownloadContainsOrderPdf(): void
    {
        $orders = $this->_objectManager->create(Order::class)->getCollection();
        $orders->addFieldToFilter('increment_id', ['in' => ['100000001', '100000002']]);
        $orderIds = $orders->getAllIds();
        $this->assertCount(2, $orderIds);
        $this->getRequest()->setParams([
            'selected' => $orderIds,
            'namespace' => 'sales_order_grid',
            'filters' => ['placeholder' => 'true'],
        ]);

        $response = $this->getResponse();
        $fileFactory = $this->createMock(FileFactory::class);
        $fileFactory->expects($this->once())->method('create')->with(
            $this->stringEndsWith('.pdf'),
            $this->callback(function (array $content): bool {
                $this->assertSame('string', $content['type']);
                $this->assertStringStartsWith('%PDF-1.', $content['value']);
                $this->assertCount(2, \Zend_Pdf::parse($content['value'])->pages);
                return true;
            }),
            DirectoryList::VAR_DIR,
            'application/pdf'
        )->willReturn($response);

        $controller = $this->_objectManager->create(Pdforders::class, ['fileFactory' => $fileFactory]);
        $this->assertSame($response, $controller->execute());
    }
}
