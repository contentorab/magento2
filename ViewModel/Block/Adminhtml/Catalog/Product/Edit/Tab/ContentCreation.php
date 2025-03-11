<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Contentor\LocalizationApi\Model\Product;

class ContentCreation extends AbstractTabBlock
{
    /**
     * @var int
     */
    protected $syncType = Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $submitLabelBtn = 'content creation';

    /**
     * @return string
     */
    public function getSubmitUrl(): string
    {
        return $this->getUrl("contentor/administration/contentcreation_postproduct/");
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
