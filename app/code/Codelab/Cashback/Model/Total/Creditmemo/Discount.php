<?php

namespace Codelab\Cashback\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Codelab\Cashback\Helper\Data;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class Discount
 *
 * @package Codelab\Cashback\Model\Total\Creditmemo
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
     * Collect Creditmemo subtotal
     *
     * @param Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $order              = $creditmemo->getOrder();
        $baseCreditDiscount = $order->getBaseCashbackCreditAmount();
        $isRefundCB       = false;


        if (!$baseCreditDiscount && !$isRefundGC) {
            return $this;
        }

        $creditmemoSubtotal = $creditmemo->getSubtotal();
        
        $orderSubtotal = $order->getSubtotal();

        $rate = $creditmemoSubtotal / $orderSubtotal;

        if ($baseCreditDiscount) {
            $orderDiscount = $order->getCashbackCreditAmount();
            $baseCashbackDiscount = $creditmemo->roundPrice($baseCreditDiscount * $rate, 'base', true);

            $baseInvoiceDiscount = 0;
            $invoiceDiscount     = 0;
            foreach ($creditmemo->getOrder()->getInvoiceCollection() as $previousInvoice) {
                $baseInvoiceDiscount += $previousInvoice->getBaseCashbackCreditAmount();
                $invoiceDiscount     += $previousInvoice->getCashbackCreditAmount();
            }
            foreach ($creditmemo->getOrder()->getCreditmemosCollection() as $previousCreditmemo) {
                $baseInvoiceDiscount -= $previousCreditmemo->getBaseCashbackCreditAmount();
                $invoiceDiscount     -= $previousCreditmemo->getCashbackCreditAmount();
            }

            $cashbackDiscount     = max($invoiceDiscount, $orderDiscount);
            $baseCashbackDiscount = max($baseInvoiceDiscount, $baseCashbackDiscount);

            $creditmemo->setCashbackCreditAmount($cashbackDiscount);
            $creditmemo->setBaseCashbackCreditAmount($baseCashbackDiscount);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $cashbackDiscount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseCashbackDiscount);
        }

       // $creditmemo->setRefundGiftCardFlag(true);

        return $this;
    }
}
