<?php

namespace Contentor\LocalizationApi\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;

class MachineTranslationViewModel implements ArgumentInterface
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

    private $machineTranslation = [
        [
            'value' => 'none',
            'label' => 'None'
        ],
        [
            'value' => 'only-automatic',
            'label' => 'Only Automatic'
        ],
        [
            'value' => 'with-post-editing',
            'label' => 'With Post Editing'
        ]
    ];

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->machineTranslation;
    }
    public function isVersioningEnabled()
    {
        if ($this->configurationService->isVersioningEnabled()) {
            return true;
        }
    }
}
