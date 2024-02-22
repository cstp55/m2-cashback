<?php
namespace Codelab\Cashback\Observer;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Codelab\Cashback\Model\CashbackFactory;
use Codelab\Cashback\Model\Transaction\Action as TransactionAction;
use Codelab\Cashback\Logger\Logger;
use Codelab\Cashback\Model\CashbackManagement;
use Codelab\Cashback\Model\TransactionFactory;
use Codelab\Cashback\Helper\Data as Helper;

/**
 * Remove cashback from customer account
 */
class OrderCancelAfter implements ObserverInterface
{
    protected $cashbackManagement;
    protected $transactionFactory;
    protected $date;
    protected $cashbackFactory;
    protected $logger;
    protected $_helper;
     /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    public function __construct(
        CashbackFactory $cashbackFactory,
        Logger $logger,
        DateTime $date,
        TransactionFactory $transactionFactory,
        CashbackManagement $cashbackManagement,
        Helper $helper,
        \Magento\Framework\App\State $state
    ) {
        $this->state = $state;
        $this->_helper            = $helper;
        $this->transactionFactory = $transactionFactory;
        $this->cashbackManagement = $cashbackManagement;
        $this->cashbackFactory = $cashbackFactory;
        $this->logger = $logger;
        $this->date = $date;
    }

    public function execute(Observer $observer)
    {
        /**
         * When Order is canceled from paymnet getway. In this case if order is used the cashback credit balance then I deduct here
         */
        if ($this->state->getAreaCode() != \Magento\Framework\App\Area::AREA_FRONTEND) {
            return;
        }
        $order = $observer->getEvent()->getOrder();
        //check quote have cashback credit
        $cashbackCredit  = $order->getCashbackCreditAmount();
        if (abs($cashbackCredit) > 0.001) {
            $this->revertCashback($order);
        }
        return $this;
    }

    private function revertCashback($order)
    {   
        $cashbackCredit = $order->getCashbackCreditAmount();
        if ($cashbackCredit && abs($cashbackCredit) > 0.0001) {
            try {
                $this->transactionFactory->create()
                    ->createTransaction(
                        TransactionAction::ACTION_REVERT,
                        abs($cashbackCredit),
                        $order->getCustomerId(),
                        ['order_increment_id' => $order->getIncrementId()]
                    );
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
        return $this;
    }
}