<?php

namespace Contentor\LocalizationApi\Service;

use Magento\Framework\App\RequestInterface;

class GetAttributeConfigService
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * GetAttributeConfigService constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Override value of word amounts which are sent from request.
     * Service getting word amount config from request
     * @return array
     */
    public function execute(): array
    {
        return $this->request->getParam('attributesConfig');
    }
}
