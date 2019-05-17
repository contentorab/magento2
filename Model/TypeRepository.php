<?php
namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Api\Data\TypeInterfaceFactory;
use Contentor\LocalizationApi\Api\TypeRepositoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Type;

/**
 * Class TypeRepository
 * @package Contentor\LocalizationApi\Model
 */
class TypeRepository implements TypeRepositoryInterface
{
    /**
     * @var TypeInterfaceFactory
     */
    private $typeInterfaceFactory;

    /**
     * @var Type
     */
    private $typeResource;

    /**
     * TypeRepository constructor.
     * @param TypeInterfaceFactory $typeInterfaceFactory
     * @param Type $typeResource
     */
    public function __construct(
        TypeInterfaceFactory $typeInterfaceFactory,
        Type $typeResource
    ) {
        $this->typeInterfaceFactory = $typeInterfaceFactory;
        $this->typeResource = $typeResource;
    }

    /**
     * @param int $contentorId
     * @param string $typeCode
     * @return \Contentor\LocalizationApi\Api\Data\TypeInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveProductContentRequest($contentorId, $typeCode = ProductInterface::TYPE_CODE)
    {
        /** @var \Contentor\LocalizationApi\Api\Data\TypeInterface $type */
        $type = $this->typeInterfaceFactory->create();
        $type->setContentorId($contentorId);
        $type->setType($typeCode);
        $this->typeResource->save($type);
        return $type;
    }

    /**
     * @param string $contentorId
     * @return string string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByContentorId($contentorId)
    {
        return $this->typeResource->getByContentorId($contentorId);
    }
}
