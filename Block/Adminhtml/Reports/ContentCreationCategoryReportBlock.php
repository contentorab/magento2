<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

/**
 * Class ContentCreationCategoryReportBlock
 * @package Contentor\LocalizationApi\Block\Adminhtml\Reports
 */
class ContentCreationCategoryReportBlock  extends \Contentor\LocalizationApi\Block\Adminhtml\Reports\AbstractCategoryReportBlock
{
    /**
     * @var string
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Category::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page) {
        return $this->getUrl('contentor/reports/contentcreation_categoryreport', [ 'page' => $page, 'key' => $this->_request->getParam('key') ]);
    }
}
