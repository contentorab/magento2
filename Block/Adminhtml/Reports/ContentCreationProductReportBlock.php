<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

/**
 * Class ContentCreationProductReportBlock
 * @package Contentor\LocalizationApi\Block\Adminhtml\Reports
 */
class ContentCreationProductReportBlock  extends \Contentor\LocalizationApi\Block\Adminhtml\Reports\AbstractProductReportBlock
{
    /**
     * @var string
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page) {
        return $this->getUrl('contentor/reports/contentcreation_productreport', [ 'page' => $page, 'key' => $this->_request->getParam('key') ]);
    }
}
