<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class MachineTranslationConfig
 *
 * Helper for machine translation configuration options
 */
class MachineTranslationConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Available machine translation options
     *
     * @var array
     */
    private $machineTranslation= [
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
     * Get machine translation configuration options
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->machineTranslation;
    }
}
