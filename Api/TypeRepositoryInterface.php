<?php
namespace Contentor\LocalizationApi\Api;

/**
 * Interface ProductRepositoryInterface
 * @package Contentor\LocalizationApi\Api
 */
interface TypeRepositoryInterface
{
    /**
     * @param int $contentorId
     * @param string $typeCode
     * @return Data\TypeInterface
     */
    public function saveProductContentRequest($contentorId, $typeCode = Data\ProductInterface::TYPE_CODE);

    /**
     * @param string $contentorId
     * @return string string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByContentorId($contentorId);
}
