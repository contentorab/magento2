<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

/**
 * Class ProductReportBlock
 * @package Contentor\LocalizationApi\Block\Adminhtml\Reports
 */
class ProductReportBlock extends \Contentor\LocalizationApi\Block\Adminhtml\Reports\AbstractProductReportBlock
{
    /**
     * @var string
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::LOCALIZED_SYNC_TYPE;

    /**
     * @param $page
     * @return string
     */
    public function getPage($page) {
        return $this->getUrl('contentor/reports/productreport', [ 'page' => $page, 'key' => $this->_request->getParam('key') ]);
    }
}
