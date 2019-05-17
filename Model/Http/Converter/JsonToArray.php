<?php
namespace Contentor\LocalizationApi\Model\Http\Converter;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class JsonToArray
 * @package Contentor\LocalizationApi\Model\Http\Converter
 */
class JsonToArray
{
    /**
     * Converts gateway response to ENV structure
     *
     * @param mixed $response
     * @return array
     * @throws LocalizedException
     */
    public function convert($response)
    {
        if (!is_string($response)) {
            throw new LocalizedException(__('Wrong response type'));
        }

        return json_decode($response, true);
    }
}
