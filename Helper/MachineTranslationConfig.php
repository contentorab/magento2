<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class DeliveryExpressConfig
 * @package Contentor\LocalizationApi\Helper
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
            'label' => 'Only Machine Translation'
        ],
        // [
        //     'value' => 'with-post-editing',
        //     'label' => 'Machine Translation and Rewrite'
        // ]
    ];

    /**
     * @return array
     */
    public function getConfig() {
        return $this->machineTranslation;
    }
}
