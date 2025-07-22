<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class Data
 * Helper for localization data and configuration
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    private $types = [
        [
            'label'   => 'Creatable',
            'value'   => 'creatable'
        ],
        [
            'label'   => 'Context',
            'value'   => 'context'
        ],
    ];

    /**
     * Get configuration value
     *
     * @param string $configPath
     * @return mixed
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get field types
     *
     * @return array
     */
    public function getFieldTypes()
    {
        return $this->types;
    }
}
