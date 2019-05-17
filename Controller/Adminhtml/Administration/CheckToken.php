<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Contentor\LocalizationApi\Model\Service\TestConnection;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CheckToken
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
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
