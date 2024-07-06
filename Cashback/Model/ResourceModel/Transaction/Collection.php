<?php

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
