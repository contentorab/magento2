<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SendBulkProducts
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
 */
class AbstractSendBulkProducts extends Action
{
    /**
     * @var array
     */
    protected $serviceSendContent;

    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Should validate target and source which should be different
     * use for localization
     * @var bool
     */
    protected $shouldValidateTargetAndSource = true;

    /**
     * AbstractSendBulkProducts constructor.
     * @param Context $context
     * @param Http $request
     * @param ProductFactory $productFactory
     * @param PageFactory $pageFactory
     * @param ConfigurationService $configurationService
     * @param array $serviceSendContent
     */
    public function __construct(
        Context $context,
        Http $request,
        ProductFactory $productFactory,
        PageFactory $pageFactory,
        ConfigurationService $configurationService,
        array $serviceSendContent = []
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->pageFactory = $pageFactory;
        $this->configurationService = $configurationService;
        $this->serviceSendContent = $serviceSendContent;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        // Get product list and send 10/50/100? first, then print form with the rest
        $max = 10;
        $data = $this->_request->getParams();

        $productlist = explode(',', $data['products']);
        $numberProducts = count($productlist);
        $sourceLocale =  str_replace('_', '-', $data['source']);
        $targetIDs = explode(",", $data["targets"]);
        $numberTargets = count($targetIDs);

        foreach ($targetIDs as $targetID) {
            $targets[$targetID] = str_replace('_', '-', $this->configurationService->getMainLocale($targetID));
        }

        // Check if source in targets?
        if (!count($targets)) {
            $this->getResponse()->setBody('No data sent!<br>No target selected');
        } elseif (in_array($sourceLocale, $targets) && $this->shouldValidateTargetAndSource == true ) {
            $this->getResponse()->setBody('No data sent!<br>Source language in targets');
        } else {
            // Loop products
            if ($data["total"] == 0) {
                $total = $numberProducts*$numberTargets;
            } else {
                $total = $data["total"];
            }
            // Loop products and send the n first
            if ($numberProducts < $max) {
                $max = $numberProducts;
            }

            for ($i=0; $i<$max; $i++) {
                // Send some products
                $prodID = $productlist[$i];

                $product = $this->productFactory->create()->load($prodID);

                /**
                 * Declare $serviceSendContent in etc/adminhtml/di.xml as argument to reusable
                 */
                try{
                    if ( array_key_exists('instance', $this->serviceSendContent) ) {
                        $this->_objectManager->create($this->serviceSendContent['instance'])
                            ->execute(
                                $product,
                                $sourceLocale,
                                $targets
                            );

                        unset($productlist[$i]);
                    }
                } catch(LocalizedException $e){
                    $this->getResponse()->setBody($e->getMessage());
                    return;
                }
            }

            $productsLeft = $numberTargets*count($productlist);
            $procent = (1-($productsLeft/$total))*100;

            $returnData = "<h3>Progress</h3>";
            $returnData .= "<svg height=20 width=\"100%\">";
            $returnData .= "<g transform=\"translate(0,0)\">";
            $returnData .= "<rect height=20 width=\"100%\" style=\"fill:#ccc;\"></rect>";
            $returnData .= "</g>";
            $returnData .= "<g transform=\"translate(0,0)\">";
            $returnData .= "<rect height=20 width=\"" . $procent . "%\" style=\"fill:#d75f07;\"></rect>";
            $returnData .= "</g>";
            $returnData .= "</svg>";
            $returnData .= '<b>' . $productsLeft . '</b> items remaining from a total of <b>' . $total . '</b> items';

            $returnData .= "<form id=\"progressform\">";
            if ($productsLeft > 0) {
                $returnData .= "<input type=\"hidden\" name=\"total\" value=\"" . $total . "\">";
                $returnData .= "<input type=\"hidden\" name=\"deliverySpeed\" value=\"" . $data['deliverySpeed'] . "\">";
                $returnData .= "<input type=\"hidden\" name=\"products\" value=\"" . join(',', $productlist) . "\">";
                $returnData .= "<input type=\"hidden\" name=\"source\" value=\"" . $sourceLocale . "\">";
                $returnData .= "<input type=\"hidden\" name=\"targets\" value=\"" . join(',', $targetIDs) . "\">";
                if ( !empty($data['attributesConfig']) ) {
                    $returnData .= '<div id="configAttributesContentCreationResponse">';
                    foreach ( $data['attributesConfig'] as $attributeCode => $configs) {

                        $wordCountId = $attributeCode.'_word_count';

                        $fieldTypeId = $attributeCode.'_field_type';

                        $contextValueId = $attributeCode.'_context_value';

                        $returnData .= "<input type=\"hidden\" id=\"$wordCountId\" name=\"attributesConfig[$attributeCode][word_count]\" value=\"" . $configs['word_count'] . "\">";

                        $returnData .= "<input type=\"hidden\" id=\"$fieldTypeId\" name=\"attributesConfig[$attributeCode][field_type]\" value=\"" . $configs['field_type'] . "\">";

                        $returnData .= "<input type=\"hidden\" id=\"$contextValueId\" name=\"attributesConfig[$attributeCode][context_value]\" value=\"" . $configs['context_value'] . "\">";
                    }
                    $returnData .= "</div>";
                }

                if (!empty($data['contextvalue'])) {
                    $returnData .= "<input type=\"hidden\" name=\"contextvalue\" value=\"" . $data['contextvalue'] . "\">";
                    $returnData .= "<input type=\"hidden\" name=\"contextname\" value=\"" . $data['contextname'] . "\">";
                } else {
                    $returnData .= "<input type=\"hidden\" name=\"contextvalue\" value=\"\">";
                    $returnData .= "<input type=\"hidden\" name=\"contextname\" value=\"\">";
                }
            } else {
                $url = $this->getUrl('contentor/reports/productreport');

                $returnData .= "<input type=\"hidden\" name=\"theend\" value=\"true\">";
                $returnData .= "<h3>Done!</h3>";
                $returnData .= "Go to <a href=\"" . $url . "\">report page</a>";
            }
            $returnData .= "</form>";

            $this->getResponse()->setBody($returnData);
        }
    }
}
