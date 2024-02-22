<?php
namespace Codelab\Cashback\Cron;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Codelab\Cashback\Helper\Data;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Transaction;
use Mageplaza\GiftCard\Model\TransactionFactory;
use Codelab\Cashback\Model\CashbackManagement;
use Codelab\Cashback\Model\CashbackFactory;
use Codelab\Cashback\Model\ResourceModel\Cashback\CollectionFactory as CashbackCollectionFactory;
use Codelab\Cashback\Model\Transaction\Action as TransactionAction;
use Codelab\Cashback\Logger\Logger;

class ExpireCashback
{
    protected $cashbackCollectionFactory;
    protected $date;
    protected $cashbackHelper;
    protected $giftCardFactory;
    protected $transactionFactory;
    protected $giftCardHelper;
    protected $cashbackManagement;
    private $logger;

    public function __construct(
        CashbackCollectionFactory $cashbackCollectionFactory,
        DateTime $date,
        Data $cashbackHelper,
        TransactionFactory $transactionFactory,
        CashbackManagement $cashbackManagement,
        CashbackFactory $cashbackFactory,
        Logger $logger
    ) {
        $this->cashbackCollectionFactory = $cashbackCollectionFactory;
        $this->date = $date;
        $this->cashbackHelper = $cashbackHelper;
        $this->transactionFactory = $transactionFactory;
        $this->cashbackManagement = $cashbackManagement;
        $this->cashbackFactory = $cashbackFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        return;
        $this->logger->info("cron job running expire_cashback_to wallet ".__LINE__);
        $expiryDays = $this->cashbackHelper->getCreditDays();
        $dateLimit = $this->date->gmtDate('Y-m-d H:i:s', strtotime('+1 days'));
        $cashbacks = $this->cashbackCollectionFactory->create()
            ->addFieldToFilter('expiry_date', ['lteq' => $dateLimit])
            ->addFieldToFilter('status', 1);
        foreach ($cashbacks as $cashback) {
            try {
                $available_amount = $cashback->getData('amount') - $cashback->getData('used_amount');
                if ($available_amount>0) {
                    $this->cashbackFactory->create()->load($cashback->getId())->setStatus(0)->save();
                    $this->reduceCashbackToWallet($cashback);
                }
            } catch (\Exception $e) {
                $this->logger->error('Error occurred: ' . $e->getMessage());
            }
        }
    }
    
    private function reduceCashbackToWallet($cashback)
    {
        try {
            $available_amount = -($cashback->getData('amount') - $cashback->getData('used_amount'));
            $this->logger->info("cashback ".$available_amount);
            $data = ['reason' => "Cashback Expired", 'date' => $this->date->gmtDate('Y-m-d H:i:s')];
            $transaction = $this->transactionFactory->create()->createTransaction(TransactionAction::ACTION_EXPIRED, $available_amount, $cashback->getCustomerId(), $data);
        } catch (\Exception $e) {
            $this->logger->error('Error occurred: ' . $e->getMessage());
        }
    }
}
