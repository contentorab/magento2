<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use \Magento\Framework\App\ResourceConnection;
use \Magento\Framework\Locale\ListsInterface;
use \Magento\Store\Model\StoreRepository;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\Request\Http;

/**
 * Class AbstractProductReportBlock
 * @package Contentor\LocalizationApi\Block\Adminhtml\Reports
 */
class AbstractProductReportBlock extends \Magento\Backend\Block\Template
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
     * AbstractProductReportBlock constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ResourceConnection $resource
     * @param ListsInterface $localeList
     * @param StoreRepository $storeRepository
     * @param ProductFactory $productFactory
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ResourceConnection $resource,
        ListsInterface $localeList,
        StoreRepository $storeRepository,
        ProductFactory $productFactory,
        Http $request,
        array $data = []
    )
    {
        $this->_logger = $context->getLogger();
        $this->_resource = $resource;
        $this->_localeList = $localeList;
        $this->_storeRepository = $storeRepository;
        $this->_productFactory = $productFactory;
        $this->_request = $request;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     * @throws \Zend_Db_Statement_Exception
     */
    public function getTotal()
    {
        $connection = $this->_resource->getConnection('core_read');
        $table = $this->_resource->getTableName('contentor_products');
        $query = "SELECT `m2_product_id` FROM `" . $table . "` WHERE `synchronize_type` = ". $this->_syncType. " GROUP BY `m2_product_id`, `source_locale`";
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

        $queryGroupSku = "SELECT `m2_product_id`,
                    `sku`,
                    `delivery_speed` AS deliverySpeed,
                    `machine_translation` AS machineTranslation,
                    `attribution`,
					GROUP_CONCAT(`target_store`, ';', `sent_time`) AS sent,
					GROUP_CONCAT(`target_store`, ';', `completed_time`) AS completed,
					`source_locale` AS source, GROUP_CONCAT(`target_store`, ';', `state`) as state
				FROM `" . $table . "`
				WHERE `synchronize_type` = ". $this->_syncType ."  AND ( `m2_product_id` IS NULL OR `m2_product_id` = 0 )
				GROUP BY `sku`, `source_locale`, `delivery_speed`,`machine_translation`
				ORDER BY `sent_time` DESC
				LIMIT " . $offset . "," . $pagesize;

        $productListGroupSku = $connection->fetchAll($queryGroupSku);

        $query = "SELECT `m2_product_id`,
                    `sku`,
                    `delivery_speed` AS deliverySpeed,
                    `machine_translation` AS machineTranslation,
                    `attribution`,
					GROUP_CONCAT(`target_store`, ';', `sent_time`) AS sent,
					GROUP_CONCAT(`target_store`, ';', `completed_time`) AS completed,
					`source_locale` AS source, GROUP_CONCAT(`target_store`, ';', `state`) as state
				FROM `" . $table . "`
				WHERE `synchronize_type` = ". $this->_syncType ."  AND `m2_product_id` IS NOT NULL
				GROUP BY `sku`, `source_locale`, `delivery_speed`,`machine_translation`
				ORDER BY `sent_time` DESC
				LIMIT " . $offset . "," . $pagesize;

        $productList = $connection->fetchAll($query);

        $productList  = array_merge($productList, $productListGroupSku);

        return $productList;
    }

    /**
     * @param $id
     * @param $field
     * @return mixed
     */
    public function getProduct($id, $field)
    {
        return $this->_productFactory->create()->loadByAttribute($field, $id);
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
        return $this->getUrl('contentor/reports/productreport', [ 'page' => $page, 'key' => $this->_request->getParam('key') ]);
    }
}
