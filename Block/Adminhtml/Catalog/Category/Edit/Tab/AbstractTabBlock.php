<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\System\Store;

/**
 * Class AbstractTabBlock
 *
 * Abstract base class for category edit tab blocks
 */
abstract class AbstractTabBlock extends Template
{
    /**
     * Template file path
     *
     * @var string
     */
    protected $_template = 'category/edit/contentor_tab.phtml';

    /**
     * Sync type
     *
     * @var int
     */
    protected $syncType;

    /**
     * Submit button label
     *
     * @var string
     */
    protected $submitLabelBtn;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Resolver instance
     *
     * @var mixed
     */
    protected $resolver;

    /**
     * System store model
     *
     * @var Store
     */
    protected $systemStores;

    /**
     * Scope configuration
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Resource connection
     *
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Locale list interface
     *
     * @var ListsInterface
     */
    protected $localeList;

    /**
     * Configuration service
     *
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
        ResourceConnection $resource,
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
     * Retrieve category
     *
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * Get available locales
     *
     * @return array
     */
    public function getLocales(): array
    {
        return $this->localeList->getOptionLocales();
    }

    /**
     * Get store views
     *
     * @return array
     */
    public function getStoreViews(): array
    {
        return $this->systemStores->getStoresStructure();
    }

    /**
     * Get store locale by ID
     *
     * @param string $id
     * @return string
     */
    public function getStoreLocale($id): string
    {
        return $this->_scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORE,
            $id
        );
    }

    /**
     * Get request status by ID
     *
     * @param string $id
     * @return array
     */
    public function getRequestStatus($id): array
    {
        $status = [];
        try {
            $connection = $this->resource->getConnection('core_read');
            $statusTable = $this->resource->getTableName('contentor_status');
            $categoryTable = $this->resource->getTableName('contentor_category');
            $query = $connection->select()
                ->from(
                    ['cc' => $categoryTable],
                    [
                        'cc.contentor_id',
                        'cc.target_store',
                        'cc.sent_time',
                        'cc.completed_time',
                        'cc.canceled_time',
                        'cs.status_time',
                        'cs.status',
                        'cc.state',
                        'cc.type'
                    ]
                )->joinLeft(['cs' => $statusTable], 'cc.contentor_id = cs.contentor_id')
                ->where('cc.category_id =:this_category_id AND synchronize_type = :this_synchronize_type')
                ->order('cc.contentor_id DESC, cs.status_time');
            $binds = [
                'this_category_id' => $id,
                'this_synchronize_type' => $this->syncType
            ];
            $status = $connection->fetchAll($query, $binds);
        } catch (Exception $e) {
            $this->_logger->error($e->getMessage());
        }
        return $status;
    }

    /**
     * Get validation messages
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        $result = $this->configurationService->validateCategoryConfiguration();
        return $result['messages'];
    }

    /**
     * Check if ready for submission
     *
     * @return bool
     */
    public function isReady(): bool
    {
        $result = $this->configurationService->validateCategoryConfiguration();
        return !$result['error'];
    }

    /**
     * Get submit button label
     *
     * @return string
     */
    public function getLabelSubmitButton(): string
    {
        return $this->submitLabelBtn;
    }

    /**
     * Get sync type
     *
     * @return int
     */
    public function getSyncType()
    {
        return $this->syncType;
    }

    /**
     * Get submit URL
     *
     * @return string
     */
    abstract public function getSubmitUrl(): string;
}
