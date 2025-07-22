<?php
namespace Contentor\LocalizationApi\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * @param $configPath
     * @return mixed
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFieldTypes()
    {
        return $this->types;
    }
}
