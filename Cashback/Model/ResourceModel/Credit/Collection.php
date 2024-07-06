<?php
/**
 * Aprilo Software.
 *
 * @category Aprilo Software Private Limited
 * @package aprilo_Cashback
 * @author Aprilo
 * @copyright Copyright (c) Aprilo Software Private Limited (https://Aprilo.com)
 * @license https://store.Aprilo.com/license.html
 */
namespace Aprilo\Cashback\Model\ResourceModel\Credit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\GiftCard\Model\Credit;

/**
 * Class Collection
 * @package Mageplaza\GiftCard\Model\ResourceModel\Credit
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'credit_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Credit::class, \Aprilo\Cashback\Model\ResourceModel\Credit::class);
    }
}
