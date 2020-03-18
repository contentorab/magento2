<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use \Magento\Framework\View\Element\Context;

/**
 * Class FieldTypeContentCreationRenderer
 * @package Contentor\LocalizationApi\Block\Adminhtml\Form\Fields
 */
class FieldTypeContentCreationRenderer extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * methodList
     *
     * @var array
     */
    protected $fieldTypes;

    /**
     * FieldTypeContentCreationRenderer constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->fieldTypes = [
            'context' => 'Context',
            'creatable' => 'Creatable'
        ];
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('', '');
            foreach ($this->fieldTypes as $code => $type) {
                $this->addOption($code, $type);
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
