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
