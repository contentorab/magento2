<?php
namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\TypeInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Type as TypeResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Type
 * @package Contentor\LocalizationApi\Model
 * Type model. Represent data from `contentor_type` table
 */
class Type extends AbstractModel implements TypeInterface
{
    /**
     * @void
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(TypeResource::class);
    }

    /**
     * @param  int $contentorId
     * @return void
     */
    public function setContentorId($contentorId)
    {
        $this->setData(
            self::CONTENTOR_ID,
            $contentorId
        );
    }

    /**
     * @return int
     */
    public function getContentorId()
    {
        return $this->getData(self::CONTENTOR_ID);
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->setData(
            self::TYPE,
            $type
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }
}
