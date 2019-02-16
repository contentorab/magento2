<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use \Magento\Framework\View\Element\Context;
use \Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class ProductAttributeRenderer extends \Magento\Framework\View\Element\Html\Select
{
    protected $_attributeFactory;

    public function __construct(
        Context $context,
        Attribute $attributeFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_attributeFactory = $attributeFactory;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $allowed = ['text', 'textarea'];
            $attributeInfo = $this->_attributeFactory->getCollection()
                ->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, 4);
            $this->addOption('', '');
            $this->addOption('productURL', 'Product URL (url)');
            foreach ($attributeInfo as $attribute) {
                $label = $attribute->getData('frontend_label');
                $type = $attribute->getFrontendInput();
                $code = $attribute->getAttributeCode();
                if (!empty($label) && in_array($type, $allowed)) {
                    $name = $label . ' (' . $type . ')';
                    $this->addOption($code, $name);
                }
            }
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param  string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
