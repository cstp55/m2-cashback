<?php

namespace Codelab\Cashback\Model;

/**
 * CashbackTransaction Model Class
 */
class CashbackTransaction extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface, \Codelab\Cashback\Api\Data\CashbackTransactionInterface
{
    final public const NOROUTE_ENTITY_ID = 'no-route';

    final public const CACHE_TAG = 'codelab_cashback_cashbacktransaction';

    protected $_cacheTag = 'codelab_cashback_cashbacktransaction';

    protected $_eventPrefix = 'codelab_cashback_cashbacktransaction';

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Codelab\Cashback\Model\ResourceModel\CashbackTransaction::class);
    }

    /**
     * Load No-Route Indexer.
     *
     * @return $this
     */
    public function noRouteReasons()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return []
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }
}

