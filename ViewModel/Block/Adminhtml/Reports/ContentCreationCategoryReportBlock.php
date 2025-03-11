<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use Contentor\LocalizationApi\Model\Category;

class ContentCreationCategoryReportBlock extends AbstractCategoryReportBlock
{
    /**
     * @var string
     */
    protected $syncType = Category::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page): string
    {
        return $this->getUrl(
            'contentor/reports/contentcreation_categoryreport',
            ['page' => $page, 'key' => $this->_request->getParam('key')]
        );
    }
}
