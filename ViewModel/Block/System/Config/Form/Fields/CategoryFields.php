<?php

namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

use Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\CategoryAttributeRenderer;

class CategoryFields extends AbstractLocalizedFields
{
    /**
     * @var string
     */
    protected $_entity = 'Category';

    /**
     * Class rendering attribute for products
     * @var string
     */
    protected $_attributeRendererClass = CategoryAttributeRenderer::class;
}
