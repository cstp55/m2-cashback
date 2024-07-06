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
namespace Aprilo\Cashback\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aprilo\Cashback\Model\Transaction;


class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Transaction::class, \Aprilo\Cashback\Model\ResourceModel\Transaction::class);
    }
}
