<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use Contentor\LocalizationApi\Model\Product;

class ProductImportReportBlock extends AbstractProductReportBlock
{
    /**
     * @var string
     */
    protected $syncType = Product::IMPORT_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page): string
    {
        return $this->getUrl(
            'contentor/reports/productimportreport',
            ['page' => $page, 'key' => $this->_request->getParam('key')]
        );
    }
}
