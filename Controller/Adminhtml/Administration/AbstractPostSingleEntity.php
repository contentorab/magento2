<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;

/**
 * Class AbstractPostSingleEntity
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
 */
class AbstractPostSingleEntity extends \Magento\Backend\App\Action
{
    /**
     * @var
     */
    protected $routeRedirect;

    /**
     * @var
     */
    protected $paramRequestId;

    /**
     * @var
     */
    protected $modelFactory;

    /**
     * @var array
     */
    protected $serviceSendContent;

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
     * AbstractPostSingleEntity constructor.
     * @param Context $context
     * @param Http $request
     * @param array $serviceSendContent
     */
    public function __construct(
        Context $context,
        Http $request,
        array $serviceSendContent = []
    ) {
        parent::__construct($context);
        $this->serviceSendContent = $serviceSendContent;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $targets = [];

            $request = $this->request;

            $idRequest = $request->getParam($this->paramRequestId);

            /**
             *  Initial modelFactory from ObjectManager
             */
            $this->modelFactory = $this->_objectManager->create($this->modelFactory);

            $entity = $this->modelFactory->create()->load($idRequest);

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
                 * Declare $serviceSendContent in etc/adminhtml/di.xml as argument to reusable
                 */
                if ( array_key_exists('instance', $this->serviceSendContent) ) {
                    $this->_objectManager->create($this->serviceSendContent['instance'])
                        ->execute(
                            $entity,
                            $sourceLocale,
                            $targets
                        );
                }
            }

            $url = $this->getUrl(
                $this->routeRedirect,
                ['id' => $idRequest]
            );

            $this->messageManager->addSuccess(__('Sent request to Contentor platform successfully.'));
        }catch ( \Exception $e ) {
            $this->messageManager->addError(__($e->getMessage()));
        }

        $this->getResponse()->setRedirect($url)->sendResponse();
    }
}
