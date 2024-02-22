<?php
namespace Codelab\Cashback\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Order;

class CashbackDetails extends Template
{
    protected $checkoutSession;
    protected $order;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        Order $order,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        parent::__construct($context, $data);
    }

    public function getCashbackAmount()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        if ($orderId) {
            $order = $this->order->load($orderId);
            return $order->getData('cashback_amount');
        }
        return null;
    }
}
