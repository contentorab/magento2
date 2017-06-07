<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

class CheckToken extends \Magento\Framework\App\Action\Action
{
	
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Contentor\LocalizationApi\Helper\ContentorAPI $contentorApi,
			array $data = []
			) {
				$this->_contentorApi = $contentorApi;
				parent::__construct($context, $data);
	}
	
    public function execute()
    {
    	$tokenAuth = $this->_contentorApi->testAuth();
    	$this->getResponse()->setBody($tokenAuth);
    }
}