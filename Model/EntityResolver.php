<?php
namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Api\ProductRepositoryInterface;
use Contentor\LocalizationApi\Api\TypeRepositoryInterface;
use Contentor\LocalizationApi\Model\Spi\ContentEntityLoaderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class EntityResolver
 * @package Contentor\LocalizationApi\Model
 */
class EntityResolver
{
    private $repositoriesMap = [
        ProductInterface::TYPE_CODE => ProductRepositoryInterface::class
    ];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
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
     * @param int $contentorId
     * @return \Contentor\LocalizationApi\Model\Spi\ContentEntityInterface
     * @throws LocalizedException
     */
    public function findByContentorId($contentorId)
    {
        $type = $this->typeRepository->getByContentorId($contentorId);
        if (null === $type) {
            throw new LocalizedException(__('Undefined content type for contentorId %1', $contentorId));
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

    public function saveByContentorId($contentorId, $entity)
    {
    }
}
