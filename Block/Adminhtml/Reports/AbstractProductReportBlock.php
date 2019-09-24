<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use \Magento\Framework\App\ResourceConnection;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Locale\ListsInterface;
use \Magento\Store\Model\StoreRepository;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\Request\Http;

/**
 * Class AbstractProductReportBlock
 * @package Contentor\LocalizationApi\Block\Adminhtml\Reports
 */
class AbstractProductReportBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var
     */
    protected $_syncType;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var ResourceConnection
     */
    protected $_resource;
    /**
     * @var ListsInterface
     */
    protected $_localeList;
    /**
     * @var StoreRepository
     */
    protected $_storeRepository;
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * ProductReportBlock constructor.
     * @param Context $context
     * @param ResourceConnection $resource
     * @param ListsInterface $localeList
     * @param StoreRepository $storeRepository
     * @param ProductFactory $productFactory
     * @param Http $request
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        ListsInterface $localeList,
        StoreRepository $storeRepository,
        ProductFactory $productFactory,
        Http $request
    ) {
        $this->_logger = $context->getLogger();
        $this->_resource = $resource;
        $this->_localeList = $localeList;
        $this->_storeRepository = $storeRepository;
        $this->_productFactory = $productFactory;
        $this->_request = $request;

        parent::__construct($context);
    }

    /**
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    public function getTotal()
    {
        $connection = $this->_resource->getConnection('core_read');
        $table = $this->_resource->getTableName('contentor_products');
        $query = "SELECT `sku` FROM `" . $table . "` WHERE `synchronize_type` = ". $this->_syncType ." GROUP BY `sku`, `source_locale`";
        $result = $connection->query($query);
        $total = $result->rowCount();

        return $total;
    }

    /**
     * @param $offset
     * @param $pagesize
     * @return array
     */
    public function getProductList($offset, $pagesize)
    {
        $connection = $this->_resource->getConnection('core_read');
        $table = $this->_resource->getTableName('contentor_products');
        $query = "SELECT `sku`,
					GROUP_CONCAT(`target_store`, ';', `sent_time`) AS sent,
					GROUP_CONCAT(`target_store`, ';', `completed_time`) AS completed,
					`source_locale` AS source, GROUP_CONCAT(`target_store`, ';', `state`) as state
				FROM `" . $table . "` 
				WHERE `synchronize_type` = ". $this->_syncType ."
				GROUP BY `sku`, `source_locale`
				ORDER BY `sent_time` DESC
				LIMIT " . $offset . "," . $pagesize;

        $productList = $connection->fetchAll($query);

        return $productList;
    }

    /**
     * @param $sku
     * @return bool|\Magento\Catalog\Model\AbstractModel
     */
    public function getProduct($sku)
    {
        return $this->_productFactory->create()->loadByAttribute('sku', $sku);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        return $this->_storeRepository->getList();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStoreLocale($id)
    {
        return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getParameter($id)
    {
        return $this->_request->getParam($id);
    }

    /**
     * @param $page
     * @return string
     */
    public function getPage($page) {
        return $this->getUrl('contentor/reports/productreport', [ 'page' => $page ]);
    }
}
