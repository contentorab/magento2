<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use Contentor\LocalizationApi\Model\Product;

/**
 * Block for bulk products content creation operations
 *
 * Handles the admin interface for bulk product content creation
 * operations via Contentor platform.
 */
class ContentCreationBulkProducts extends AbstractBulkProducts
{

    /**
     * @var int
     */
    protected $syncType = Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $btnSubmitLabel = 'Send for content creation';

    /**
     * Get send URL for bulk products content creation
     *
     * @return string
     */
    public function getSendUrl(): string
    {
        return $this->getUrl('contentor/administration/contentcreation_sendbulkproducts');
    }

    /**
     * Get validation messages for content creation configuration
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        $result = $this->configurationService->validateContentCreationConfiguration();
        return $result['messages'];
    }

    /**
     * Check if content creation configuration is ready
     *
     * @return bool
     */
    public function isReady(): bool
    {
        $result = $this->configurationService->validateContentCreationConfiguration();
        return !$result['error'];
    }
}
