<?php

namespace Codelab\Cashback\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveOrderDataToInvoice implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $cashbackData = $order->getCashbackCreditAmount();
        if ($cashbackData && abs($cashbackData) > 0) {
            $invoice->setCashbackCreditAmount($cashbackData);
            $invoice->setBaseCashbackCreditAmount($cashbackData);
        }
    }
}
