<?php
namespace Contentor\LocalizationApi\Block\System\Config\Form\Fields;

class ProductFields extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];
    protected $_productAttributeRenderer;
    protected $_sourceRenderer;
    protected $_fieldTypeRenderer;
    protected $_fieldDataTypeRenderer;
    protected $_requiredRenderer;

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
     * Returns renderer for country element
     *
     * @return \Magento\Braintree\Block\Adminhtml\Form\Field\Countries
     */
    protected function getProductAttributeRenderer()
    {
        if (!$this->_productAttributeRenderer) {
            $this->_productAttributeRenderer = $this->getLayout()->createBlock(
                '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\ProductAttributeRenderer',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_productAttributeRenderer;
    }

    protected function getScourceRenderer()
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

    protected function getFieldTypeRenderer()
    {
        if (!$this->_fieldTypeRenderer) {
            $this->_fieldTypeRenderer = $this->getLayout()->createBlock(
                '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\FieldTypeRenderer',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_fieldTypeRenderer;
    }

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

    protected function getRequiredRenderer()
    {
        if (!$this->_requiredRenderer) {
            $this->_requiredRenderer = $this->getLayout()->createBlock(
                '\Contentor\LocalizationApi\Block\Adminhtml\Form\Fields\RequiredRenderer',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_requiredRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'attribute',
            [
                'label' => __('Product attribute'),
                'renderer' => $this->getProductAttributeRenderer(),
            ]
        );

        $this->addColumn(
            'store',
            [
                'label' => __('Source Store View'),
                'renderer' => $this->getScourceRenderer(),
            ]
        );

        $this->addColumn(
            'type',
            [
                'label' => __('Field Type'),
                'renderer' => $this->getFieldTypeRenderer(),
            ]
        );

        $this->addColumn(
            'data',
            [
                'label' => __('Field Data Type'),
                'renderer' => $this->getFieldDataTypeRenderer(),
            ]
        );

        $this->addColumn(
            'required',
            [
                'label' => __('Required'),
                'renderer' => $this->getRequiredRenderer(),
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $productAttribute = $row->getData('attribute');
        $source = $row->getData('store');
        $fieldType = $row->getData('type');
        $fieldDatatype = $row->getData('data');
        $required = $row->getData('required');

        $options = [];
        if ($productAttribute) {
            $options['option_' . $this->getProductAttributeRenderer()->calcOptionHash($productAttribute)] = 'selected="selected"';
        }

        if ($source) {
            $options['option_' . $this->getScourceRenderer()->calcOptionHash($source)] = 'selected="selected"';
        }

        if ($fieldType) {
            $options['option_' . $this->getFieldTypeRenderer()->calcOptionHash($fieldType)] = 'selected="selected"';
        }

        if ($fieldDatatype) {
            $options['option_' . $this->getFieldDataTypeRenderer()->calcOptionHash($fieldDatatype)] = 'selected="selected"';
        }

        if ($required) {
            $options['required'] = 'checked';
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
