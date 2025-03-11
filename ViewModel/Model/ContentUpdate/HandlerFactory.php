<?php
namespace Contentor\LocalizationApi\Model\ContentUpdate;

use Contentor\LocalizationApi\Model\Spi\ContentUpdateHandlerInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class HandlerFactory
 * @package Contentor\LocalizationApi\Model\ContentUpdate
 */
class HandlerFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Standard M2 factory. Create handler instance by class name
     * and check ContentUpdateHandlerInterface support
     *
     * @param string $className
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return ContentUpdateHandlerInterface
     */
    public function create($className, array $arguments = [])
    {
        $model =  $this->objectManager->create($className, $arguments);
        if (!$model instanceof ContentUpdateHandlerInterface) {
            $message = __(
                'Type "%s" is not an instance of "%s"',
                $className,
                ContentUpdateHandlerInterface::class
            );
            throw new \InvalidArgumentException(
                $message
            );
        }
        return $model;
    }
}
