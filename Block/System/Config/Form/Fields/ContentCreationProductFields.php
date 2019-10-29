<?php
namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

/**
 * Class ContentCreationProductFields
 * @package Contentor\LocalizationApi\Block\System\Config\Form\Fields
 */
class ContentCreationProductFields extends \Contentor\LocalizationApi\Block\System\Config\Form\Fields\AbstractContentCreationFields
{
    /**
     * @var string
     */
    protected $_attributeRendererClass = '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\ProductAttributeRenderer';

    /**
     * @var string
     */
    protected $_entity = 'Product';
}
