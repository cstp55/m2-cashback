<?php

namespace Codelab\Cashback\Model\Total\Invoice;

use Magento\Catalog\Model\ProductFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Class Discount
 * @package Mageplaza\GiftCard\Model\Total\Invoice
 */
class Discount extends AbstractTotal
{
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @param ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($data);
    }

    /**
     * Collect invoice subtotal
     *
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $order              = $invoice->getOrder();
        $baseCreditDiscount = $order->getBashCashbackCreditAmount();

        if ((!$baseCreditDiscount) || (abs($baseCreditDiscount) == 0)) {
            return $this;
        }
        $invoiceSubtotal = $invoice->getSubtotal();
        
        $orderSubtotal = $order->getSubtotal();


        $rate = $invoiceSubtotal / $orderSubtotal;
        if ($baseCreditDiscount) {
            $creditDiscount = $order->getCashbackCreditAmount();

            $cashbackCreditDiscount     = $invoice->roundPrice($creditDiscount * $rate, 'regular', true);
            $baseCashbackCreditDiscount = $invoice->roundPrice($baseCreditDiscount * $rate, 'base', true);

            foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
//                die(json_encode($previousInvoice->getData()));
                $baseCreditDiscount -= $previousInvoice->getBaseCashbackCreditAmount();
                $creditDiscount     -= $previousInvoice->getCashbackCreditAmount();
            }

            if ($invoice->isLast()) {
                $cashbackCreditDiscount     = $creditDiscount;
                $baseCreditcreditDiscount = $baseCreditDiscount;
            } else {
                $cashbackCreditDiscount     = max($creditDiscount, $cashbackCreditDiscount);
                $baseCashbackCreditDiscount = max($baseCreditDiscount, $baseCashbackCreditDiscount);
            }

            $invoice->setCashbackCreditAmount($cashbackCreditDiscount);
            $invoice->setBaseCashbackCreditAmount($baseCashbackCreditDiscount);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $CashbackCreditDiscount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseCashbackCreditDiscount);
        }

        return $this;
    }
}
