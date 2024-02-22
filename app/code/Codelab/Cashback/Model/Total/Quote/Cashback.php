<?php
namespace Codelab\Cashback\Model\Total\Quote;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Codelab\Cashback\Helper\Data as CashbackHeper;
use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address;
use Codelab\Cashback\Logger\Logger;

class Cashback extends AbstractTotal
{

    /**
     * @var string
     */
    private $_creditCode = 'cashback_credit';

    protected $cashbackHelper;
   
    /**
     * Discount constructor.
     *
     * @param GiftCardCheckoutHelper $helper
     * @param GiftCardFactory $giftCardFactory
     * @param ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        CashbackHeper $cashbackHelper,
        ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->cashbackHelper   = $cashbackHelper;
        $this->_messageManager  = $messageManager;
        $this->priceCurrency    = $priceCurrency;
        $this->setCode('cashback');
    }

    /**
     * Collect cashback total
     *
     * @param Quote $quote
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $this->logger->info("totals" . $this->logger->info(json_encode($total->getData())));
        $cashbackAmount = $this->calculateCashback($quote);
        // Apply to quote
        // $total->addTotalAmount($this->getCode(), $cashbackAmount);
        // $total->addBaseTotalAmount($this->getCode(), $cashbackAmount);
        $this->calculateCashbackCreditDiscount($quote, $total);
        return $this;
    }

    /**
     * Fetch cashback total
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        $totalArray = [];
        $cashbackCredit = 0;

        $totalArray[] = [
            'code' => $this->getCode(),
            'title' => __('Cashback'),
            'value' => $this->calculateCashback($quote)
        ];

        $cashbackCredit = (float) $quote->getCashbackCreditAmount();
        if ($cashbackCredit > 0.0001) {
            $totalArray[] = [
                'code'  => $this->_creditCode,
                'title' => __('Cashback Credit'),
                'value' => -$cashbackCredit
            ];
        }else{
            $totalArray[] = [
                'code'  => $this->_creditCode,
                'title' => __('Cashback Credit'),
                'value' => 0
            ];
        }

        return $totalArray;
    }

   /**
     * Calculate cashback amount based on your logic
     *
     * @param Quote $quote
     * @return float
     */
    protected function calculateCashback(Quote $quote)
    {
        //  I'll just return a cashback to quote fixed amount.
        return max(0, $quote->getCashbackAmount());
    }

     /**
     * Discount by Cashback credit
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function calculateCashbackCreditDiscount(Quote $quote, Total $total)
    {
        $total->setTotalAmount($this->_creditCode, 0);
        $total->setBaseTotalAmount($this->_creditCode, 0);

        $creditAmount = $this->cashbackHelper->getCashbackCreditUsed($quote);
        $this->logger->info("cashback credit ".$creditAmount);
        if ($creditAmount < 0) {
            $quote->setCashbackCreditAmount(0);
            return $this;
        }
        $this->logger->info(__METHOD__);
        
        $customerBalance = $this->cashbackHelper->getCustomerBalance($quote->getCustomerId());
        $this->logger->info("cashback balance ".$customerBalance);
        $baseTotalAmount = $this->getTotalAmountForDiscount($quote, $total);
        $totalAmount     = $this->cashbackHelper->getPriceCurrency()->convertAndRound($baseTotalAmount);
        $this->logger->info("total amount ".$totalAmount);
        $creditAmount     = min($creditAmount, $totalAmount, $customerBalance);
        $baseCreditAmount = $creditAmount / $this->cashbackHelper->convertPrice(1, false);
        $this->logger->info("cashback credit amount ".$creditAmount);
        $total->setTotalAmount($this->_creditCode, -$creditAmount);
        $total->setBaseTotalAmount($this->_creditCode, -$baseCreditAmount);
        $total->setGrandTotal($total->getGrandTotal() - $creditAmount);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseCreditAmount);
        $total->setCashbackCredit($creditAmount);
        $quote->setCashbackCreditAmount($creditAmount);
        return $this;
    }
    /**
     * Calculate total amount for discount
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return float
     */
    public function getTotalAmountForDiscount(Quote $quote, Total $total)
    {
        $this->logger->info(__METHOD__);
        $this->logger->info(json_encode($total->getData()));
        $discountTotal = $total->getBaseGrandTotal();
        
        // if (!$this->_helper->canUsedForTax($quote->getStoreId())) {
        //     $discountTotal -= $total->getBaseTaxAmount();
        // }
        // if (!$this->_helper->canUsedForShipping($quote->getStoreId())) {
        //     $discountTotal -= $total->getBaseShippingAmount();
        // }
        return $discountTotal;
    }
}
