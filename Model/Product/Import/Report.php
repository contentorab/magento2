<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Model\Product\Import;

use Magento\Framework\Model\AbstractModel;
use Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report as ResourceModel;

/**
 * @method string getCreatedAt()
 * @method self setProductId(int $id)
 * @method int getProductId()
 * @method self setMessage(string $message)
 * @method string getMessage()
 * @method self setStatus()
 * @method int getStatus()
 * @method self setSourceLocale(string $locale)
 * @method string getSourceLocale()
 * @method self setTargetLocale(string $locale)
 * @method string getTargetLocale()
 * @method self setTargetStoreId(int $id)
 * @method int getTargetStoreId()
 */
class Report extends AbstractModel
{
    public const STATUS_PENDING = 0;

    public const STATUS_SUCCESS = 1;

    public const STATUS_ERROR = 2;

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
