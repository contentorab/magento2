<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\AbstractModel;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Locale\ListsInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreRepository;
use Psr\Log\LoggerInterface;

class AbstractProductReportBlock extends Template
{
    /**
     * @var int
     */
    protected $syncType;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var ListsInterface
     */
    protected $localeList;
    /**
     * @var StoreRepository
     */
    protected $storeRepository;
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * AbstractProductReportBlock constructor.
     * @param Context $context
     * @param ResourceConnection $resource
     * @param ListsInterface $localeList
     * @param StoreRepository $storeRepository
     * @param ProductFactory $productFactory
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        ListsInterface $localeList,
        StoreRepository $storeRepository,
        ProductFactory $productFactory,
        Http $request,
        array $data = []
    ) {
        $this->logger = $context->getLogger();
        $this->resource = $resource;
        $this->localeList = $localeList;
        $this->storeRepository = $storeRepository;
        $this->productFactory = $productFactory;
        $this->_request = $request;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        $total = 0;
        try {
            $connection = $this->resource->getConnection('core_read');
            $table = $this->resource->getTableName('contentor_products');
            $query = $connection->select()
                ->from(['cp' => $table])
                ->where('synchronize_type =?', $this->syncType)
                ->group(['m2_product_id', 'source_locale']);
            $result = $connection->query($query);
            $total = $result->rowCount();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $total;
    }

    /**
     * @param $offset
     * @param $pagesize
     * @return array
     */
    public function getProductList($offset, $pagesize): array
    {
        $result = [];
        try {
            $connection = $this->resource->getConnection('core_read');
            $table = $this->resource->getTableName('contentor_products');

            $queryGroupSku = $connection->select()
                ->from(['cp' => $table], [
                    'cp.m2_product_id',
                    'cp.sku',
                    'deliverySpeed' => 'cp.delivery_speed',
                    'machineTranslation' => 'cp.machine_translation',
                    'cp.attribution',
                    'sent' => "group_concat(`cp`.`target_store`, ';', `cp`.`sent_time`)",
                    'completed' => "group_concat(`cp`.`target_store`, ';', `cp`.`completed_time`)",
                    'source' => 'cp.source_locale',
                    'state' => "group_concat(`cp`.`target_store`, ';', `cp`.`state`)"
                ])->where(
                    "`cp`.`synchronize_type`= ? AND (`cp`.`m2_product_id` IS NULL OR `cp`.`m2_product_id` = 0)",
                    $this->syncType
                )->group(['sku', 'source_locale', 'delivery_speed', 'machine_translation'])
                ->order('sent_time DESC')
                ->limit($pagesize, $offset);

            $productListGroupSku = $connection->fetchAll($queryGroupSku);
            $query = $connection->select()
                ->from(['cp' => $table], [
                    'cp.m2_product_id',
                    'cp.sku',
                    'deliverySpeed' => 'cp.delivery_speed',
                    'machineTranslation' => 'cp.machine_translation',
                    'cp.attribution',
                    'sent' => "group_concat(`cp`.`target_store`, ';', `cp`.`sent_time`)",
                    'completed' => "group_concat(`cp`.`target_store`, ';', `cp`.`completed_time`)",
                    'source' => 'cp.source_locale',
                    'state' => "group_concat(`cp`.`target_store`, ';', `cp`.`state`)"
                ])
                ->where("`cp`.`synchronize_type`= ? AND `cp`.`m2_product_id` IS NOT NULL", $this->syncType)
                ->group(['sku', 'source_locale', 'delivery_speed', 'machine_translation'])
                ->order('sent_time DESC')
                ->limit($pagesize, $offset);
            $productList = $connection->fetchAll($query);
            $result = array_merge($productList, $productListGroupSku);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $result;
    }

    /**
     * @param $id
     * @param $field
     * @return bool|AbstractModel
     */
    public function getProduct($id, $field)
    {
        return $this->productFactory->create()->loadByAttribute($field, $id);
    }

    /**
     * @return StoreInterface[]
     */
    public function getStores(): array
    {
        return $this->storeRepository->getList();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStoreLocale($id)
    {
        return $this->_scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $id);
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
    public function getPage($page): string
    {
        return $this->getUrl(
            'contentor/reports/productreport',
            ['page' => $page, 'key' => $this->_request->getParam('key')]
        );
    }
}
