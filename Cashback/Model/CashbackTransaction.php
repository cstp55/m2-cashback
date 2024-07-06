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
namespace Aprilo\Cashback\Model;

/**
 * CashbackTransaction Model Class
 */
class CashbackTransaction extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface, \Aprilo\Cashback\Api\Data\CashbackTransactionInterface
{
    final public const NOROUTE_ENTITY_ID = 'no-route';

    final public const CACHE_TAG = 'Aprilo_cashback_cashbacktransaction';

    protected $_cacheTag = 'Aprilo_cashback_cashbacktransaction';

    protected $_eventPrefix = 'Aprilo_cashback_cashbacktransaction';

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Aprilo\Cashback\Model\ResourceModel\CashbackTransaction::class);
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

