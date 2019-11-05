<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

/**
 * Class ContentCreationBulkProducts
 * @package Contentor\LocalizationApi\Block\Adminhtml\Administration
 */
class ContentCreationBulkProducts extends \Contentor\LocalizationApi\Block\Adminhtml\Administration\AbstractBulkProducts
{

    /**
     * @var int
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $_btnSubmitLabel = 'Send for content creation' ;

    /**
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('contentor/administration/contentcreation_sendbulkproducts');
    }
}
