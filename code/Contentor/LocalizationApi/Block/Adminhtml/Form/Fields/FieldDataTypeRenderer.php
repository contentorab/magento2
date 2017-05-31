<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use \Magento\Framework\View\Element\Context;

class FieldDataTypeRenderer extends \Magento\Framework\View\Element\Html\Select {
	/**
	 * methodList
	 *
	 * @var array
	 */
	protected $fieldDataTypes;

	public function __construct(Context $context, array $data = [])
	{
				parent::__construct($context, $data);
				$this->fieldDataTypes = array('string' => 'Text', 'html:relaxed' => 'HTML');
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	public function _toHtml() {
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
	 * @param string $value
	 * @return $this
	 */
	public function setInputName($value) {
		return $this->setName($value);
	}
}