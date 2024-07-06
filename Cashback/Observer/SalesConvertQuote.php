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
namespace Aprilo\Cashback\Observer;

use Aprilo\Cashback\Model\CashbackManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aprilo\Cashback\Logger\Logger;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Aprilo\Cashback\Model\TransactionFactory;
use Aprilo\Cashback\Model\Transaction\Action;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Aprilo\Cashback\Helper\Data;


class SalesConvertQuote implements ObserverInterface
{

    /**
     * @var CashbackManagement
     */
    protected $cashbackManagement;
    protected $transactionFactory;
    protected $date;
    protected $logger;
    protected $cashbackHelper;

    /**
     * OrderSaveAfter constructor.
     *
     * @param Helper $helper
     */
    public function __construct(
        CashbackManagement $cashbackManagement,
        TransactionFactory $transactionFactory,
        DateTime $date,
        Data $cashbackHelper,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->date = $date;
        $this->cashbackHelper = $cashbackHelper;
        $this->transactionFactory = $transactionFactory;
        $this->cashbackManagement = $cashbackManagement;
    }
    public function execute(Observer $observer)
    {
        $this->logger->info(__METHOD__);
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        $cashback = $this->cashbackManagement->getCashbackValueForQuoteId($quote->getId());

        $order->setCashbackAmount($quote->getCashbackAmount());
        $order->setCashbackAmount($cashback);
        //save the cashback credit in sales_order table
        $order->setCashbackCreditAmount(-$quote->getCashbackCreditAmount());
        $order->setBaseCashbackCreditAmount(-$quote->getCashbackCreditAmount());
        // Loop through all quote items
        foreach ($quote->getAllItems() as $quoteItem) {

            $orderItem = $order->getItemByQuoteItemId($quoteItem->getId());
            if ($orderItem) {
                // Calculate cashback percentage for the item
                $cashbackPercentage = $this->calculateCashbackPercentage($quoteItem, $cashback);
                // Set cashback percentage to order item
                $orderItem->setCashbackPercentage($cashbackPercentage);
            }
        }
        $order->save();
        if ($cashback) {
            $this->addPendingTrancation($order, $cashback);
        }
        if($quote->getCashbackCreditAmount()>0.001){
            $this->addSpendTrancation($order, $quote->getCashbackCreditAmount());
        }
    }
    private function calculateCashbackPercentage(QuoteItem $item, $totalCashback)
    {
        $product = $item->getProduct();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $product = $productRepository->getById($product->getId());
        $cashbackPercentage = $product->getData('cashback');
        return $cashbackPercentage; 
    }

    private function addSpendTrancation($order, $amount)
    {
        if($order->getCustomerId()){
            $dateLimit = $this->date->gmtDate('jS F Y',);
            $this->transactionFactory->create()
                ->createTransaction(
                    Action::ACTION_SPEND,
                    -$amount,
                    $order->getCustomerId(),
                    ['date' => $dateLimit, 'amount'=>$amount, 'order_increment_id' => $order->getIncrementId()]
                );
        }
    }
    /**
     * add pending transaction
     */
    private function addPendingTrancation($order, $cashback)
    {
        if($order->getCustomerId()){
            $creaditDays = $this->cashbackHelper->getCreditDays();
            $dateLimit = $this->date->gmtDate('jS F Y', strtotime('-'.$creaditDays.' days'));
            $this->transactionFactory->create()
                ->createTransaction(
                    Action::ACTION_PENDING,
                    $cashback,
                    $order->getCustomerId(),
                    ['date' => $dateLimit, 'amount'=>$cashback, 'order_increment_id' => $order->getIncrementId()]
                );
        }
    }
}
