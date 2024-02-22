<?php

namespace Codelab\Cashback\Plugin\Quote;

use Closure;
use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Model\Quote;
use Codelab\Cashback\Logger\Logger;

use Codelab\Cashback\Helper\Data as CashbackHelper;
/**
 * Class CartTotalRepository
 * @package Mageplaza\GiftCard\Plugin\Quote
 */
class CartTotalRepository
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var TotalsExtensionFactory
     */
    protected $totalExtensionFactory;

    /**
     * @var Checkout
     */
    protected $checkoutHelper;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * CartTotalRepository constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param TotalsExtensionFactory $totalExtensionFactory
     * @param Checkout $gcCheckoutHelper
     * @param GiftCardFactory $giftCardFactory
     * @param RequestInterface $request
     * @param Repository $assetRepo
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        TotalsExtensionFactory $totalExtensionFactory,
        RequestInterface $request,
        Repository $assetRepo,
        UrlInterface $urlBuilder,
        Logger $logger,
        CashbackHelper $cashbackHelper
    ) {
        $this->logger                = $logger;
        $this->cashbackHelper        = $cashbackHelper;
        $this->quoteRepository       = $quoteRepository;
        $this->totalExtensionFactory = $totalExtensionFactory;
        $this->request               = $request;
        $this->_assetRepo            = $assetRepo;
        $this->_urlBuilder           = $urlBuilder;
    }

    /**
     * @param CartTotalRepositoryInterface $subject
     * @param Closure $proceed
     * @param $cartId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundGet(CartTotalRepositoryInterface $subject, Closure $proceed, $cartId)
    {
        $quoteTotals = $proceed($cartId);

        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        // if (!$this->checkoutHelper->cashbackCreditEnable($quote)) {
        //     return $quoteTotals;
        // }

        $cashbackConfig = $this->getCashbackConfig($quote);

        $totalsExtension = $quoteTotals->getExtensionAttributes() ?: $this->totalExtensionFactory->create();
        $totalsExtension->setCashbackConfig(CashbackHelper::jsonEncode($cashbackConfig));

        $quoteTotals->setExtensionAttributes($totalsExtension);

        return $quoteTotals;
    }

    /**
     * @param Quote $quote
     * @param false $graphQl
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCashbackConfig($quote, $graphQl = false)
    {
        $this->logger->info(__METHOD__);
        $enableCashback = true;
        $customerBalance = $this->cashbackHelper->getCustomerBalance($quote->getCustomerId());
        if($this->cashbackHelper->getTotalAmountForDiscount($quote, true) >=0 ){
            $maxUsed  = max($customerBalance, $this->cashbackHelper->getTotalAmountForDiscount($quote, true));
        }else{
            $maxUsed  = min($customerBalance, $this->cashbackHelper->getTotalAmountForDiscount($quote, true));
        }
        $creditUsed = min($maxUsed, $customerBalance);
        // $maxUsed         = min($customerBalance, $quote->getGrandTotal())
        return [
            'enableCashbackCredit' => true,//$enableCashback && ($maxUsed > 0.0001),
            'balance'          => $customerBalance,
            'maxUsed'          => $maxUsed,
            'creditUsed'       => $creditUsed,
            'css'              => [
                $this->getViewFileUrl('Mageplaza_Core/css/ion.rangeSlider.css'),
                $this->getViewFileUrl('Mageplaza_Core/css/skin/ion.rangeSlider.skinModern.css')
            ]
        ];
    }


   
    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     *
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);

            return $this->_assetRepo->getUrlWithParams($fileId, $params);
        } catch (Exception $e) {
            return $this->_urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }
}
