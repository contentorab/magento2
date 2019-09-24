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
}
