<?php

namespace Contentor\LocalizationApi\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class DeliveryExpressViewModel implements ArgumentInterface
{
    /**
     * @var array
     */
    private $deliverySpeed = [
        [
            'value' => 'normal',
            'label' => 'Normal'
        ],
        [
            'value' => 'express',
            'label' => 'Express'
        ]
    ];

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->deliverySpeed;
    }
}
