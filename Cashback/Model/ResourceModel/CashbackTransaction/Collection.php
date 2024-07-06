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


namespace Aprilo\Cashback\Model\ResourceModel\CashbackTransaction;

/**
 * CashbackTransaction Collection Class
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Aprilo\Cashback\Model\CashbackTransaction::class,
            \Aprilo\Cashback\Model\ResourceModel\CashbackTransaction::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.id';
    }
}

