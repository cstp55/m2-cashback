<?php
namespace Codelab\Cashback\Plugin;

use Codelab\Cashback\Helper\Data as Helper;

class CheckoutConfigPlugin
{
    protected $cart;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        Helper $helper
    ) {
        $this->helper = $helper;
        $this->cart = $cart;
    }

    public function afterGetConfig(
        \Mageplaza\Osc\Model\DefaultConfigProvider $subject,
        
        array $result
    ) {
        $result['codelab']['hasMpGiftCard'] = $this->checkMpGiftCardInCart();
        $result['codelab']['cashback'] = $this->getCashbackConfig();
        return $result;
    }

    protected function checkMpGiftCardInCart()
    {
        $items = $this->cart->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            if ($item->getProductType() !== 'mpgiftcard') {
                return false;
            }
        }
        return true;
    }

    public function getCashbackConfig()
    {
        return ['checkout_message'=>$this->helper->getCashbackCheckoutMessage()];
    }
}
