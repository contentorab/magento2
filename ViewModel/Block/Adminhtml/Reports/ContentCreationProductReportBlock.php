<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use Contentor\LocalizationApi\Model\Product;

class ContentCreationProductReportBlock extends AbstractProductReportBlock
{
    /**
     * @var string
     */
    protected $syncType = Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page): string
    {
        return $this->getUrl(
            'contentor/reports/contentcreation_productreport',
            ['page' => $page, 'key' => $this->_request->getParam('key')]
        );
    }
}
