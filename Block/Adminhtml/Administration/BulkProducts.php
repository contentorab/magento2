<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use Contentor\LocalizationApi\Model\Product;

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
     * @return string
     */
    public function getSendUrl(): string
    {
        return $this->getUrl('contentor/administration/sendbulkproducts');
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
}
