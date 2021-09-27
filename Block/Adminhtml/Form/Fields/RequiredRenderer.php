<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Framework\View\Element\AbstractBlock;

class RequiredRenderer extends AbstractBlock
{
    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        $column = $this->getColumn();
        $return = '<input type="checkbox" value="1" <%= option_extra_attrs.required %> ' .
            ' name="groups[fieldDetails][fields][productFieldDetails][value][<%- _id %>][required]"' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') .
            ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';

        return $return;
    }
}
