<?php
namespace Aprilo\Cashback\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Credit extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Aprilo_cashback_credit', 'credit_id');
    }
}
