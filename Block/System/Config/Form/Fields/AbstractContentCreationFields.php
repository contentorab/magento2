<?php

namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

use Exception;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

class AbstractContentCreationFields extends AbstractFieldArray
{
    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];

    /**
     * @var
     */
    protected $_sourceRenderer;
    /**
     * @var
     */
    protected $_fieldTypeRenderer;
    /**
     * @var
     */
    protected $_fieldDataTypeRenderer;
    /**
     * @var
     */
    protected $_requiredRenderer;

    /**
     * @var
     */
    protected $_attributeRenderer;

    /**
     * Class for rendering attributes of entity
     */
    protected $_attributeRendererClass;

    /**
     * Define entity
     */
    protected $_entity;

    /**
     * Enable the "Add after" button or not
     *
     * @var bool
     */
    protected $_addAfter = true;

    /**
     * Label of add button
     *
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     * @throws Exception
     */
    public function renderCellTemplate($columnName): string
    {
        return parent::renderCellTemplate($columnName);
    }

    /**
     * Check if columns are defined, set template
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'attribute',
            [
                'label' => sprintf(__('%s Attribute'), $this->_entity),
                'renderer' => $this->getAttributeRenderer()
            ]
        );

        $this->addColumn(
            'store',
            [
                'label' => __('Source Store View'),
                'renderer' => $this->getSourceRenderer()
            ]
        );

        $this->addColumn(
            'field_type',
            [
                'label' => __('Field Type'),
                'renderer' => $this->getFieldTypeRenderer()
            ]
        );

        $this->addColumn(
            'data',
            [

                'label' => __('Field Data Type'),
                'renderer' => $this->getFieldDataTypeRenderer()
            ]
        );

        $this->addColumn(
            'word_count',
            [
                'label' => __('Amount Of Words'),
                'class' => 'required-entry'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return BlockInterface
     * @throws LocalizedException
     */
    protected function getAttributeRenderer(): BlockInterface
    {
        if (!$this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                $this->_attributeRendererClass,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_attributeRenderer;
    }

    /**
     * @return BlockInterface
     * @throws LocalizedException
     */
    protected function getSourceRenderer(): BlockInterface
    {
        if (!$this->_sourceRenderer) {
            $this->_sourceRenderer = $this->getLayout()->createBlock(
                '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\SourceRenderer',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_sourceRenderer;
    }

    /**
     * @return BlockInterface
     * @throws LocalizedException
     */
    protected function getFieldTypeRenderer(): BlockInterface
    {
        if (!$this->_fieldTypeRenderer) {
            $this->_fieldTypeRenderer = $this->getLayout()->createBlock(
                '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\FieldTypeContentCreationRenderer',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_fieldTypeRenderer;
    }

    /**
     * @return BlockInterface
     * @throws LocalizedException
     */
    protected function getFieldDataTypeRenderer(): BlockInterface
    {
        if (!$this->_fieldDataTypeRenderer) {
            $this->_fieldDataTypeRenderer = $this->getLayout()->createBlock(
                '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\FieldDataTypeRenderer',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_fieldDataTypeRenderer;
    }

    /**
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $productAttribute = $row->getData('attribute');

        $source = $row->getData('store');

        $fieldDatatype = $row->getData('data');

        $fieldType = $row->getData('field_type');

        $options = [];

        if ($productAttribute) {
            $options['option_'
            . $this->getAttributeRenderer()->calcOptionHash($productAttribute)] = 'selected="selected"';
        }

        if ($source) {
            $options['option_' . $this->getSourceRenderer()->calcOptionHash($source)] = 'selected="selected"';
        }

        if ($fieldType) {

            $options['option_' . $this->getFieldTypeRenderer()->calcOptionHash($fieldType)] = 'selected="selected"';
        }

        if ($fieldDatatype) {
            $options['option_'
            . $this->getFieldDataTypeRenderer()->calcOptionHash($fieldDatatype)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
