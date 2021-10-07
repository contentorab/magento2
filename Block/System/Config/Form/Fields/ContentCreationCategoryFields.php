<?php

namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

use Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\CategoryAttributeRenderer;

class ContentCreationCategoryFields extends AbstractContentCreationFields
{
    /**
     * @var string
     */
    protected $_attributeRendererClass = CategoryAttributeRenderer::class;

    /**
     * @var string
     */
    protected $_entity = 'Category';
}
