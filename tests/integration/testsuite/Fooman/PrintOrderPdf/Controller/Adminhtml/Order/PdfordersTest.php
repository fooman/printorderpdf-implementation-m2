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

use Fooman\PhpunitBridge\AbstractBackendController;
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
}
