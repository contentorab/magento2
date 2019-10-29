<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

/**
 * Class ProductAttributeRenderer
 * @package Contentor\LocalizationApi\Block\Adminhtml\Form\Fields
 */
class ProductAttributeRenderer extends \Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\AbstractAttributeRenderer
{
    /**
     * Entity type code of catalog
     * @var string
     */
    protected $entityType = \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE;

    /**
     * additionalOptions with $value => $label
     * @var array
     */
    protected $additionalOptions = [
        'productURL'   => 'Product URL (url)'
    ];
}
