<?php

namespace Contentor\LocalizationApi\Model\Http;

use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface;

/**
 * Class Transfer
 *
 * HTTP request transfer implementation
 */
class Transfer implements HttpRequestTransferInterface
{
    /**
     * Request headers
     *
     * @var array
     */
    private $headers;

    /**
     * HTTP method
     *
     * @var string
     */
    private $method;

    /**
     * Request parameters
     *
     * @var array
     */
    private $params;

    /**
     * Request body
     *
     * @var array|string
     */
    private $body;

    /**
     * Request URI
     *
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
     * Get HTTP method
     *
     * @return string|int
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get request headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get request body
     *
     * @return array|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get request parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get request URI
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}
