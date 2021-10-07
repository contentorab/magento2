<?php

namespace Contentor\LocalizationApi\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class DataViewModel implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var array
     */
    private $types = [
        [
            'label' => 'Creatable',
            'value' => 'creatable'
        ],
        [
            'label' => 'Context',
            'value' => 'context'
        ],
    ];

    /**
     * DataViewModel constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $configPath
     * @return mixed
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    public function getFieldTypes(): array
    {
        return $this->types;
    }
}
