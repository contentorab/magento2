<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\CategoryRepositoryInterface;
use Contentor\LocalizationApi\Api\Data\CategoryInterface;
use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Api\ProductRepositoryInterface;
use Contentor\LocalizationApi\Api\TypeRepositoryInterface;
use Contentor\LocalizationApi\Model\Spi\ContentEntityInterface;
use Contentor\LocalizationApi\Model\Spi\ContentEntityLoaderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class EntityResolver
 *
 * Work with different content entities (Product,CMS..)
 * Get Content Updates API returns all request data in one response,
 * this class can help you to find needed to find related repository
 * for easy data management
 */
class EntityResolver
{
    /**
     * Static repository map
     *
     * @var array
     */
    private $repositoriesMap = [
        ProductInterface::TYPE_CODE => ProductRepositoryInterface::class,
        CategoryInterface::TYPE_CODE => CategoryRepositoryInterface::class
    ];

    /**
     * Object manager instance
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Type repository instance
     *
     * @var TypeRepositoryInterface
     */
    private $typeRepository;

    /**
     * EntityResolver constructor.
     * @param ObjectManagerInterface $objectManager
     * @param TypeRepositoryInterface $typeRepository
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        TypeRepositoryInterface $typeRepository
    ) {
        $this->objectManager = $objectManager;
        $this->typeRepository = $typeRepository;
    }

    /**
     * Find entity by Contentor ID
     *
     * @param int $contentorId
     * @return ContentEntityInterface|null
     * @throws LocalizedException
     */
    public function findByContentorId($contentorId): ?ContentEntityInterface
    {
        $type = $this->typeRepository->getByContentorId($contentorId);
        if (empty($type)) {
            // No type found - meaning we didn't send this request - return null as we can't load associated entity
            return null;
        }

        if (!isset($this->repositoriesMap[$type])) {
            throw new LocalizedException(__('Undefined content type  %1', $type));
        }

        /** @var ContentEntityLoaderInterface $repository */
        $repository = $this->objectManager->create(
            $this->repositoriesMap[$type]
        );

        return $repository->loadByContentorId(
            $contentorId
        );
    }
}
