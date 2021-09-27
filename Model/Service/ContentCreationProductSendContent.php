<?php

namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\Product;

class ContentCreationProductSendContent extends AbstractProductSendContent
{
    /**
     * @var int
     */
    protected $syncType = Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $syncName = 'content creation';
}
