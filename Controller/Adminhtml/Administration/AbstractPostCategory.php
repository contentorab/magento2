<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;

/**
 * Class AbstractPostCategory
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
 */
class AbstractPostCategory extends \Magento\Backend\App\Action
{
    /**
     * @var array
     */
    protected $categorySendContent;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Http
     */
    protected $request;

    /**
     * Should validate target and source which should be different
     * use for localization
     * @var bool
     */
    protected $shouldValidateTargetAndSource = true;


    /**
     * AbstractPostCategory constructor.
     * @param Context $context
     * @param Http $request
     * @param CategoryRepositoryInterface $categoryFactory
     * @param array $categorySendContent
     */
    public function __construct(
        Context $context,
        Http $request,
        CategoryRepositoryInterface $categoryFactory,
        array $categorySendContent = []
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->categorySendContent = $categorySendContent;
        $this->request = $request;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $targets = [];

        $request = $this->request;

        $categoryId = $request->getParam('categoryid');

        $category = $this->categoryFactory->get($categoryId);

        $sourceLocale = $request->getParam('source');

        $targetList = $request->getParam('targets');

        foreach ($targetList as $targetData) {
            list($id, $locale) = explode(':', $targetData);
            $targets[$id] = $locale;
        }

        if (!count($targets)) {
            // No target selected
        } elseif (in_array($sourceLocale, $targets) && $this->shouldValidateTargetAndSource == true ) {
            // Source locale in targets
        } else {
            /**
             * Declare $categorySendContent in etc/adminhtml/di.xml as argument to reusable
             */
            if ( array_key_exists('instance', $this->categorySendContent) ) {
                $this->_objectManager->create($this->categorySendContent['instance'])
                    ->execute(
                        $category,
                        $sourceLocale,
                        $targets
                );
            }
        }

        $url = $this->getUrl(
            'catalog/category/edit',
            ['id' => $categoryId]
        );

        $this->messageManager->addSuccess(__('Sent request to localization '));

        $this->getResponse()->setRedirect($url)->sendResponse();
    }
}
