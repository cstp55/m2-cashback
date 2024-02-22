<?php
namespace Codelab\Cashback\Model\ResourceModel\Credit;

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
        $this->_init(Credit::class, \Codelab\Cashback\Model\ResourceModel\Credit::class);
    }
}
