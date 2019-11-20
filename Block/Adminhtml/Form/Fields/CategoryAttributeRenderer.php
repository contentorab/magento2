<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

/**
 * Class CategoryAttributeRenderer
 * @package Contentor\LocalizationApi\Block\Adminhtml\Form\Fields
 */
class CategoryAttributeRenderer extends \Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\AbstractAttributeRenderer
{
    /**
     * Entity type code of catalog
     * @var string
     */
    protected $entityType = \Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE;

    /**
     * additionalOptions with $value => $label
     * @var array
     */
    protected $additionalOptions = [];
}
