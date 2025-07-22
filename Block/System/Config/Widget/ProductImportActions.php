<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Block\System\Config\Widget;

use Contentor\LocalizationApi\Model\Product\Import\ReportRepository;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Button;

class ProductImportActions extends Field
{
    public const PRODUCT_IMPORT_PATH = 'contentor/administration/productimport';

    /**
     * @var string
     */
    protected $_template = 'Contentor_LocalizationApi::system/config/widget/product_import_actions.phtml';

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl(self::PRODUCT_IMPORT_PATH);
    }

    /**
     * @return string
     */
    public function getStartImportButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        );
        /** @var Button $button */

        $button->setData(
            [
                'id' => 'start_product_import_button',
                'label' => __('Start Product Import')
            ]
        );

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getRetryFailedImportsButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        );
        /** @var Button $button */

        $button->setData(
            [
                'id' => 'retry_failed_imports_button',
                'label' => __('Retry Failed Imports')
            ]
        );

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getLastImport(): string
    {
        $latestReport = $this->getReportRepository()->getLatest();
        if ($latestReport->getId()) {
            return $latestReport->getCreatedAt();
        }

        return 'Never';
    }

    /**
     * report_repository populated by di.xml
     *
     * @return ReportRepository
     */
    private function getReportRepository(): ReportRepository
    {
        return $this->getData('report_repository');
    }
}
