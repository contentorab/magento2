<?php

namespace Contentor\LocalizationApi\Service;

use InvalidArgumentException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigurationService
 *
 * Contains all accessors to configurable extension options (api credentials, general settings)
 */
class ConfigurationService
{
    /**
     * Admin config XML Path constants
     *
     * @var string
     */
    public const XML_PATH_STATUS = 'contentor_options/status/enable';
    public const XML_PATH_TOKEN = 'contentor_options/token/apitoken';
    public const XML_PATH_AUTOMATION_ENABLE = 'contentor_options/automation/import';
    public const XML_PATH_SOURCE_LOCALE = 'contentor_options/source/sourcelocale';
    public const XML_PATH_TARGET_STORE_VIEWS = 'contentor_options/targets/targetviews';
    public const XML_PATH_PRODUCT_FIELDS = 'contentor_options/fieldDetails/productFieldDetails';
    public const XML_PATH_DEVELOPER_MODE = 'contentor_options/fieldDetails/developer_mode';
    public const XML_PATH_API_BASE_URL = 'contentor_options/contentor_options/api_base_url';
    public const XML_PATH_MODULE_VERSION = 'contentor_options/contentor_options/version';
    public const XML_PATH_VERSIONING_ENABLED = 'contentor_options/versioning/versioning_enable';
    public const XML_PATH_MAIN_LOCALE = 'general/locale/code';
    public const XML_PATH_CONTENT_CREATION_FIELDS = 'contentor_options/contentCreation/fieldDetails';

    /**
     * Category config XML Path constants
     *
     * @var string
     */
    public const XML_PATH_CATEGORY_AUTOMATION_ENABLE = 'contentor_options/automation/category_import';
    public const XML_PATH_CATEGORY_SOURCE_LOCALE = 'contentor_options/source/category_sourcelocale';
    public const XML_PATH_CATEGORY_TARGET_STORE_VIEWS = 'contentor_options/targets/category_targetviews';
    public const XML_PATH_CATEGORY_FIELDS = 'contentor_options/fieldDetails/categoryFieldDetails';
    public const XML_PATH_CATEGORY_CONTENT_CREATION_FIELDS = 'contentor_options/contentCreation/categoryFieldDetails';

    /**
     * Scope configuration interface
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * JSON serializer
     *
     * @var Json
     */
    private $serializer;

    /**
     * ConfigurationService constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * Get module version
     *
     * @param null $store
     * @return mixed
     */
    public function getVersion($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MODULE_VERSION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns module status enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isModuleEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STATUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Automation enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isAutomationEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_AUTOMATION_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns category automation enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isCategoryAutomationEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CATEGORY_AUTOMATION_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns versioning enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isVersioningEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_VERSIONING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return is developer mode enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isDeveloperMode($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DEVELOPER_MODE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Main locale from magento config
     *
     * @param null|int|string $store
     * @return mixed
     */
    public function getMainLocale($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAIN_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Basic config validation.
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateConfiguration($store = null): array
    {
        $result = [
            'error' => false,
            'messages' => []
        ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getProductFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Product Attribute configuration';
        }

        if (empty($this->getSourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Source locale';
        }

        if (empty($this->getTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Target store views';
        }

        return $result;
    }

    /**
     * Returns token from config
     *
     * @param string|null $store
     * @return mixed
     */
    public function getToken($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TOKEN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Contentor API url from magento config
     *
     * @param string|null $store
     * @return mixed
     */
    public function getApiBaseUrl($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_BASE_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Product attributes map from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getProductFields($store = null): array
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        return $this->serializer->unserialize($data);
    }

    /**
     * Returns Source locale from magento config
     *
     * @param string|null $store
     * @return mixed
     */
    public function getSourceLocale($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SOURCE_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Product target store views from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getTargetStoreViews($store = null): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TARGET_STORE_VIEWS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return explode(',', $value ?? '');
    }

    /**
     * Basic config validation for category
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateCategoryConfiguration($store = null): array
    {
        $result = [
            'error' => false,
            'messages' => []
        ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getCategoryFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Attribute configuration';
        }

        if (empty($this->getCategorySourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Source locale';
        }

        if (empty($this->getCategoryTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Category Target store views';
        }

        return $result;
    }

    /**
     * Returns category attributes map from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getCategoryFields($store = null): array
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        return $this->serializer->unserialize($data);
    }

    /**
     * Returns Category Source locale from magento config
     *
     * @param string|null $store
     * @return mixed
     */
    public function getCategorySourceLocale($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_SOURCE_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Category target store views from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getCategoryTargetStoreViews($store = null): array
    {
        return (array)$this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_TARGET_STORE_VIEWS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Basic config validation.
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateContentCreationConfiguration($store = null): array
    {
        $result = [
            'error' => false,
            'messages' => []
        ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getContentCreationProductFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Product Attribute configuration - content creation';
        }

        if (empty($this->getSourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Source locale - content creation';
        }

        if (empty($this->getTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Target store views - content creation';
        }

        return $result;
    }

    /**
     * Returns Product attributes map from magento config for content creation
     *
     * @param string|null $store
     * @return array
     */
    public function getContentCreationProductFields($store = null): array
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_CONTENT_CREATION_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        return $this->serializer->unserialize($data);
    }

    /**
     * Basic config validation for category content creation
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateCategoryContentCreationConfiguration($store = null): array
    {
        $result = [
            'error' => false,
            'messages' => []
        ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getContentCreationCategoryFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Attribute configuration - content creation';
        }

        if (empty($this->getCategorySourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Source locale - content creation';
        }

        if (empty($this->getCategoryTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Category Target store views - content creation';
        }

        return $result;
    }

    /**
     * Returns category attributes map from magento config for content creation
     *
     * @param string|null $store
     * @return array
     */
    public function getContentCreationCategoryFields($store = null): array
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_CONTENT_CREATION_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        return $this->serializer->unserialize($data);
    }
}
