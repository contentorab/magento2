<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Reports;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Locale\ListsInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreRepository;
use Psr\Log\LoggerInterface;

class AbstractCategoryReportBlock extends Template
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
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * AbstractCategoryReportBlock constructor.
     * @param Context $context
     * @param ResourceConnection $resource
     * @param ListsInterface $localeList
     * @param StoreRepository $storeRepository
     * @param CategoryFactory $productFactory
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        ListsInterface $localeList,
        StoreRepository $storeRepository,
        CategoryFactory $productFactory,
        Http $request,
        array $data = []
    ) {
        $this->logger = $context->getLogger();
        $this->resource = $resource;
        $this->localeList = $localeList;
        $this->storeRepository = $storeRepository;
        $this->categoryFactory = $productFactory;
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
            $table = $this->resource->getTableName('contentor_category');
            $query = $connection->select()
                ->from($table, 'category_id')
                ->where('synchronize_type =?', $this->syncType)
                ->group(['category_id', 'source_locale']);
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
    public function getCategoryList($offset, $pagesize): array
    {
        $result = [];
        try {
            $connection = $this->resource->getConnection('core_read');
            $table = $this->resource->getTableName('contentor_category');
            $query = $connection->select()
                ->from(['cc' => $table], [
                    'cc.category_id',
                    'deliverySpeed' => 'cc.delivery_speed',
                    'machineTranslation' => 'cc.machine_translation',
                    'cc.attribution',
                    'sent' => "group_concat(`cc`.`target_store`, ';', `cc`.`sent_time`)",
                    'completed' => "group_concat(`cc`.`target_store`, ';', `cc`.`completed_time`)",
                    'source' => 'cc.source_locale',
                    'state' => "group_concat(`cc`.`target_store`, ';', `cc`.`state`)"
                ])
                ->where("`cc`.`synchronize_type`=?", $this->syncType)
                ->group(['category_id', 'source_locale', 'delivery_speed', 'machine_translation'])
                ->order('sent_time DESC')
                ->limit($pagesize, $offset);
            $result = $connection->fetchAll($query);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }

    /**
     * @param $id
     * @return Category
     */
    public function getCategory($id): Category
    {
        return $this->categoryFactory->create()->load($id);
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
