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
}
