<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class AbstractPostSingleEntity extends Action
{
    protected $entityName;
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
    protected $shouldValidateTargetAndSource = false;

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
     * @return ResponseInterface|ResultInterface|void
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

            $versioning = $request->getParam('versioning');

            foreach ($targetList as $targetData) {
                list($id, $locale) = explode(':', $targetData);
                $targets[$id] = $locale;
            }

            if (count($targets)
                && !in_array($sourceLocale, $targets)
                && !$this->shouldValidateTargetAndSource == true
            ) {
                /**
                 * Declare $serviceSendContent in etc/adminhtml/di.xml as argument to reusable
                 */
                if (array_key_exists('instance', $this->serviceSendContent)) {
                    $this->_objectManager->create($this->serviceSendContent['instance'])
                        ->execute(
                            $entity,
                            $sourceLocale,
                            $targets,
                            $versioning
                        );
                }
            }

            $url = $this->getUrl(
                $this->routeRedirect,
                ['id' => $idRequest]
            );

            $this->messageManager->addSuccessMessage(
                __(sprintf('Sent %s request to Contentor platform successfully.', $this->entityName))
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));

            $url = $this->getUrl(
                $this->routeRedirect,
                ['id' => $idRequest]
            );
            $this->getResponse()->setRedirect($url)->sendResponse();
        }

        $this->getResponse()->setRedirect($url)->sendResponse();
    }
}
