<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab;

/**
 * Class ContentCreation
 * @package Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab
 */
class ContentCreation extends \Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab\AbstractTabBlock
{
    /**
     * @var string
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $_submitLabelBtn = 'content creation';

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl("contentor/administration/contentcreation_postproduct/");
    }
}
