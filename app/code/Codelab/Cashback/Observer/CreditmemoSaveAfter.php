<?php
namespace Codelab\Cashback\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Codelab\Cashback\Model\CashbackFactory;
use Codelab\Cashback\Model\Transaction\Action as TransactionAction;
use Codelab\Cashback\Logger\Logger;
use Codelab\Cashback\Model\CashbackManagement;
use Codelab\Cashback\Model\TransactionFactory;
use Magento\Sales\Model\Order;

class CreditmemoSaveAfter implements ObserverInterface
{
    protected $cashbackManagement;
    protected $transactionFactory;
    protected $date;
    protected $cashbackFactory;
    protected $logger;

    public function __construct(
        CashbackFactory $cashbackFactory,
        Logger $logger,
        DateTime $date,
        TransactionFactory $transactionFactory,
        CashbackManagement $cashbackManagement
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->cashbackManagement = $cashbackManagement;
        $this->cashbackFactory = $cashbackFactory;
        $this->logger = $logger;
        $this->date = $date;
    }

    public function execute(Observer $observer)
    {
        try {
            $creditmemo = $observer->getEvent()->getCreditmemo();
            $order = $creditmemo->getOrder();
            $cashbackData = $order->getCashbackCreditAmount();
            if ($cashbackData && abs($cashbackData) > 0) {

                $data = [
                    'order_increment_id' => $order->getIncrementId(),
                    'reason' => "Order cancelled",
                    'amount' => $cashbackData,
                    'date' => $this->date->gmtDate('Y-m-d H:i:s')
                ];
                $transaction = $this->transactionFactory->create()->createTransaction(
                    TransactionAction::ACTION_CANCELLED,
                    $cashbackData,
                    $order->getData('customer_id'),
                    $data
                );
                $creditmemo->setCashbackCreditAmount($cashbackData);
                $creditmemo->setBaseCashbackCreditAmount($cashbackData);
                $this->logger->info("Processed credit memo for order ID: " . $order->getIncrementId());
            } else {
                $this->logger->info("No cashback data found for order ID: " . $order->getIncrementId());
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in CreditmemoSaveAfter: ' . $e->getMessage());
        }

        return $this;
    }
}
