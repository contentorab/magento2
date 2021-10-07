<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class FieldDataTypeRenderer extends Select
{
    /**
     * @var array
     */
    protected $fieldDataTypes;

    /**
     * FieldDataTypeRenderer constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->fieldDataTypes = [
            'string' => 'Text',
            'html:relaxed' => 'HTML'
        ];
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->addOption('', '');
            foreach ($this->fieldDataTypes as $code => $dataType) {
                $this->addOption($code, $dataType);
            }
        }
        $this->setValue('string');
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value): FieldDataTypeRenderer
    {
        return $this->setName($value);
    }
}
