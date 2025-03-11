<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab;

use Contentor\LocalizationApi\Model\Category;

class ContentCreation extends AbstractTabBlock
{
    /**
     * @var int
     */
    protected $syncType = Category::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $submitLabelBtn = 'content creation';

    /**
     * @return string
     */
    public function getSubmitUrl(): string
    {
        return $this->getUrl("contentor/administration/contentcreation_postcategory/");
    }

    /**
     * Validate content creation configuration
     * @return array
     */
    public function getValidationMessages(): array
    {
        $result = $this->configurationService->validateCategoryContentCreationConfiguration();
        return $result['messages'];
    }

    /**
     * Check if isReady to show send request button
     * @return bool
     */
    public function isReady(): bool
    {
        $result = $this->configurationService->validateCategoryContentCreationConfiguration();
        return !$result['error'];
    }
}
