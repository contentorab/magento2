<?php 
namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use \Magento\Backend\Block\Template\Context;
use \Contentor\LocalizationApi\Helper\ContentorAPI;
use \Magento\Framework\Locale\ListsInterface;
use \Magento\Store\Model\System\Store;

class BulkProducts extends \Magento\Backend\Block\Template
{
	
	public function __construct(
		Context $context,
		ContentorAPI $contentorApi,
		ListsInterface $localeList,
		Store $systemStores
		) {
		
		$this->_contentorApi = $contentorApi;
		$this->_localeList = $localeList;
		$this->_systemStores = $systemStores;
		$this->_scopeConfig = $context->getScopeConfig();
		
		parent::__construct($context);
	}
	
	public function getLocaleList()
	{
		return $this->_localeList->getOptionLocales();
	}
	
	public function getStoreViews()
	{
		return $this->_systemStores->getStoresStructure();
	}
	
	public function getStoreLocale($id)
	{
		return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id);
	}
}