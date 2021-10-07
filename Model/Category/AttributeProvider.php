<?php

namespace Contentor\LocalizationApi\Model\Category;

use Contentor\LocalizationApi\Model\Category;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Contentor\LocalizationApi\Service\GetAttributeConfigService;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CatalogCategory;

class AttributeProvider
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var CategoryInterface|CatalogCategory
     */
    private $category;

    /**
     * @var int
     */
    private $syncType;

    /**
     * @var GetAttributeConfigService
     */
    private $getAttributeConfigService;

    /**
     * AttributeProvider constructor.
     * @param ConfigurationService $configurationService
     * @param CategoryInterface $category
     * @param GetAttributeConfigService $getAttributeConfigService
     * @param null $syncType
     */
    public function __construct(
        ConfigurationService $configurationService,
        CategoryInterface $category,
        GetAttributeConfigService $getAttributeConfigService,
        $syncType = null
    ) {
        $this->configurationService = $configurationService;
        $this->category = $category;
        $this->syncType = $syncType;
        $this->getAttributeConfigService = $getAttributeConfigService;
    }

    /**
     * Returns list of attributes in contentor api format
     *
     * @return array
     */
    public function getList(): array
    {
        if ($this->syncType == Category::LOCALIZED_SYNC_TYPE) {
            $fieldArray = $this->configurationService->getCategoryFields();
        }

        if ($this->syncType == Category::CONTENT_CREATION_SYNC_TYPE) {
            $fieldArray = $this->configurationService->getContentCreationCategoryFields();
        }

        //map is empty
        if (empty($fieldArray)) {
            return [];
        }

        $category = $this->category;

        $categoryId = $category->getId();

        $fields = [];

        $fields[] = [
            'id' => 'auto_category_id',
            'type' => 'internal',
            'data' => 'string',
            'value' => (string)$categoryId
        ];

        $fields[] = [
            'id' => 'auto_category_name',
            'type' => 'internal',
            'data' => 'string',
            'value' => $category->getName()
        ];

        $n = [];

        foreach ($fieldArray as $field) {
            $attribute = $category->getResource()->getAttribute($field['attribute']);

            $value = $category->getResource()->getAttributeRawValue($categoryId, $field['attribute'], $field['store']);

            $name = $attribute->getFrontendLabel();

            if (!isset($n[$field['attribute']])) {
                $n[$field['attribute']] = 1;
            } else {
                $n[$field['attribute']]++;
            }

            $id = $field['attribute'] . '_' . sprintf("%03d", $n[$field['attribute']]);

            //localizable feature
            if ($this->syncType == Category::LOCALIZED_SYNC_TYPE) {
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
            if ($this->syncType == Category::CONTENT_CREATION_SYNC_TYPE) {
                //Value has to be null for creatable field

                /**
                 * Override WordAmount configs from request
                 */
                $configAttribute = $this->getAttributeConfigService->execute();

                if (!empty($configAttribute)
                    && array_key_exists($field['attribute'], $configAttribute)
                ) {
                    $field['word_count'] = $configAttribute[$field['attribute']]['word_count'];
                }

                $fieldType = $configAttribute[$field['attribute']]['field_type'];

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
                        $contextValue = $configAttribute[$field['attribute']]['context_value'];
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
                        } else {
                            return [];
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
}
