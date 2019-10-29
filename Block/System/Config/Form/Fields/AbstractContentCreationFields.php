<?php
namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

/**
 * Class AbstractContentCreationFields
 * @package Contentor\LocalizationApi\Block\System\Config\Form\Fields
 */
class AbstractContentCreationFields extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
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
     * Check if columns are defined, set template
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAttributeRenderer()
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
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getSourceRenderer()
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
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getFieldDataTypeRenderer()
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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender()
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
     * @param \Magento\Framework\DataObject $row
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $productAttribute = $row->getData('attribute');

        $source = $row->getData('store');

        $fieldDatatype = $row->getData('data');

        $options = [];

        if ($productAttribute) {
            $options['option_' . $this->getAttributeRenderer()->calcOptionHash($productAttribute)] = 'selected="selected"';
        }

        if ($source) {
            $options['option_' . $this->getSourceRenderer()->calcOptionHash($source)] = 'selected="selected"';
        }

        if ($fieldDatatype) {
            $options['option_' . $this->getFieldDataTypeRenderer()->calcOptionHash($fieldDatatype)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param  string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        return parent::renderCellTemplate($columnName);
    }
}
