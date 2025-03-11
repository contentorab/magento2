<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Catalog\Api\Data\CategoryAttributeInterface;

class CategoryAttributeRenderer extends AbstractAttributeRenderer
{
    /**
     * Entity type code of catalog
     * @var string
     */
    protected $entityType = CategoryAttributeInterface::ENTITY_TYPE_CODE;

    /**
     * additionalOptions with $value => $label
     * @var array
     */
    protected $additionalOptions = [];
}
