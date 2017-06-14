<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

class PostBulkProducts extends \Magento\Framework\App\Action\Action
{
	
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Contentor\LocalizationApi\Helper\ContentorAPI $contentorApi,
			\Magento\Framework\App\Request\Http $request,
			\Magento\Catalog\Model\ProductFactory $productFactory
			) {
				$this->_contentorApi = $contentorApi;
				$this->_request = $request;
				$this->_productfactory = $productFactory;
				parent::__construct($context);
	}
	
    public function execute()
    {

    	
    	$this->getResponse()->setBody('Done');
    	//$this->getResponse()->setRedirect()->sendResponse();
    }
}