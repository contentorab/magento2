<?php

namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\Category;

class CategorySendContent extends AbstractCategorySendContent
{
    /**
     * @var int
     */
    protected $syncType = Category::LOCALIZED_SYNC_TYPE;

    /**
     * @var string
     */
    protected $syncName = 'localization';
}
