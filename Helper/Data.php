<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class Data
 * @package Contentor\LocalizationApi\Helper
 * @deprecated
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
