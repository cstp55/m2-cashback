<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Codelab_Cashback
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */


namespace Codelab\Cashback\Model\ResourceModel;

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
        $this->_init("codelab_cashback_transaction", "transaction_id");
    }
}

