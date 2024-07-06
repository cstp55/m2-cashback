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

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Aprilo\Cashback\Model\CashbackManagement;
use Aprilo\Cashback\Model\CashbackFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Aprilo\Cashback\Helper\Data;
use Aprilo\Cashback\Model\TransactionFactory;
use Aprilo\Cashback\Model\Transaction\Action as TransactionAction;


class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var CashbackManagement
     */
    protected $cashbackManagement;

    protected $transactionFactory;

    /**
     * OrderSaveAfter constructor.
     *
     * @param Helper $helper
     */
    public function __construct(
        CashbackManagement $cashbackManagement
    ) {
        $this->cashbackManagement = $cashbackManagement;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getData('quote_id');
        $totalCashback = $this->cashbackManagement->getCashbackValueForQuoteId($quoteId);
        $order->setCashbackAmount($totalCashback);
        $order->save();
        return $this;
    }
}
