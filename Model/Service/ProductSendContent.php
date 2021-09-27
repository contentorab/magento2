<?php

namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\Product;

class ProductSendContent extends AbstractProductSendContent
{
    /**
     * @var int
     */
    protected $syncType = Product::LOCALIZED_SYNC_TYPE;

    /**
     * @var string
     */
    protected $syncName = 'localization';
}
