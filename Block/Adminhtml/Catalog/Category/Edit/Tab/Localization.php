<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab;

/**
 * Class Localization
 * @package Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab
 */
class Localization extends \Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab\AbstractTabBlock
{
    /**
     * @var int
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::LOCALIZED_SYNC_TYPE;

    /**
     * @var string
     */
    protected $_submitLabelBtn = 'localization';

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl("contentor/administration/postcategory/");
    }

    /**
     * @return array
     */
    public function getValidationMessages()
    {
        return parent::getValidationMessages();
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        return parent::isReady();
    }
}
