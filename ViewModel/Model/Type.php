<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\TypeInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Type as TypeResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Type
 *
 * Type model. Represent data from `contentor_type` table
 */
class Type extends AbstractModel implements TypeInterface
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(TypeResource::class);
    }

    /**
     * @inheritdoc
     */
    public function setContentorId($contentorId): void
    {
        $this->setData(
            self::CONTENTOR_ID,
            $contentorId
        );
    }

    /**
     * @inheritdoc
     */
    public function getContentorId()
    {
        return $this->getData(self::CONTENTOR_ID);
    }

    /**
     * @inheritdoc
     */
    public function setType($type): void
    {
        $this->setData(
            self::TYPE,
            $type
        );
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->getData(self::TYPE);
    }
}
