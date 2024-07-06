<?php
namespace Aprilo\Cashback\Api;
/**
 * Aprilo Software.
 *
 * @category Aprilo Software Private Limited
 * @package aprilo_Cashback
 * @author Aprilo
 * @copyright Copyright (c) Aprilo Software Private Limited (https://Aprilo.com)
 * @license https://store.Aprilo.com/license.html
 */
interface CashbackManagementInterface
{
    /**
     * GET for Cashback api
     * @param string $cartId
     * @return float
     */
    public function getCashbackValueForCart($cartId);

    
    /**
     * Credit amount from a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param double $amount The amount to credit.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function credit($cartId, $amount);
}