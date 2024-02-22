<?php

namespace Codelab\Cashback\Model\ResourceModel;

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
        $this->_init('codelab_cashback_transaction', 'transaction_id');
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
