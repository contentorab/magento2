<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Contentor\LocalizationApi\Model\Service\ProductSendContent;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ProductFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
/**
 * Class PostProduct
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
 */
class PostProduct extends \Magento\Backend\App\Action
{
    /**
     * @var ProductSendContent
     */
    private $productSendContent;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Http
     */
    private $request;

    /**
     * PostProduct constructor.
     * @param Context $context
     * @param Http $request
     * @param ProductFactory $productFactory
     * @param Data $backendHelper
     * @param ProductSendContent $productSendContent
     */
    public function __construct(
        Context $context,
        Http $request,
        ProductFactory $productFactory,
        ProductSendContent $productSendContent
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->productSendContent = $productSendContent;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
            $request = $this->request;
            $productId = $request->getParam('productid');
            $product = $this->productFactory->create()->load($productId);
            $sourceLocale = $request->getParam('source');
            $targetList = $request->getParam('targets');
            foreach ($targetList as $targetData) {
                list($id, $locale) = explode(':', $targetData);
                $targets[$id] = $locale;
            }

            if (!count($targets)) {
                // No target selected
            } elseif (in_array($sourceLocale, $targets)) {
                // Source locale in targets
            } else {
                $this->productSendContent->execute(
                    $product,
                    $sourceLocale,
                    $targets
                );
            }

            $url = $this->getUrl(
                'catalog/product/edit',
                ['id' => $productId]
            );

        $this->getResponse()->setRedirect($url)->sendResponse();
    }
}
