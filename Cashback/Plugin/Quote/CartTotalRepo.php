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
namespace Aprilo\Cashback\Plugin\Quote;

use Mageplaza\GiftCard\Plugin\Quote\CartTotalRepository as OriginalCartTotalRepository;
use Magento\Quote\Model\Quote;

class CustomCartTotalRepository extends OriginalCartTotalRepository
{
    /**
     * Override getGiftCardConfig method
     *
     * @param Quote $quote
     * @param bool $graphQl
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGiftCardConfig($quote, $graphQl = false)
    {
        
        
        $enableGiftCard = $this->checkoutHelper->canUsedGiftCard($quote);
        $customerBalance = $this->checkoutHelper->getCustomerBalance($quote->getCustomerId());
        $maxUsed         = min($customerBalance, $this->checkoutHelper->getTotalAmountForDiscount($quote, true));

        return [
            'enableGiftCard'   => !$this->checkoutHelper->isUsedCouponBox() && $enableGiftCard,
            'enableMultiple'   => $this->checkoutHelper->isUsedMultipleCode(),
            'canShowDetail'    => (boolean) $this->checkoutHelper->getCheckoutConfig('show_detail'),
            'listGiftCard'     => $this->getGiftCardList(),
            'giftCardUsed'     => $this->getGiftCardsUsed($quote, $graphQl),
            'enableGiftCredit' => $this->checkoutHelper->canUsedCredit() && $enableGiftCard && ($maxUsed > 0.0001),
            'balance'          => $customerBalance,
            'maxUsed'          => $maxUsed,
            'creditUsed'       => $this->checkoutHelper->getGiftCreditUsed($quote),
            'css'              => [
                $this->getViewFileUrl('Mageplaza_Core/css/ion.rangeSlider.css'),
                $this->getViewFileUrl('Mageplaza_Core/css/skin/ion.rangeSlider.skinModern.css')
            ]
        ];

       // $config = parent::getGiftCardConfig($quote, $graphQl);
        // Modify $config as needed
       // return $config;
    }
}
