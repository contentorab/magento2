<?php
namespace Contentor\LocalizationApi\Service;

/**
 * Class GetWordAmountConfigService
 * @package Contentor\LocalizationApi\Service
 */
class GetWordAmountConfigService
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * GetWordAmountConfigService constructor.
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

        $response = [];

        $configWordsAmount = $this->request->getParam('attributesConfig');

        if ( !empty($configWordsAmount) ) {
            foreach ($configWordsAmount as $arrayAttributeConfig) {
                foreach ( $arrayAttributeConfig as $attribute => $value ) {
                    $response[$attribute] = $value;
                }
            }
        }
        return $response;
    }
}
