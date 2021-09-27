<?php

namespace Contentor\LocalizationApi\Model\ContentUpdate;

use Contentor\LocalizationApi\Model\ContentUpdate\Handler\Category;
use Contentor\LocalizationApi\Model\ContentUpdate\Handler\Product;
use Contentor\LocalizationApi\Model\Spi\ContentUpdateHandlerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class HandlerList
 *
 * Work with ContentUpdateHandlerInterface handlers
 * Used for search handler by code (product, in future category and CMS)
 */
class HandlerList
{
    /**
     * @var array
     */
    private $handlers = [];

    /**
     * HandlerList constructor.
     * @param Product $product
     * @param Category $category
     */
    public function __construct(
        Product $product,
        Category $category
    ) {
        $this->handlers = [
            'product' => $product,
            'category' => $category
        ];
    }

    /**
     * Returns related handler by code see di.xml for available handlers
     * @param string $code
     * @param array $arguments
     * @return ContentUpdateHandlerInterface
     * @throws LocalizedException
     * @see di.xml for available handlers
     */
    public function getHandlerByCode($code, array $arguments = []): ContentUpdateHandlerInterface
    {
        $handler = $this->handlers[$code];
        if (empty($handler)) {
            throw  new LocalizedException(__('Handler with code %1 is not defined', $code));
        }

        return $handler;
    }
}
