<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

/**
 * Class BulkProducts
 * @package Contentor\LocalizationApi\Block\Adminhtml\Administration
 */
class BulkProducts extends \Contentor\LocalizationApi\Block\Adminhtml\Administration\AbstractBulkProducts
{
    /**
     * @var int
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::LOCALIZED_SYNC_TYPE;

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

    /**
     * @return array
     */
    public function getValidationMessages()
    {
        $result = $this->configurationService->validateConfiguration();
        return $result['messages'];
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        $result = $this->configurationService->validateConfiguration();
        return !$result['error'];
    }

}
