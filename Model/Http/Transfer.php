<?php

namespace Contentor\LocalizationApi\Model\Http;

use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface;

class Transfer implements HttpRequestTransferInterface
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $params;

    /**
     * @var array|string
     */
    private $body;

    /**
     * @var string
     */
    private $uri;

    /**
     * Transfer constructor.
     * @param array $headers
     * @param string $body
     * @param array $params
     * @param string $method
     * @param string $uri
     */
    public function __construct(
        array $headers,
        $body,
        array $params,
        $method,
        $uri
    ) {
        $this->headers = $headers;
        $this->body = $body;
        $this->params = $params;
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * @return string|int
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}
