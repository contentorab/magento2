<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab;

use Contentor\LocalizationApi\Model\Category;

/**
 * Class ContentCreation
 *
 * Block for category content creation tab
 */
class ContentCreation extends AbstractTabBlock
{
    /**
     * Sync type for content creation
     *
     * @var int
     */
    protected $syncType = Category::CONTENT_CREATION_SYNC_TYPE;

    /**
     * Submit button label
     *
     * @var string
     */
    protected $submitLabelBtn = 'content creation';

    /**
     * Get submit URL
     *
     * @return string
     */
    public function getSubmitUrl(): string
    {
        return $this->getUrl("contentor/administration/contentcreation_postcategory/");
    }

    /**
     * Validate content creation configuration
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        $result = $this->configurationService->validateCategoryContentCreationConfiguration();
        return $result['messages'];
    }

    /**
     * Check if ready to show send request button
     *
     * @return bool
     */
    public function isReady(): bool
    {
        $result = $this->configurationService->validateCategoryContentCreationConfiguration();
        return !$result['error'];
    }
}
