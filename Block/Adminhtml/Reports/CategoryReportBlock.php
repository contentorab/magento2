<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

/**
 * Class CategoryReportBlock
 * @package Contentor\LocalizationApi\Block\Adminhtml\Reports
 */
class CategoryReportBlock extends \Contentor\LocalizationApi\Block\Adminhtml\Reports\AbstractCategoryReportBlock
{
    /**
     * @var string
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Category::LOCALIZED_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page) {
        return $this->getUrl('contentor/reports/categoryreport', [ 'page' => $page, 'key' => $this->_request->getParam('key') ]);
    }
}
