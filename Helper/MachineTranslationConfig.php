<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class DeliveryExpressConfig
 */
class MachineTranslationConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
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
     * @return array
     */
    public function getConfig()
    {
        return $this->machineTranslation;
    }
}
