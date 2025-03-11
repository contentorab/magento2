<?php

namespace Contentor\LocalizationApi\Model\Product;

use Contentor\LocalizationApi\Model\Product;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Contentor\LocalizationApi\Service\GetAttributeConfigService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\ProductFactory;

/**
 * Class AttributeProvider
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
     * @var ProductFactory
     */
    private ProductFactory $productResourceFactory;

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
     * @var GetAttributeConfigService
     */
    private $getAttributeConfigService;

    /**
     * @var int|null
     */
    private ?int $storeId = null;

    /**
     * AttributeProvider constructor.
     * @param ConfigurationService $configurationService
     * @param ProductInterface $product
     * @param GetAttributeConfigService $getAttributeConfigService
     * @param array $extraFields
     * @param null $syncType
     */
    public function __construct(
        ConfigurationService $configurationService,
        ProductFactory $productResourceFactory,
        ProductInterface $product,
        GetAttributeConfigService $getAttributeConfigService,
        array $extraFields = [],
        $syncType = null,
        $storeId = null
    ) {
        $this->configurationService = $configurationService;
        $this->productResourceFactory = $productResourceFactory;
        $this->product = $product;
        $this->extraFields = $extraFields;
        $this->syncType = $syncType;
        $this->storeId = $storeId;
        $this->getAttributeConfigService = $getAttributeConfigService;
    }

    /**
     * Returns list of attributes in contentor api format
     *
     * @return array
     */
    public function getList(): array
    {
        if ($this->syncType == Product::LOCALIZED_SYNC_TYPE) {
            $fieldArray = $this->configurationService->getProductFields();
        }

        if ($this->syncType == Product::CONTENT_CREATION_SYNC_TYPE) {
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

        $productResource = $this->productResourceFactory->create();
        foreach ($fieldArray as $field) {
            if ($field['attribute'] == 'productURL') {
                $value = $product->setStoreId($field['store'])->getUrlInStore();
                $name = 'Product URL';
            } else {
                $attribute = $productResource->getAttribute($field['attribute']);
                $value = $productResource
                    ->getAttributeRawValue($productID, $field['attribute'], $field['store']);
                $name = $attribute->getFrontendLabel();
            }

            if (!isset($n[$field['attribute']])) {
                $n[$field['attribute']] = 1;
            } else {
                $n[$field['attribute']]++;
            }

            $id = $field['attribute'] . '_' . sprintf("%03d", $n[$field['attribute']]);

            //localizable feature
            if ($this->syncType == Product::LOCALIZED_SYNC_TYPE) {
                if (!empty($value)) {
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
            if ($this->syncType == Product::CONTENT_CREATION_SYNC_TYPE) {
                //Value has to be null for creatable field

                /**
                 * Override Attribute Configs from request
                 */
                $configAttributes = $this->getAttributeConfigService->execute();

                if (!empty($configAttributes) && array_key_exists($field['attribute'], $configAttributes)) {
                    $field['word_count'] = $configAttributes[$field['attribute']]['word_count'];
                }

                $fieldType = $configAttributes[$field['attribute']]['field_type'];

                if (!empty($fieldType)) {
                    $field['field_type'] = $fieldType;
                }

                if (!empty($field['field_type'])) {

                    /**
                     * Logic for context field
                     * Send if there has value only
                     * Otherwise skip this
                     */
                    if ($field['field_type'] == 'context') {
                        $contextValue = $configAttributes[$field['attribute']]['context_value'];
                        // If context Value is null, take the value from Magento's Attribute
                        if (empty($contextValue)) {
                            $contextValue = $value;
                        }

                        if (!empty($contextValue)) {
                            $fields[] = [
                                'id' => $id,
                                'name' => $name,
                                'type' => 'context',
                                'data' => $field['data'],
                                'value' => $contextValue
                            ];
                        }
                    } else {
                        $fields[] = [
                            'id' => $id,
                            'name' => $name,
                            'type' => 'creatable',
                            'data' => $field['data'],
                            'hints' => [
                                [
                                    'type' => 'word-count',
                                    'around' => (int)$field['word_count'],
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

    /**
     * Get a field list for an import operation
     *
     * @return array
     */
    public function getImportList(): array
    {
        $fieldArray = $this->configurationService->getProductFields();
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
        $fields[] = [
            'id' => 'auto_m2_product_id',
            'type' => 'internal',
            'data' => 'string',
            'value' => $productID
        ];

        $productResource = $this->productResourceFactory->create();
        $n = [];
        foreach ($fieldArray as $field) {

            if (!isset($n[$field['attribute']])) {
                $n[$field['attribute']] = 1;
            } else {
                $n[$field['attribute']]++;
            }

            $id = $field['attribute'] . '_' . sprintf("%03d", $n[$field['attribute']]);
            if ($field['attribute'] == 'productURL') {
                continue;
            }

            $attributeModel = $productResource->getAttribute($field['attribute']);
            $originalValue = $productResource->getAttributeRawValue($productID, $field['attribute'], 0);
            $value = $productResource->getAttributeRawValue($productID, $field['attribute'], $this->storeId);

            if (empty($value) || empty($originalValue) || $value === $originalValue) {
                continue;
            }

            $fields[] = [
                'id' => $id,
                'name' => $attributeModel->getFrontendLabel(),
                'type' => $field['type'],
                'data' => $field['data'],
                'originalValue' => $originalValue,
                'value' => $value
            ];
        }

        return $fields;
    }
}
