<?php
namespace Codelab\Cashback\Api;

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