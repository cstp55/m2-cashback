<?php
/**
 * Aprilo Software.
 *
 * @category Aprilo
 * @package Aprilo_Cashback
 * @author Aprilo
 * @copyright Copyright (c) Aprilo Software Private Limited (https://Aprilo.com)
 * @license https://store.Aprilo.com/license.html
 */


namespace Aprilo\Cashback\Model\ResourceModel;

/**
 * CashbackTransaction RosourceModel Class
 */
class CashbackTransaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init("Aprilo_cashback_transaction", "transaction_id");
    }
}

