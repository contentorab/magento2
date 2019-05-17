<?php
namespace Contentor\LocalizationApi\Model\Spi;

/**
 * Interface ContentUpdateHandlerInterface
 */
interface ContentUpdateHandlerInterface
{
    /**
     * @param ContentEntityInterface $entity
     * @param array $data
     * @return void
     */
    public function execute(ContentEntityInterface $entity, array $data);
}
