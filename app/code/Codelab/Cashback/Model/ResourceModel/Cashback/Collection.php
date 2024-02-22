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


namespace Codelab\Cashback\Model\ResourceModel\Cashback;

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
            \Codelab\Cashback\Model\Cashback::class,
            \Codelab\Cashback\Model\ResourceModel\Cashback::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}

