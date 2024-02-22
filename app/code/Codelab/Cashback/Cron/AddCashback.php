<?php
namespace Codelab\Cashback\Cron;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Codelab\Cashback\Helper\Data;
use Codelab\Cashback\Helper\Data as DataHelper;
use Codelab\Cashback\Model\GiftCard;
use Codelab\Cashback\Model\Transaction;
use Codelab\Cashback\Model\TransactionFactory;
use Codelab\Cashback\Model\CashbackManagement;
use Codelab\Cashback\Model\CashbackFactory;
use Codelab\Cashback\Model\Transaction\Action as TransactionAction;
use Codelab\Cashback\Logger\Logger;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class AddCashback
{
    protected $orderCollectionFactory;
    protected $date;
    protected $cashbackHelper;
    protected $transactionFactory;
    protected $cashbackManagement;
    private $logger;
    protected $orderItemRepository;


    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        DateTime $date,
        Data $cashbackHelper,
        TransactionFactory $transactionFactory,
        CashbackManagement $cashbackManagement,
        CashbackFactory $cashbackFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Logger $logger,
        OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->date = $date;
        $this->cashbackHelper = $cashbackHelper;
        $this->transactionFactory = $transactionFactory;
        $this->cashbackManagement = $cashbackManagement;
        $this->cashbackFactory = $cashbackFactory;
        $this->logger = $logger;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function execute()
    {
        $this->logger->info("add_cashack_to_wallet job is running");
        $this->logger->info(__METHOD__);
        $creaditDays = $this->cashbackHelper->getCreditDays();
        $dateLimit = $this->date->gmtDate('Y-m-d H:i:s', strtotime('-'.$creaditDays.' days'));
        $this->logger->info("datelimit". $dateLimit);
        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('cashback_status', 0)
            // ->addFieldToFilter('cashback_amount', ['gt',0])
            ->addFieldToFilter('state', ['eq' => \Magento\Sales\Model\Order::STATE_COMPLETE]);
            if($creaditDays){
                $orders =  $orders->addFieldToFilter('created_at', ['lteq' => $dateLimit]);
            }
        foreach ($orders as $order) {
            try {
                $this->logger->info("order id for add cashback ".$order->getId());
                if ($order->getCustomerId()) {
                    $this->addCashbackToWallet($order);
                }
                $this->addCashbackToItem($order);
                $this->updateOrderCashbackStatus($order->getId());
            } catch (\Exception $e) {
                $this->logger->info("exepetion occur method".__METHOD__);
                $this->logger->error('Error occurred: ' . $e->getMessage());
            }
        }
    }

    private function addCashbackToWallet($order)
    {
        try {
            $expirydays = $this->cashbackHelper->getExpiryDays();
            $expiryDay = $this->date->gmtDate('jS F Y', strtotime('+'.$expirydays.' days'));
            $totalCashback = $this->cashbackManagement->getCashbackUsingOrder($order->getId());
            if ($totalCashback > 0) {
                $this->logger->info("cashback " . $totalCashback);
                $data = [
                    'order_increment_id' => $order->getIncrementId(), 
                    'auth' => $order->getCustomerName(),
                    'reason' => "cashback for order",
                    'expired_at' =>$expiryDay
                ];
                $transaction = $this->transactionFactory->create()->createTransaction(
                    TransactionAction::ACTION_CREDIT, 
                    $totalCashback, 
                    $order->getCustomerId(), 
                    $data
                );
                // Implement the logic to add cashback to the customer's wallet
                $this->saveCashbackTransaction($order->getId(), $totalCashback, $order->getCustomerId());
            }
        } catch (\Exception $e) {
            $this->logger->error('Error occurred: ' . $e->getMessage());
        }         
    }
    public function saveCashbackTransaction($orderId, $totalcashback, $customerId)
    {
        $this->logger->info("saveCashbackTransaction " . $totalcashback);
        $expirydays = $this->cashbackHelper->getExpiryDays();
        $expiryDay = $this->date->gmtDate('Y-m-d H:i:s', strtotime('+'.$expirydays.' days'));
        $cashbackData = $this->cashbackFactory->create()->load($orderId, 'order_id')->getData();
        if (!$cashbackData) {
            $this->cashbackFactory->create()
                ->setOrderId($orderId)
                ->setAmount($totalcashback)
                ->setData('expiry_date', $expiryDay)
                ->setData('customer_id', $customerId)
                ->save();
        }
    }
    
    public function updateOrderCashbackStatus($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        $order->setData('cashback_status', 1);
        $order->save();
    }

    private function addCashbackToItem($order)
    {
        try {
            foreach ($order->getAllVisibleItems() as $item) {
                $cashbackPercentage = $this->calculateCashbackPercentage($item);
                $cashbackAmount = $this->calculateCashbackAmount($item, $cashbackPercentage);
                $item->setData('cashback_percentage', $cashbackPercentage);
                $item->setData('amount', $cashbackAmount);
                $this->orderItemRepository->save($item);
            }

        } catch (\Exception $e) {
            $this->logger->error('Error occurred: ' . $e->getMessage());
        }         
    }

    private function calculateCashbackPercentage($item)
    {
        $product = $item->getProduct();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $product = $productRepository->getById($product->getId());
        $cashbackPercentage = $product->getData('cashback');
        return $cashbackPercentage?$cashbackPercentage:0;
    }

    private function calculateCashbackAmount($item, $cashbackPercentage)
    {
        $totalCashback = 0;
        if ($cashbackPercentage) {
            $totalCashback += ($item->getPrice() * $cashbackPercentage / 100) * $item->getData('qty');
        }
        return $totalCashback;
    }
}
