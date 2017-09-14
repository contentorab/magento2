<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

class PostProduct extends \Magento\Framework\App\Action\Action
{
	
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Contentor\LocalizationApi\Helper\ContentorAPI $contentorApi,
			\Magento\Framework\App\Request\Http $request,
			\Magento\Catalog\Model\ProductFactory $productFactory,
			\Magento\Framework\App\ResourceConnection $resource,
			\Contentor\LocalizationApi\Helper\Data $helper
			) {
				$this->_contentorApi = $contentorApi;
				$this->_request = $request;
				$this->_productfactory = $productFactory;
				$this->_resource = $resource;
				$this->_helper = $helper;
				parent::__construct($context);
	}
	
    public function execute()
    {
    	$productId = $this->_request->getParam('productid');
    	$product = $this->_productfactory->create()->load($productId);
    	$sku = $product->getSku();
    	$sourceLocale = $this->_request->getParam('source');
    	$targetList  = $this->_request->getParam('targets');
    	foreach($targetList as $targetData) {
    		list($id, $locale) = explode(':', $targetData);
    		$targets[$id] = $locale;
    	}

    	if(!count($targets)) {
    		// No target selected
    	} elseif(in_array($sourceLocale, $targets)) {
    		// Source locale in targets
    	} else {
    		// Do your magic
    		if($fields = $this->_contentorApi->getFieldData($product, 'product')) {
    			foreach($targets as $targetID => $targetLocale) {
	    			$type = 'standard';
	    			$prevID = false;
	    			// Check for versioning
	    			if($this->_helper->getConfig('contentor_versioning/versioning/versioning_enable')) {
	    				$connection = $this->_resource->getConnection('core_read');
	    				$table = $this->_resource->getTableName('contentor_products');
	    			
	    				$query = "SELECT `contentor_id` FROM `" . $table . "` WHERE `sku` = :sku AND `target_locale` = :target_locale AND `source_locale` = :source_locale ORDER BY `sent_time` DESC";
	    				$binds = array('sku' 	=> $sku,
	    						'target_locale'	=> $targetLocale,
	    						'source_locale' => $sourceLocale
	    				);
	    				$return = $connection->fetchOne($query, $binds);
	    			
	    				if($return) {
	    					$type = 'update';
	    					$prevID = $return;
	    				}
	    			}
	    			$request = $this->_contentorApi->createRequest($sourceLocale, $targetLocale, $fields, $type, $prevID);
	    			if($contentorID = $this->_contentorApi->send($request)) {
	    				$this->_contentorApi->logSent($contentorID, $sku, $sourceLocale, $targetLocale, $targetID, $product, $type);
	    			} else {
	    				// Kasta exception, inte lyckats
	    				$contentorID = 'no id for you';
	    			}

    			}
    		}
    	}
    	
    	$this->getResponse()->setRedirect($this->_request->getParam('returnurl'))->sendResponse();
    }
}