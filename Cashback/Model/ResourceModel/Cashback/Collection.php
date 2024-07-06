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


namespace Aprilo\Cashback\Model\ResourceModel\Cashback;

/**
 * Cashback Collection Class
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Aprilo\Cashback\Model\Cashback::class,
            \Aprilo\Cashback\Model\ResourceModel\Cashback::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}

