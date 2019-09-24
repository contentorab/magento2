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
}
