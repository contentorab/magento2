<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

class PostBulkProducts extends \Magento\Framework\App\Action\Action
{
	
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Contentor\LocalizationApi\Helper\ContentorAPI $contentorApi,
			\Magento\Framework\App\Request\Http $request,
			\Magento\Catalog\Model\ProductFactory $productFactory,
			\Magento\Framework\View\Result\PageFactory $pageFactory
			) {
				$this->_contentorApi = $contentorApi;
				$this->_request = $request;
				$this->_productfactory = $productFactory;
				$this->_pageFactory = $pageFactory;
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