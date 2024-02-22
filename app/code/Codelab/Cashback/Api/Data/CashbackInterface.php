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


namespace Codelab\Cashback\Api\Data;

/**
 * Cashback Model Interface
 */
interface CashbackInterface
{
    public const ENTITY_ID = 'entity_id';

    public const ORDER_ID = 'order_id';

    public const CUSTOMER_ID = 'customer_id';

    public const STATUS = 'status';

    public const AMOUNT = 'amount';

    public const CREATED_AT = 'created_at';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Codelab\Cashback\Api\Data\CashbackInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set OrderId
     *
     * @param int $orderId
     * @return Codelab\Cashback\Api\Data\CashbackInterface
     */
    public function setOrderId($orderId);
    /**
     * Get OrderId
     *
     * @return int
     */
    public function getOrderId();
    /**
     * Set Status
     *
     * @param int $status
     * @return Codelab\Cashback\Api\Data\CashbackInterface
     */
    public function setStatus($status);
    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();
    /**
     * Set CashbackAmount
     *
     * @param float $cashbackAmount
     * @return Codelab\Cashback\Api\Data\CashbackInterface
     */
    public function setAmount($amount);
    /**
     * Get CashbackAmount
     *
     * @return float
     */
    public function getAmount();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Codelab\Cashback\Api\Data\CashbackInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
    public function getCustomerId();
    public function setCustomerId($customerId);
}

