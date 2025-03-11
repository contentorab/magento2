<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Controller\ResultFactory;

/**
 * Adds import of products to the message queue
 */
class ProductImport extends Action
{
    const QUEUE_NAME = 'contentor.product_import';

    private PublisherInterface $publisher;

    private Json $serializer;

    public function __construct(
        PublisherInterface $publisher,
        Json $serializer,
        Context $context
    ) {
        $this->publisher = $publisher;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $retryFailed = $this->getRequest()->getParam('retry_failed', false);
        $instruction = ['retry_failed' => $retryFailed];

        $this->publisher->publish(
            self::QUEUE_NAME,
            $this->serializer->serialize($instruction)
        );

        $notification = 'Product import is now queued.';
        if ($retryFailed) {
            $notification = 'Retry of previously failed imports is now queued.';
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result->setData([
            'success' => true,
            'message' => $notification,
        ]);

        return $result;
    }
}
