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
     * @var \Contentor\LocalizationApi\Service\GetAttributeConfigService
     */
    private $getAttributeConfigService;

    /**
     * AttributeProvider constructor.
     * @param ConfigurationService $configurationService
     * @param ProductInterface $product
     * @param \Contentor\LocalizationApi\Service\GetAttributeConfigService $getAttributeConfigService
     * @param array $extraFields
     * @param null $syncType
     */
    public function __construct(
        ConfigurationService $configurationService,
        ProductInterface $product,
        \Contentor\LocalizationApi\Service\GetAttributeConfigService $getAttributeConfigService,
        array $extraFields = [],
        $syncType = null
    ) {
        $this->configurationService = $configurationService;
        $this->product = $product;
        $this->extraFields = $extraFields;
        $this->syncType = $syncType;
        $this->getAttributeConfigService = $getAttributeConfigService;
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

        $fields[] =
            [
                'id' => 'auto_sku',
                'type' => 'internal',
                'data' => 'string',
                'value' => $sku
            ];
        $fields[] =
            [
                'id' => 'auto_m2_product_id',
                'type' => 'internal',
                'data' => 'string',
                'value' => $productID
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
                 * Override Attribute Configs from request
                 */
                $configAttributes = $this->getAttributeConfigService->execute();


                if (
                    !empty($configAttributes)
                    && array_key_exists($field['attribute'], $configAttributes)
                ) {
                    $field['word_count'] = $configAttributes[$field['attribute']]['word_count'];
                }

                $fieldType = $configAttributes[$field['attribute']]['field_type'];

                if ( !empty($fieldType) ) {
                    $field['field_type'] = $fieldType;
                }

                if ( !empty($field['field_type']) ) {

                    /**
                     * Logic for context field
                     * Send if there has value only
                     * Otherwise skip this
                     */
                    if ( $field['field_type'] == 'context' ) {

                        $contextValue = $configAttributes[$field['attribute']]['context_value'];
                        // If context Value is null, take the value from Magento's Attribute
                        if ( empty($contextValue) ) {
                            $contextValue = $value;
                        }

                        if( !empty($contextValue) ) {
                            $fields[] = [
                                'id' => $id . '_original',
                                'name' => $name . '_original',
                                'type' => 'context',
                                'data' => $field['data'],
                                'value' => $contextValue
                            ];

                        } else {
                            return [];
                        }

                        $fields[] = [
                            'id' => $id,
                            'name' => $name,
                            'type' => 'context',
                            'data' => $field['data'],
                            'hints' => [
                                [
                                    'type'   => 'word-count',
                                    'around' => (int) $field['word_count'],
                                ],
                            ],
                            'value' => $contextValue
                        ];
                    } else {
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



                } else {
                    /**
                     * Skip this request due to missing configuration
                     */
                    return [];
                }

            }
        }
        return $fields;
    }
}
