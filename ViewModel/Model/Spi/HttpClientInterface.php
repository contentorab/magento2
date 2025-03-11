<?php

namespace Contentor\LocalizationApi\Model\Spi;

interface HttpClientInterface
{
    /**
     * @param HttpRequestTransferInterface $httpRequestTransfer
     * @return array
     */
    public function sendRequest(HttpRequestTransferInterface $httpRequestTransfer): array;
}
