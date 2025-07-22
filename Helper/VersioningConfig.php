<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class VersioningConfig
 *
 * Helper for versioning configuration options
 */
class VersioningConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Available versioning options
     *
     * @var array
     */
    private $versioning= [
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
        ],
    ];

    /**
     * Get versioning configuration options
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->versioning;
    }
}
