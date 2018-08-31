<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreRepository;

class SourceRenderer extends \Magento\Framework\View\Element\Html\Select {
	/**
	 * methodList
	 *
	 * @var array
	 */
	protected $_storeRepository;
	protected $_scopeConfig;
	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\View\Element\Context $context
	 * @param \Magento\Braintree\Model\System\Config\Source\Country $countrySource
	 * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
	 * @param array $data
	 */
	public function __construct(Context $context, StoreRepository $storeRepository, array $data = [])
	{
				parent::__construct($context, $data);
				$this->_storeRepository = $storeRepository;
				$this->_scopeConfig = $context->getScopeConfig();
	}
	/**
	 * Returns countries array
	 *
	 * @return array
	 */
	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	public function _toHtml() {
		if (!$this->getOptions()) {
			$stores = $this->_storeRepository->getList();
			$this->addOption('', '');
			foreach ($stores as $store) {
				if($store->getId()) {
					$locale = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
					$this->addOption($store->getId(), $store->getName());
				}
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