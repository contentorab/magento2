<?php 
namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use \Magento\Backend\Block\Template\Context;

class BulkProducts extends \Magento\Backend\Block\Template
{
	protected $_template = 'gift/index.phtml';

	public function __construct(Context $context) {
				
		parent::__construct($context);
	}
}