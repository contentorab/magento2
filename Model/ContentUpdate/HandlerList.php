<?php
namespace Contentor\LocalizationApi\Model\ContentUpdate;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class HandlerList
 * @package Contentor\LocalizationApi\Model\ContentUpdate
 */
class HandlerList
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var HandlerFactory
     */
    private $handlerFactory;

    /**
     * HandlerList constructor.
     * @param HandlerFactory $handlerFactory
     * @param array $config
     */
    public function __construct(
        HandlerFactory $handlerFactory,
        $config = []
    ) {
        $this->config = $config;
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @param string $code
     * @param array $arguments
     * @return \Contentor\LocalizationApi\Model\Spi\ContentUpdateHandlerInterface
     * @throws LocalizedException
     */
    public function getHandlerByCode($code, array $arguments = [])
    {
        foreach ($this->config as $handlerCode => $handlerClass) {
            if ($handlerCode === $code) {
                return $this->handlerFactory->create($handlerClass, $arguments);
            }
        }

        throw  new LocalizedException(__('Handler with code %1 is not defined', $code));
    }
}

