<?php
namespace Contentor\LocalizationApi\Service;

/**
 * Class GetAttributeConfigService
 * @package Contentor\LocalizationApi\Service
 */
class GetAttributeConfigService
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;


    /**
     * GetAttributeConfigService constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request              = $request;
    }

    /**
     * Override value of word amounts which are sent from request.
     * Service getting word amount config from request
     * @return array
     */
    public function execute() {

        $configAttributes = $this->request->getParam('attributesConfig');

        return $configAttributes;
    }
}
