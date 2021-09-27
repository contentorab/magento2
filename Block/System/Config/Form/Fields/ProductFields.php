<?php

namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

use Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\ProductAttributeRenderer;

class ProductFields extends AbstractLocalizedFields
{
    /**
     * @var string
     */
    protected $_entity = 'Product';

    /**
     * Class rendering attribute for products
     * @var string
     */
    protected $_attributeRendererClass = ProductAttributeRenderer::class;
}
