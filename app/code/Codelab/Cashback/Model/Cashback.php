<?php

namespace Codelab\Cashback\Model;

/**
 * Cashback Model Class
 */
class Cashback extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface, \Codelab\Cashback\Api\Data\CashbackInterface
{
    final public const NOROUTE_ENTITY_ID = 'no-route';

    final public const CACHE_TAG = 'codelab_cashback_cashback';

    protected $_cacheTag = 'codelab_cashback_cashback';

    protected $_eventPrefix = 'codelab_cashback_cashback';

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Codelab\Cashback\Model\ResourceModel\Cashback::class);
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

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Codelab\Cashback\Model\CashbackInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set OrderId
     *
     * @param int $orderId
     * @return Codelab\Cashback\Model\CashbackInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get OrderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return parent::getData(self::ORDER_ID);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return Codelab\Cashback\Model\CashbackInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Set CashbackAmount
     *
     * @param float $cashbackAmount
     * @return Codelab\Cashback\Model\CashbackInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Get CashbackAmount
     *
     * @return float
     */
    public function getAmount()
    {
        return parent::getData(self::AMOUNT);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Codelab\Cashback\Model\CashbackInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }
    public function getCustomerId()
    {
        return parent::getData(self::CUSTOMER_ID);
    }
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
}

