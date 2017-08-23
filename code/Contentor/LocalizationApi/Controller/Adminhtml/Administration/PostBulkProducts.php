<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

class PostBulkProducts extends \Magento\Framework\App\Action\Action
{
	
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Contentor\LocalizationApi\Helper\ContentorAPI $contentorApi,
			\Magento\Framework\App\Request\Http $request,
			\Magento\Framework\Locale\ListsInterface $localeList,
			\Magento\Store\Model\System\Store $systemStores,
			\Magento\Framework\View\Result\PageFactory $pageFactory
			) {
				$this->_contentorApi = $contentorApi;
				$this->_request = $request;
				$this->_localeList = $localeList;
				$this->_systemStores = $systemStores;
				$this->_pageFactory = $pageFactory;
				//$this->_scopeConfig = $context->getScopeConfig();
				parent::__construct($context);
	}
	
    public function execute()
    {
    	// Set template
    	$resultPage = $this->_pageFactory->create();
    	$resultPage->setActiveMenu('Magento_Reports::report');
    	$resultPage->getConfig()->getTitle()->prepend(__('Contentor Bulk Products Post'));
    	 
    	return $resultPage;
    }
}