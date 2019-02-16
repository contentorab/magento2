<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use \Magento\Framework\View\Element\Context;

class FieldDataTypeRenderer extends \Magento\Framework\View\Element\Html\Select
{
    protected $fieldDataTypes;

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
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('', '');
            foreach ($this->fieldDataTypes as $code => $dataType) {
                $this->addOption($code, $dataType);
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
