<?php
namespace Contentor\LocalizationApi\Model\Product;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class AttributeProvider
 * @package Contentor\LocalizationApi\Model
 *
 * Provide all localizable and creatable attributes based on config ,
 * particular product , and apply extra data if needed
 */
class AttributeProvider
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var ProductInterface|\Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var array
     */
    private $extraFields;

    /**
     * @var int
     */
    private $syncType;

    /**
     * @var \Contentor\LocalizationApi\Service\GetWordAmountConfigService
     */
    private $getWordAmountConfigService;

    /**
     * AttributeProvider constructor.
     * @param ConfigurationService $configurationService
     * @param ProductInterface $product
     * @param \Contentor\LocalizationApi\Service\GetWordAmountConfigService $getWordAmountConfigService
     * @param array $extraFields
     * @param null $syncType
     */
    public function __construct(
        ConfigurationService $configurationService,
        ProductInterface $product,
        \Contentor\LocalizationApi\Service\GetWordAmountConfigService $getWordAmountConfigService,
        array $extraFields = [],
        $syncType = null
    ) {
        $this->configurationService = $configurationService;
        $this->product = $product;
        $this->extraFields = $extraFields;
        $this->syncType = $syncType;
        $this->getWordAmountConfigService = $getWordAmountConfigService;
    }

    /**
     * Returns list of attributes in contentor api format
     *
     * @return array
     */
    public function getList()
    {
        if ( $this->syncType ==  \Contentor\LocalizationApi\Model\Product::LOCALIZED_SYNC_TYPE  ) {
            $fieldArray = $this->configurationService->getProductFields();
        }

        if ( $this->syncType ==  \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE  ) {
            $fieldArray = $this->configurationService->getContentCreationProductFields();
        }

        //map is empty
        if (empty($fieldArray)) {
            return [];
        }

        $product = $this->product;

        $sku = $product->getSku();

        $productID = $product->getId();

        $fields = [];

        $fields[] = [
                'id' => 'auto_sku',
                'type' => 'internal',
                'data' => 'string',
                'value' => $sku
            ];

        // Add extra content if sent
        if ($this->extraFields) {
            $extraField = $this->extraFields;
            $fields[] = [
                'id' => 'extraField',
                'name' => $extraField['name'],
                'type' => $extraField['type'],
                'data' => $extraField['data'],
                'value' => $extraField['value']
            ];
        }

        $n = [];

        foreach ($fieldArray as $field) {
            if ($field['attribute'] == 'productURL') {
                $value = $product->setStoreId($field['store'])->getUrlInStore();
                $name = 'Product URL';
            } else {
                $attribute = $product->getResource()->getAttribute($field['attribute']);
                $value = $product->getResource()->getAttributeRawValue($productID, $field['attribute'], $field['store']);
                $name = $attribute->getFrontendLabel();
            }

            if (!isset($n[$field['attribute']])) {
                $n[$field['attribute']] = 1;
            } else {
                $n[$field['attribute']]++;
            }

            $id = $field['attribute'] . '_' . sprintf("%03d", $n[$field['attribute']]);

            //localizable feature
            if ( $this->syncType == \Contentor\LocalizationApi\Model\Product::LOCALIZED_SYNC_TYPE ) {

                if (! empty($value)) {

                    $fields[] = [
                        'id' => $id,
                        'name' => $name,
                        'type' => $field['type'],
                        'data' => $field['data'],
                        'value' => $value
                    ];

                } else {
                    if (isset($fields['required'])) {
                        $messages[] = 'Required field: [' . $field['attribute'] . '] empty';
                    }
                }
            }

            //content creation feature
            if ( $this->syncType ==  \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE  ) {
                //Value has to be null for creatable field

                /**
                 * Override WordAmount configs from request
                 */
                $configWordsAmount = $this->getWordAmountConfigService->execute();
                if (
                    !empty($configWordsAmount)
                    && array_key_exists($field['attribute'], $configWordsAmount)
                ) {
                    $field['word_count'] = $configWordsAmount[$field['attribute']];
                }

                $fields[] = [
                    'id' => $id,
                    'name' => $name,
                    'type' => 'creatable',
                    'data' => $field['data'],
                    'hints' => [
                        [
                            'type'   => 'word-count',
                            'around' => (int) $field['word_count'],
                        ],
                    ]
                ];
            }
        }

        return $fields;
    }
}
