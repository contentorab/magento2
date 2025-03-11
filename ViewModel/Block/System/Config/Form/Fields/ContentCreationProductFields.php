<?php

namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

use Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\ProductAttributeRenderer;

class ContentCreationProductFields extends AbstractContentCreationFields
{
    /**
     * @var string
     */
    protected $_attributeRendererClass = ProductAttributeRenderer::class;

    /**
     * @var string
     */
    protected $_entity = 'Product';
}
