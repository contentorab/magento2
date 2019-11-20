<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use \Magento\Framework\View\Element\Context;
use \Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class AbstractAttributeRenderer
 * @package Contentor\LocalizationApi\Block\Adminhtml\Form\Fields
 */
class AbstractAttributeRenderer extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Allow attributes which have these frontendInput
     * @var array
     */
    protected $frontendInput = ['text', 'textarea'];

    /**
     * Allow attributes which have these backendType
     * @var array
     */
    protected $backendType = ['varchar', 'text'];
    /**
     * @var
     */
    protected $entityType;

    /**
     * @var Attribute
     */
    protected $_attributeFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var array
     */
    protected $additionalOptions = [];

    /**
     * AbstractAttributeRenderer constructor.
     * @param Context $context
     * @param Attribute $attributeFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $attributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_attributeFactory = $attributeFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {

            $attributeInfo = $this->_attributeFactory->getCollection()
                ->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, $this->getEntityTypeId());

            $this->addOption('', '');

            foreach ( $this->additionalOptions as $optionLabelValue => $optionLabel ) {
                $this->addOption($optionLabelValue, $optionLabel);
            }

            foreach ($attributeInfo as $attribute) {

                $label = $attribute->getData('frontend_label');

                $type = $attribute->getFrontendInput();

                $code = $attribute->getAttributeCode();

                $backendType = $attribute->getBackEndType();

                if ( !empty($label) && in_array($type, $this->frontendInput) && in_array($backendType, $this->backendType) ) {

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

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEntityTypeId() {
        $entityId = $this->eavConfig
            ->getEntityType($this->entityType)
            ->getEntityTypeId();

        return $entityId;
    }
}
