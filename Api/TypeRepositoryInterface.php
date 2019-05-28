<?php
namespace Contentor\LocalizationApi\Api;

/**
 * Interface ProductRepositoryInterface
 * @package Contentor\LocalizationApi\Api
 * @api
 */
interface TypeRepositoryInterface
{
    /**
     * Save Product content Type.
     *
     * @param int $contentorId
     * @param string $typeCode
     * @return Data\TypeInterface
     */
    public function saveProductContentRequest($contentorId, $typeCode = Data\ProductInterface::TYPE_CODE);

    /**
     * Returns content type code by contentorID
     *
     * @param string $contentorId
     * @return string string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByContentorId($contentorId);
}
