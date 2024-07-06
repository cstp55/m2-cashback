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
namespace Aprilo\Cashback\Model\ResourceModel;

use Exception;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Transaction
 * @package Mageplaza\GiftCard\Model
 */
class Transaction extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Aprilo_cashback_transaction', 'transaction_id');
    }

    /**
     * @param $objects
     *
     * @return $this
     * @throws Exception
     */
    public function createTransaction($objects)
    {
        $this->beginTransaction();

        try {
            foreach ($objects as $object) {
                $object->save();
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}
