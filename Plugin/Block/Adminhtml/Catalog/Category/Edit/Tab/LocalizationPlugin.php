<?php

namespace Contentor\LocalizationApi\Plugin\Block\Adminhtml\Catalog\Category\Edit\Tab;

use Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab\Localization;
use Contentor\LocalizationApi\ViewModel\DataViewModel;
use Contentor\LocalizationApi\ViewModel\DeliveryExpressViewModel;
use Contentor\LocalizationApi\ViewModel\MachineTranslationViewModel;
use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;

class LocalizationPlugin
{
    /**
     * @var DataViewModel
     */
    private $dataViewModel;

    /**
     * @var DeliveryExpressViewModel
     */
    private $deliveryExpress;

    /**
     * @var MachineTranslationViewModel
     */
    private $machineTranslation;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ContentCreationPlugin constructor.
     * @param DataViewModel $dataViewModel
     * @param DeliveryExpressViewModel $deliveryExpress
     * @param MachineTranslationViewModel $machineTranslation
     * @param LoggerInterface $logger
     */
    public function __construct(
        DataViewModel $dataViewModel,
        DeliveryExpressViewModel $deliveryExpress,
        MachineTranslationViewModel $machineTranslation,
        LoggerInterface $logger
    ) {
        $this->dataViewModel = $dataViewModel;
        $this->deliveryExpress = $deliveryExpress;
        $this->machineTranslation = $machineTranslation;
        $this->logger = $logger;
    }

    /**
     * @param Localization $subject
     * @return void
     */
    public function beforeToHtml(Localization $subject): void
    {
        try {
            $childBlock = $subject->getLayout()
                ->createBlock(Template::class, 'localization_delivery_speed')
                ->setTemplate('Contentor_LocalizationApi::options/delivery_speed.phtml')
                ->setData('deliveryExpressViewModel', $this->deliveryExpress);
            $subject->setChild('localization_delivery_speed', $childBlock);
        } catch (\Exception $exception) {
            $this->logger->error('Cannot create Delivery Speed block' . $exception->getMessage());
        }
        try {
            $childBlock = $subject->getLayout()
                ->createBlock(Template::class, 'localization_machine_translation')
                ->setTemplate('Contentor_LocalizationApi::options/machine_translation.phtml')
                ->setData('machineTranslationViewModel', $this->machineTranslation);
            $subject->setChild('localization_machine_translation', $childBlock);
        } catch (\Exception $exception) {
            $this->logger->error('Cannot create Machine Translation block' . $exception->getMessage());
        }
        try {
            $childBlock = $subject->getLayout()
                ->createBlock(Template::class, 'localization_product_words_amount')
                ->setTemplate('Contentor_LocalizationApi::options/product_words_amount.phtml')
                ->setData('dataViewModel', $this->dataViewModel);
            $subject->setChild('localization_product_words_amount', $childBlock);
        } catch (\Exception $exception) {
            $this->logger->error('Cannot create Product Words Amount block' . $exception->getMessage());
        }
    }
}
