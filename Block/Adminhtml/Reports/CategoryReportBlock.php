<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use Contentor\LocalizationApi\Model\Category;

class CategoryReportBlock extends AbstractCategoryReportBlock
{
    /**
     * @var string
     */
    protected $syncType = Category::LOCALIZED_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page): string
    {
        return $this->getUrl(
            'contentor/reports/categoryreport',
            ['page' => $page, 'key' => $this->_request->getParam('key')]
        );
    }
}
