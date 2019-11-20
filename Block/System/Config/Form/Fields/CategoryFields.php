<?php
namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

class CategoryFields extends \Contentor\LocalizationApi\Block\System\Config\Form\Fields\AbstractLocalizedFields
{
    /**
     * @var string
     */
    protected $_entity = 'Category';

    /**
     * Class rendering attribute for products
     * @var string
     */
    protected $_attributeRendererClass = '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\CategoryAttributeRenderer';
}
