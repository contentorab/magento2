<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class FieldTypeRenderer extends Select
{
    /**
     * methodList
     *
     * @var array
     */
    protected $fieldTypes;

    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->fieldTypes = [
            'internal' => 'Internal',
            'context' => 'Context',
            'localizable' => 'Localizable'
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
            foreach ($this->fieldTypes as $code => $type) {
                $this->addOption($code, $type);
            }
        }
        $this->setValue('internal');
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value): FieldTypeRenderer
    {
        return $this->setName($value);
    }
}
