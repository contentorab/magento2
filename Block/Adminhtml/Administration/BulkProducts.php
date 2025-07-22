<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use Contentor\LocalizationApi\Model\Product;

/**
 * Block for bulk products localization operations
 *
 * Handles the admin interface for bulk product localization
 * operations via Contentor platform.
 */
class BulkProducts extends AbstractBulkProducts
{
    /**
     * @var int
     */
    protected $syncType = Product::LOCALIZED_SYNC_TYPE;

    /**
     * @var string
     */
    protected $btnSubmitLabel = 'Send for localization';

    /**
     * Get send URL for bulk products localization
     *
     * @return string
     */
    public function getSendUrl(): string
    {
        return $this->getUrl('contentor/administration/sendbulkproducts');
    }

    /**
     * Get validation messages for configuration
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        $result = $this->configurationService->validateConfiguration();
        return $result['messages'];
    }

    /**
     * Check if configuration is ready
     *
     * @return bool
     */
    public function isReady(): bool
    {
        $result = $this->configurationService->validateConfiguration();
        return !$result['error'];
    }
}
