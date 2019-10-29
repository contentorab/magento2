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
}
