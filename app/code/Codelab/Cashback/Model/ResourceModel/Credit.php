<?php
namespace Codelab\Cashback\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Credit extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('codelab_cashback_credit', 'credit_id');
    }
}
