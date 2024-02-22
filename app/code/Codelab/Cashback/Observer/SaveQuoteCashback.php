<?php 
namespace Codelab\Cashback\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Codelab\Cashback\Model\CashbackManagement;
use Codelab\Cashback\Logger\Logger;
use Codelab\Cashback\Helper\Data as CashbackHelper;

class SaveQuoteCashback implements ObserverInterface
{
    /**
     * @var CashbackManagement
     */
    protected $cashbackManagement;

    /**
     * OrderSaveAfter constructor.
     *
     * @param Helper $helper
     */
    public function __construct(
        CashbackManagement $cashbackManagement,
        CashbackHelper $cashbackHelper,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->cashbackHelper = $cashbackHelper;
        $this->cashbackManagement = $cashbackManagement;
    }

    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $calculatedCashbackAmount = $this->calculateTotalCashback($quote);
        $this->logger->info(__METHOD__." ".$calculatedCashbackAmount);
        $quote->setCashbackAmount($calculatedCashbackAmount);
        //$quote->setCashbackCreditAmount($quote->getCashbackCreditAmount());
        $quote->save();
    }

    private function calculateTotalCashback($quote)
    {
        return $this->cashbackManagement->getCashbackValueForQuoteId($quote->getId());
    }
}
