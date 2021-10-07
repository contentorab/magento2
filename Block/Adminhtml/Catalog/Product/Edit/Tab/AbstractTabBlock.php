<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\System\Store;
use Magento\Framework\App\ResourceConnection;

abstract class AbstractTabBlock extends Template
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/contentor_tab.phtml';

    /**
     * @var int
     */
    protected $syncType;

    /**
     * @var string
     */
    protected $submitLabelBtn;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var
     */
    protected $resolver;

    /**
     * @var Store
     */
    protected $systemStores;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var ListsInterface
     */
    protected $localeList;

    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * Localization constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ListsInterface $localeList
     * @param Store $systemStores
     * @param ResourceConnection $resource
     * @param ConfigurationService $configurationService
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ListsInterface $localeList,
        Store $systemStores,
        ResourceConnection    $resource,
        ConfigurationService $configurationService,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->localeList = $localeList;
        $this->systemStores = $systemStores;
        $this->resource = $resource;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
        $this->configurationService = $configurationService;
    }

    /**
     * Retrieve product
     *
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * @return array
     */
    public function getLocales(): array
    {
        return $this->localeList->getOptionLocales();
    }

    /**
     * @return array
     */
    public function getStoreViews(): array
    {
        return $this->systemStores->getStoresStructure();
    }

    /**
     * @param string $id
     * @return string
     */
    public function getStoreLocale($id): string
    {
        return $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $id);
    }

    /**
     * @param string $sku
     * @return array
     */
    public function getRequestStatus($sku): array
    {
        $status = [];
        try {
            $connection = $this->resource->getConnection('core_read');
            $statusTable = $this->resource->getTableName('contentor_status');
            $productTable = $this->resource->getTableName('contentor_products');
            $query = $connection->select()
                ->from(
                    ['cp' => $productTable],
                    [
                        'cp.contentor_id',
                        'cp.target_store',
                        'cp.sent_time',
                        'cp.completed_time',
                        'cp.canceled_time',
                        'cs.status_time',
                        'cs.status',
                        'cp.state',
                        'cp.type'
                    ]
                )->joinLeft(['cs' => $statusTable], 'cp.contentor_id = cs.contentor_id')
                ->where('cp.sku =:this_sku AND synchronize_type = :this_synchronize_type')
                ->order('cp.contentor_id DESC, cs.status_time');
            $binds = [
                'this_sku' => $sku,
                'this_synchronize_type' => $this->syncType
            ];
            $status = $connection->fetchAll($query, $binds);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
        return $status;
    }

    /**
     * @return array
     */
    public function getValidationMessages(): array
    {
        $result = $this->configurationService->validateConfiguration();
        return $result['messages'];
    }

    /**
     * @return bool
     */
    public function isReady(): bool
    {
        $result = $this->configurationService->validateConfiguration();
        return !$result['error'];
    }

    /**
     * @return string
     */
    public function getLabelSubmitButton(): string
    {
        return $this->submitLabelBtn;
    }

    /**
     * @return int
     */
    public function getSyncType(): int
    {
        return $this->syncType;
    }

    /**
     * @return string
     */
    abstract public function getSubmitUrl(): string;
}
