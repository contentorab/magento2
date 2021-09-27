<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab;

use Contentor\LocalizationApi\Model\Product;

class Localization extends AbstractTabBlock
{
    /**
     * @var int
     */
    protected $syncType = Product::LOCALIZED_SYNC_TYPE;

    /**
     * @var string
     */
    protected $submitLabelBtn = 'localization';

    /**
     * @return string
     */
    public function getSubmitUrl(): string
    {
        return $this->getUrl("contentor/administration/postcategory/");
    }
}
