<?php

namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\Category;

class ContentCreationCategorySendContent extends AbstractCategorySendContent
{
    /**
     * @var int
     */
    protected $syncType = Category::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $syncName = 'content creation';
}
