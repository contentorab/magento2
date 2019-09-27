<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Catalog\Model\ProductFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;

/**
 * Class AbstractPostProduct
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
 */
class AbstractPostProduct extends \Magento\Backend\App\Action
{
    /**
     * @var array
     */
    protected $productSendContent;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Http
     */
    protected $request;

    /**
     * AbstractPostProduct constructor.
     * @param Context $context
     * @param Http $request
     * @param ProductFactory $productFactory
     * @param array $productSendContent
     */
    public function __construct(
        Context $context,
        Http $request,
        ProductFactory $productFactory,
        array $productSendContent = []
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
        $targets = [];

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
            /**
             * Declare $productSendContent in etc/adminhtml/di.xml as argument to reusable
             */
            if ( array_key_exists('instance', $this->productSendContent) ) {
                $this->_objectManager->create($this->productSendContent['instance'])
                    ->execute(
                        $product,
                        $sourceLocale,
                        $targets
                );
            }
        }

        $url = $this->getUrl(
            'catalog/product/edit',
            ['id' => $productId]
        );

        $this->getResponse()->setRedirect($url)->sendResponse();
    }
}
