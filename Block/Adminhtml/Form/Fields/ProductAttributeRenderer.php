<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Catalog\Api\Data\ProductAttributeInterface;

class ProductAttributeRenderer extends AbstractAttributeRenderer
{
    /**
     * Entity type code of catalog
     * @var string
     */
    protected $entityType = ProductAttributeInterface::ENTITY_TYPE_CODE;

    /**
     * additionalOptions with $value => $label
     * @var array
     */
    protected $additionalOptions = ['productURL' => 'Product URL (url)'];
}
