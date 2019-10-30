<?php
namespace Contentor\LocalizationApi\Model\Category;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Class AttributeProvider
 * @package Contentor\LocalizationApi\Model\Category
 */
class AttributeProvider
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var CategoryInterface|\Magento\Catalog\Model\Category
     */
    private $category;

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
     * @param CategoryInterface $category
     * @param \Contentor\LocalizationApi\Service\GetWordAmountConfigService $getWordAmountConfigService
     * @param array $extraFields
     * @param null $syncType
     */
    public function __construct(
        ConfigurationService $configurationService,
        CategoryInterface $category,
        \Contentor\LocalizationApi\Service\GetWordAmountConfigService $getWordAmountConfigService,
        array $extraFields = [],
        $syncType = null
    ) {
        $this->configurationService = $configurationService;
        $this->category = $category;
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
        if ( $this->syncType ==  \Contentor\LocalizationApi\Model\Category::LOCALIZED_SYNC_TYPE  ) {
            $fieldArray = $this->configurationService->getCategoryFields();
        }

        if ( $this->syncType ==  \Contentor\LocalizationApi\Model\Category::CONTENT_CREATION_SYNC_TYPE  ) {
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
                'value' => (string) $categoryId
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
            if ( $this->syncType == \Contentor\LocalizationApi\Model\Category::LOCALIZED_SYNC_TYPE ) {

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
            if ( $this->syncType ==  \Contentor\LocalizationApi\Model\Category::CONTENT_CREATION_SYNC_TYPE  ) {
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
