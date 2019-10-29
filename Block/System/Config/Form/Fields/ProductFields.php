<?php
namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

/**
 * Class ProductFields
 * @package Contentor\LocalizationApi\Block\System\Config\Form\Fields
 */
class ProductFields extends \Contentor\LocalizationApi\Block\System\Config\Form\Fields\AbstractLocalizedFields
{
    /**
     * @var string
     */
    protected $_entity = 'Product';

    /**
     * Class rendering attribute for products
     * @var string
     */
    protected $_attributeRendererClass = '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\ProductAttributeRenderer';
}
