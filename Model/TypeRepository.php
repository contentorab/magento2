<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Api\Data\TypeInterface;
use Contentor\LocalizationApi\Api\Data\TypeInterfaceFactory;
use Contentor\LocalizationApi\Api\TypeRepositoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Type;

/**
 * Class TypeRepository
 *
 * @api
 * Allow to save different contentorId->type relations.
 * We need to know what type of content linked to ContentorId.
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
     * @inheritdoc
     */
    public function saveContentRequest($contentorId, $typeCode = ProductInterface::TYPE_CODE): TypeInterface
    {
        $type = $this->typeInterfaceFactory->create();
        $type->setContentorId($contentorId);
        $type->setType($typeCode);
        $this->typeResource->save($type);
        return $type;
    }

    /**
     * @inheritdoc
     */
    public function getByContentorId($contentorId): string
    {
        return $this->typeResource->getByContentorId($contentorId);
    }
}
