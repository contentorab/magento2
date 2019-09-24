<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab;

/**
 * Class Localization
 * @package Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab
 */
class Localization extends \Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab\AbstractTabBlock
{
    /**
     * @var string
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
        return $this->getUrl("contentor/administration/postproduct/");
    }
}
