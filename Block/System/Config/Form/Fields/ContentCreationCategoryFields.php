<?php
namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

/**
 * Class ContentCreationCategoryFields
 * @package Contentor\LocalizationApi\Block\System\Config\Form\Fields
 */
class ContentCreationCategoryFields extends \Contentor\LocalizationApi\Block\System\Config\Form\Fields\AbstractLocalizedFields
{
    /**
     * @var string
     */
    protected $_attributeRendererClass = '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\CategoryAttributeRenderer';

    /**
     * @var string
     */
    protected $_entity = 'Product';
}
