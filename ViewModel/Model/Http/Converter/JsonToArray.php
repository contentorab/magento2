<?php

namespace Contentor\LocalizationApi\Model\Http\Converter;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;

class JsonToArray
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * JsonToArray constructor.
     * @param Json $serializer
     */
    public function __construct(Json $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Converts gateway response to ENV structure
     *
     * @param mixed $response
     * @return array
     * @throws LocalizedException
     */
    public function convert($response): array
    {
        if (!is_string($response)) {
            throw new LocalizedException(__('Wrong response type'));
        }

        return $this->serializer->unserialize($response);
    }
}
