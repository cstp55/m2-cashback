<?php

namespace Codelab\Cashback\Block;

use Exception;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Codelab\Cashback\Helper\Customer;
use Codelab\Cashback\Helper\Data as DataHelper;
use Codelab\Cashback\Model\CreditFactory;
use Codelab\Cashback\Model\GiftCard;
use Codelab\Cashback\Model\Transaction;

/**
 * Class Dashboard
 * @package Codelab\Cashback\Block
 */
class Dashboard extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CreditFactory
     */
    protected $_creditFactory;

    /**
     * @var Customer
     */
    protected $cashbackHelper;

    /**
     * @var Transaction
     */
    protected $_transaction;


    /**
     * Dashboard constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CreditFactory $creditFactory
     * @param DataHelper $cashbackHelper
     * @param Transaction $transaction
     * @param GiftCard $giftCard
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CreditFactory $creditFactory,
        DataHelper $cashbackHelper,
        Transaction $transaction,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->_creditFactory  = $creditFactory;
        $this->cashbackHelper  = $cashbackHelper;
        $this->_transaction    = $transaction;
        parent::__construct($context, $data);
    }

    /**
     * Returns popup config
     *
     * @return array
     * @throws Exception
     * @throws NoSuchEntityException
     */
    public function getConfig()
    {
        $customer = $this->customerSession->getCustomer();
        if (!$customer || !$customer->getId()) {
            return [];
        }

        $emailEnable       = true;//$this->cashbackHelper->getEmailConfig('enable');
        $creditEmailEnable = true;//$this->cashbackHelper->getEmailConfig('credit/enable');


        return [
            'baseUrl'        => $this->getBaseUrl(),
            'customerEmail'  => $customer->getEmail(),
            'balance'        => $this->cashbackHelper->getCustomerBalance($customer->getId(), true, true),
            'transactions'   => $this->_transaction->getTransactionsForCustomer($customer->getId()),
            'notification'   => [
                'enable'               => $emailEnable,
                'creditEnable'         => $creditEmailEnable,
                'creditNotification'   => true
            ]
        ];
    }
}
