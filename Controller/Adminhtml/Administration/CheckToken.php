<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Contentor\LocalizationApi\Model\Service\TestConnection;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Controller for testing API token connection to Contentor platform
 *
 * Validates the API token by attempting to connect to the Contentor service
 * and returns a success/failure message to the admin interface.
 */
class CheckToken extends Action
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var TestConnection
     */
    private $testConnection;

    /**
     * CheckToken constructor.
     *
     * @param Context $context
     * @param TestConnection $testConnection
     */
    public function __construct(
        Context $context,
        TestConnection $testConnection
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->testConnection = $testConnection;
    }

    /**
     * Test the API token connection to Contentor platform
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $errorMessage = '';
        try {
            $this->testConnection->execute();
            $result = true;
        } catch (LocalizedException $localizedException) {
            $result = false;
            $errorMessage = $localizedException->getMessage();
        }

        $message = $result
            ? __('Successfully connected to Contentor')
            : $errorMessage;
        $this->getResponse()->setBody($message);
    }
}
