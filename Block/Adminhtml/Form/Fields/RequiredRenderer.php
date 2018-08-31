<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

class RequiredRenderer extends \Magento\Framework\View\Element\AbstractBlock {

	protected function _toHtml()
	{
		$elId = $this->getInputId();
		$elName = $this->getInputName();
		$colName = $this->getColumnName();
		$column = $this->getColumn();
		$val = $this->getValue();
		
		$return =  '<input type="checkbox" value="1" <%= option_extra_attrs.required %> ' .
				' name="groups[fieldDetails][fields][productFieldDetails][value][<%- _id %>][required]"' .
				($column['size'] ? 'size="' . $column['size'] . '"' : '') .
				' class="' .
				(isset($column['class']) ? $column['class'] : 'input-text') . '"' .
				(isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';

		return $return;
	}

	public function setInputName($value) {
		return $this->setName($value);
	}
}