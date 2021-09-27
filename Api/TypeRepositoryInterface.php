<?php

namespace Contentor\LocalizationApi\Api;

use Contentor\LocalizationApi\Api\Data\TypeInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ProductRepositoryInterface
 * @api
 */
interface TypeRepositoryInterface
{
    /**
     * Save Product content Type.
     * @param $contentorId
     * @param string $typeCode
     * @return TypeInterface
     */
    public function saveContentRequest($contentorId, $typeCode = Data\ProductInterface::TYPE_CODE): TypeInterface;

    /**
     * Returns content type code by contentorID
     *
     * @param string $contentorId
     * @return string string
     * @throws LocalizedException
     */
    public function getByContentorId($contentorId): string;
}
