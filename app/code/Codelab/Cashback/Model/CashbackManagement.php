<?php
namespace Codelab\Cashback\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Codelab\Cashback\Api\CashbackManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Codelab\Cashback\Logger\Logger;
use Magento\Quote\Api\CartRepositoryInterface;

class CashbackManagement implements CashbackManagementInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    protected $cartRepository;
    protected $helper;
    protected $maskedQuoteIdToQuoteId;
    protected $priceCurrency;
    private $logger;

    public function __construct(
        \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Codelab\Cashback\Helper\Data $helper,
        PriceCurrencyInterface $priceCurrency,
        OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\Session $customerSession,
        CartRepositoryInterface      $quoteRepository,
        Logger $logger
    ) {
        $this->quoteRepository     = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->priceCurrency = $priceCurrency;
        $this->cartRepository = $cartRepository;
        $this->helper = $helper;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->orderRepository = $orderRepository;
    }

    public function getCashbackValueForCart($maskedId)
    {
        try{
            if (!$this->isCustomerLoggedIn()) {
                $maskedId = $this->maskedQuoteIdToQuoteId->execute($maskedId);
            }
            $cart = $this->cartRepository->get($maskedId);
            $items = $cart->getItems();
            $totalCashback = 0;
            foreach ($items as $item) {
                $product = $item->getProduct();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
                $product = $productRepository->getById($product->getId());
                $cashbackPercentage = $product->getData('cashback');
                if($cashbackPercentage){
                    $totalCashback += ($item->getPrice() * $cashbackPercentage / 100) * $item->getQty();
                }
            }
            if ($totalCashback) {
                return $this->priceCurrency->convertAndFormat($totalCashback);
            }
            return 0;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->error('Error in getCashbackValueForCart: ' . $e->getMessage());
            return 0; // or handle it as per your logic
        }
    }

    public function getCashbackValueForQuoteId($quoteId)
    {
        $cart = $this->cartRepository->get($quoteId);
        $items = $cart->getAllVisibleItems();
        $totalCashback = 0;
        foreach ($items as $item) {
            $product = $item->getProduct();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
            $product = $productRepository->getById($product->getId());
            $cashbackPercentage = $product->getData('cashback');
            $totalCashback += ($item->getPrice() * $cashbackPercentage / 100) * $item->getQty();
        }
        return $totalCashback; //$this->priceCurrency->convertAndFormat($totalCashback);
    }

    public function getCashbackUsingOrder($orderId)
    {
        
        $totalCashback=0;
        // Load the order
        $this->logger->info("lineno".__METHOD__);
        $this->logger->info("order info".$orderId);
        $order = $this->orderRepository->get($orderId);
        $this->logger->info("order cashback".$order->getData('cashback_amount'));
        if ($order->getData('cashback_amount')>0) {
            return $order->getData('cashback_amount');
        }
        $items =  $order->getItems();
        foreach ($items as $item) {
            $this->logger->info("enter loop".__LINE__);
            $product = $item->getProduct();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
            $product = $productRepository->getById($product->getId());
            $cashbackPercentage = $product->getData('cashback');
            $this->logger->info("cashback percentage ".$cashbackPercentage);
            if ($cashbackPercentage) {
                $totalCashback = $totalCashback + ($item->getPrice() * $cashbackPercentage / 100) * $item->getData('qty');
                $this->logger->info("enter if condition ".$totalCashback);
            }
        }
        $this->logger->info("order info cashback ".$totalCashback);
        return  $totalCashback;
    }
    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    
    /**
     * Apply cashback credit
     */
    public function credit($cartId, $amount)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $this->helper->applyCredit($amount, $quote);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not apply gift credit'));
        }

        return true;
    }
}
