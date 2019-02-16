<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

class SendBulkProducts extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Contentor\LocalizationApi\Helper\ContentorAPI $contentorApi,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Contentor\LocalizationApi\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_contentorApi = $contentorApi;
        $this->_request = $request;
        $this->_productfactory = $productFactory;
        $this->_resource = $resource;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

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
            $targets[$targetID] = str_replace('_', '-', $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $targetID));
        }

        // Check if source in targets?
        if (!count($targets)) {
            $this->getResponse()->setBody('No data sent!<br>No target selected');
        } elseif (in_array($sourceLocale, $targets)) {
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
                $product = $this->_productfactory->create()->load($prodID);
                $sku = $product->getSku();

                if ($fields = $this->_contentorApi->getFieldData($product, 'product')) {
                    // Then Loop targets and send
                    foreach ($targets as $targetID => $targetLocale) {
                        // Find out type and pass it along
                        $type = 'standard';
                        $prevID = false;
                        // TODO: check settings for versioning

                        if ($this->_helper->getConfig('contentor_versioning/versioning/versioning_enable')) {
                            $connection = $this->_resource->getConnection('core_read');
                            $table = $this->_resource->getTableName('contentor_products');

                            $query = "SELECT `contentor_id` FROM `" . $table . "` WHERE `sku` = :sku AND `target_locale` = :target_locale AND `source_locale` = :source_locale ORDER BY `sent_time` DESC";
                            $binds = ['sku'     => $sku,
                            'target_locale'    => $targetLocale,
                            'source_locale' => $sourceLocale
                            ];
                            $return = $connection->fetchOne($query, $binds);

                            if ($return) {
                                   $type = 'update';
                                   $prevID = $return;
                            }
                        }

                        $request = $this->_contentorApi->createRequest($sourceLocale, $targetLocale, $fields, $type, $prevID);

                        if ($contentorID = $this->_contentorApi->send($request)) {
                            if (!$this->_contentorApi->logSent($contentorID, $sku, $sourceLocale, $targetLocale, $targetID, $product, $type)) {
                                      print 'No logs written';
                            }
                        } else {
                            print 'Error sending to Contentor API';
                        }
                    }
                } else {
                    $this->getResponse()->setBody('Error can\'t get fields from settings');
                }

                unset($productlist[$i]);
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
                $returnData .= "<input type=\"hidden\" name=\"products\" value=\"" . join(',', $productlist) . "\">";
                $returnData .= "<input type=\"hidden\" name=\"source\" value=\"" . $sourceLocale . "\">";
                $returnData .= "<input type=\"hidden\" name=\"targets\" value=\"" . join(',', $targetIDs) . "\">";
                if (!empty($data['contextvalue'])) {
                    $returnData .= "<input type=\"hidden\" name=\"contextvalue\" value=\"" . $data['contextvalue'] . "\">";
                    $returnData .= "<input type=\"hidden\" name=\"contextname\" value=\"" . $data['contextname'] . "\">";
                } else {
                    $returnData .= "<input type=\"hidden\" name=\"contextvalue\" value=\"\">";
                    $returnData .= "<input type=\"hidden\" name=\"contextname\" value=\"\">";
                }
            } else {
                $returnData .= "<input type=\"hidden\" name=\"theend\" value=\"true\">";
                $returnData .= "<h3>Done!</h3>";
                $returnData .= "Go to <a href=\"" . "\">report page</a>";
            }
            $returnData .= "</form>";
        }

        $this->getResponse()->setBody($returnData);
    }
}
