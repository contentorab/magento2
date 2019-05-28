<?php
namespace Contentor\LocalizationApi\Model\Spi;

/**
 * Interface ContentUpdateHandlerInterface
 */
interface ContentUpdateHandlerInterface
{
    /**
     * Represent basic handler for content update,
     * diff content type should implement this interface for work with content.upgrade api
     *
     * @param ContentEntityInterface $entity
     * @param array $data
     * @return void
     */
    public function execute(ContentEntityInterface $entity, array $data);
}
