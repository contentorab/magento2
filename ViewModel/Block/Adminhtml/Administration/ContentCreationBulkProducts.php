<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use Contentor\LocalizationApi\Model\Product;

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
     * @return string
     */
    public function getSendUrl(): string
    {
        return $this->getUrl('contentor/administration/contentcreation_sendbulkproducts');
    }

    /**
     * Validate content creation configuration
     * @return array
     */
    public function getValidationMessages(): array
    {
        $result = $this->configurationService->validateContentCreationConfiguration();
        return $result['messages'];
    }

    /**
     * Check if isReady to show send request button
     * @return bool
     */
    public function isReady(): bool
    {
        $result = $this->configurationService->validateContentCreationConfiguration();
        return !$result['error'];
    }
}
