<?php
namespace Codelab\Cashback\Block;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Codelab\Cashback\Helper\Data;

class CartCashback extends \Magento\Checkout\Block\Cart\Additional\Info
{
    protected $cart;
    protected $productRepository;

    /**
     * Dependencies Initillization
     * 
     */
    public function __construct(
        Context $context,
        Cart $cart,
        Data $helper,
        PriceCurrencyInterface $priceCurrency,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->cart = $cart;
        $this->priceCurrency = $priceCurrency;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getCashbackAmounts()
    {   
        $product = $this->getItem()->getProduct();
        if ($product) {
            $cashbackPercentage = $this->productRepository->getById($product->getData('entity_id'))->getCashback();
            if ($cashbackPercentage) {
                $price = $product->getPrice()?$product->getPrice():$product->getData('quote_item_price');
                $cashbackAmount = ($cashbackPercentage / 100) * $price;
                $cashbackAmount = $this->priceCurrency->convertAndFormat($cashbackAmount);
                return $this->helper->getCashbackMessage($cashbackAmount);
            }
        }
        return '';
    }

    public function getTotalCashbackAmount()
    {
        $items = $this->cart->getQuote()->getAllVisibleItems();
        $totalCashback = 0;

        foreach ($items as $item) {
            $productId = $item->getProductId();
            $product = $this->productRepository->getById($productId);
            $cashbackPercentage = $product->getData('cashback');
            $price = $item->getPrice();
            $cashbackAmount = (($cashbackPercentage / 100) * $price) * $item->getQty();

            $totalCashback += $cashbackAmount;
        }
        return $totalCashback;
    }

    public function getProductTypeId()
    {
        $productType = '';
        $product = $this->getItem()->getProduct();
        if ($product) {
            $productType = $this->productRepository->getById($product->getData('entity_id'))->getData('type_id');
        }
        return $productType;
    }
}
