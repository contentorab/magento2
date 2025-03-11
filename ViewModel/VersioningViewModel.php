<?php

namespace Contentor\LocalizationApi\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;

class VersioningViewModel implements ArgumentInterface
{
    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * @var array
     */

    private $versioning = [
        [
            'value' => 'default',
            'label' => 'default'
        ],
        [
            'value' => 'yes',
            'label' => 'Yes'
        ],
        [
            'value' => 'no',
            'label' => 'No'
        ]
    ];

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->versioning;
    }
}
