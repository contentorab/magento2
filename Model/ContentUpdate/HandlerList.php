<?php
namespace Contentor\LocalizationApi\Model\ContentUpdate;

use Contentor\LocalizationApi\Model\ContentUpdate\Handler\Product;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class HandlerList
 * @package Contentor\LocalizationApi\Model\ContentUpdate
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
     * @param HandlerFactory $handlerFactory
     * @param array $config
     */
    public function __construct(
        Product $product
    ) {
        $this->handlers = [
            'product' => $product
        ];
    }

    /**
     * Returns related handler by code see di.xml for available handlers
     * @see di.xml for available handlers
     * @param string $code
     * @param array $arguments
     * @return \Contentor\LocalizationApi\Model\Spi\ContentUpdateHandlerInterface
     * @throws LocalizedException
     */
    public function getHandlerByCode($code, array $arguments = [])
    {
        $handler = $this->handlers[$code];
        if(empty($handler)) {
            throw  new LocalizedException(__('Handler with code %1 is not defined', $code));
        }

        return $handler;
    }
}

