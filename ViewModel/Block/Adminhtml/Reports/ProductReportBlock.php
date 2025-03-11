<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use Contentor\LocalizationApi\Model\Product;

class ProductReportBlock extends AbstractProductReportBlock
{
    /**
     * @var string
     */
    protected $syncType = Product::LOCALIZED_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page): string
    {
        return $this->getUrl(
            'contentor/reports/productreport',
            ['page' => $page, 'key' => $this->_request->getParam('key')]
        );
    }
}
