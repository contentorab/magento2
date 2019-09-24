<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

/**
 * Class BulkProducts
 * @package Contentor\LocalizationApi\Block\Adminhtml\Administration
 */
class BulkProducts extends \Contentor\LocalizationApi\Block\Adminhtml\Administration\AbstractBulkProducts
{
    /**
     * @var string
     */
    protected $_btnSubmitLabel = 'Send for localization' ;

    /**
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('contentor/administration/sendbulkproducts');
    }
}
