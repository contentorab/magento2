<?php

namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\Gateway\TestConnection as GatewayTestConnection;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Framework\Exception\LocalizedException;

class TestConnection
{

    /**
     * @var GatewayTestConnection
     */
    private $testConnection;

    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * TestConnection constructor.
     * @param GatewayTestConnection $testConnection
     * @param ConfigurationService $configurationService
     */
    public function __construct(
        GatewayTestConnection $testConnection,
        ConfigurationService $configurationService
    ) {
        $this->testConnection = $testConnection;
        $this->configurationService = $configurationService;
    }

    /**
     * Validate contentor api state
     * Do test call to check if everything ready
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        $configuration = $this->configurationService->validateConfiguration();
        if (true === $configuration['error']) {
            throw new LocalizedException(
                __(implode(
                    ',',
                    $configuration['messages']
                ))
            );
        }

        $gatewayValidation = $this->testConnection->execute();
        if (!$gatewayValidation) {
            throw new LocalizedException(
                __('Contentor service is unavailable')
            );
        }
    }
}
