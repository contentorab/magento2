<?php

namespace Contentor\LocalizationApi\Model\Spi;

interface HttpRequestTransferInterface
{
    /**
     * Returns method used to place request
     *
     * @return string|int
     */
    public function getMethod();

    /**
     * Returns headers
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Returns request parameters
     *
     * @return array|string
     */
    public function getParams();

    /**
     * Returns request body
     *
     * @return array|string
     */
    public function getBody();

    /**
     * Returns URI
     *
     * @return string
     */
    public function getUri(): string;
}
