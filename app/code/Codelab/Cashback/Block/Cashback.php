<?php
namespace Codelab\Cashback\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Catalog\Block\Product\View\Attributes;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Cashback extends Attributes
{
    protected $scopeConfig;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $registry, $priceCurrency, $data);
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
    }

    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue('cashback/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCashbackAmount()
    {
        $product = $this->getProduct();
    
        if (!$product) {
            return 0; // Return 0 if product is not found
        }

        $cashbackPercentage = $product->getData('cashback');
        $price = $product->getData('price');
        if($cashbackPercentage){
            $cashbackAmount = ($cashbackPercentage / 100) * $price;
            $cashbackAmount = $this->priceCurrency->convertAndFormat($cashbackAmount);
            return $cashbackAmount;
        }
        return 0;
    }

    public function getCashbackMessage()
    {
        $message = $this->scopeConfig->getValue('cashback/general/message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return str_replace('{{amount}}', $this->getCashbackAmount(), $message);
    }

}
