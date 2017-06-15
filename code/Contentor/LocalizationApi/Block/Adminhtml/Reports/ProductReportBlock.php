<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use \Magento\Framework\App\ResourceConnection;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Locale\ListsInterface;
use \Magento\Store\Model\StoreRepository;
use \Magento\Catalog\Model\ProductFactory;

class ProductReportBlock extends \Magento\Framework\View\Element\Template
{
	protected $_logger;	
	
	public function __construct(Context $context, 
								ResourceConnection $resource, 
								ListsInterface $localeList,
								StoreRepository $storeRepository,
								ProductFactory $productFactory)
	{
		$this->_logger = $context->getLogger();
		$this->_resource = $resource;
		$this->_localeList = $localeList;
		$this->_storeRepository = $storeRepository;
		$this->_productFactory = $productFactory;

		parent::__construct($context);
	}

	public function getTotal()
	{
		$connection = $this->_resource->getConnection('core_read');
		$table = $this->_resource->getTableName('contentor_products');
		$query = "SELECT `sku` FROM `" . $table . "` GROUP BY `sku`, `source_locale`";
		$result = $connection->query($query);
		$total = $result->rowCount();
		 
		return $total;
	}
	
	public function getProductList($offset, $pagesize) 
	{
		$connection = $this->_resource->getConnection('core_read');
		$table = $this->_resource->getTableName('contentor_products');
		$query = "SELECT `sku`,
					GROUP_CONCAT(`target_store`, ';', `sent_time`) AS sent,
					GROUP_CONCAT(`target_store`, ';', `completed_time`) AS completed,
					`source_locale` AS source, GROUP_CONCAT(`target_store`, ';', `state`) as state
				FROM `" . $table . "`
				GROUP BY `sku`, `source_locale`
				ORDER BY `sent_time` DESC
				LIMIT " . $offset . "," . $pagesize;
		
		$productList = $connection->fetchAll($query);
		
		return $productList;
	}
	
	public function getProduct($sku)
	{
		return $this->_productFactory->create()->loadByAttribute('sku',$sku);
	}
	
	public function getStores()
	{
		return $this->_storeRepository->getList();
	}
	
	public function getStoreLocale($id)
	{
		return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id);
	}
}