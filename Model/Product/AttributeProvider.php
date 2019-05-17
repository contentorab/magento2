<?php
namespace Contentor\LocalizationApi\Model\Product;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class AttributeProvider
 * @package Contentor\LocalizationApi\Model
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
     * AttributeProvider constructor.
     * @param ConfigurationService $configurationService
     * @param ProductInterface $product
     * @param array $extraFields
     */
    public function __construct(
        ConfigurationService $configurationService,
        ProductInterface $product,
        array $extraFields = []
    ) {
        $this->configurationService = $configurationService;
        $this->product = $product;
        $this->extraFields = $extraFields;
    }

    /**
     * @return array
     */
    public function getList()
    {
        $fieldArray = $this->configurationService->getProductFields();

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

            if (! empty($value)) {
                $fields[] = ['id'=>$id,
                    'name'=>$name,
                    'type'=>$field['type'],
                    'data'=>$field['data'],
                    'value'=>$value];
            } else {
                if (isset($fields['required'])) {
                    $messages[] = 'Required field: [' . $field['attribute'] . '] empty';
                }
            }
        }

        return $fields;
    }
}
