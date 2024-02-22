<?php
namespace Codelab\Cashback\Helper;


use IntlDateFormatter;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Codelab\Cashback\Model\ResourceModel\Cashback\CollectionFactory;
use Codelab\Cashback\Model\CashbackFactory;
use Codelab\Cashback\Model\CashbackTransactionFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Codelab\Cashback\Logger\Logger;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManager;
use Codelab\Cashback\Model\Credit;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Quote\Api\CartRepositoryInterface;

class Data extends AbstractHelper
{
     /**
     * @var CustomerRegistry
     */
    protected $_customerRegistry;
     /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

     /**
     * @var CustomerSession
     */
    protected $_customerSession;
    protected $_quoteRepository;
    /**
     * @type ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $cashbackCollectionFactory,
        CashbackTransactionFactory $cashbackTransaction,
        CashbackFactory $cashbackFactory,
        ObjectManagerInterface $objectManager,
        CustomerSession $customerSession,
        TimezoneInterface $localeDate,
        CustomerRegistry $customerRegistry,
        CartRepositoryInterface $quoteRepository,
        Logger $logger
    ){
        $this->_customerRegistry = $customerRegistry;
        $this->_localeDate      = $localeDate;
        $this->_customerSession = $customerSession;
        $this->logger = $logger;
        $this->objectManager = $objectManager;
        $this->cashbackTransaction = $cashbackTransaction;
        $this->scopeConfig = $scopeConfig;
        $this->cashbackFactory = $cashbackFactory;
        $this->cashbackCollectionFactory = $cashbackCollectionFactory;
        $this->_quoteRepository = $quoteRepository;
    }


    
    /**
     * Configuration path to the cashback message
     */
    const XML_PATH_CASHBACK_MESSAGE = 'cashback/general/message';

    const XML_PATH_CASHBACK_ENABLED = 'cashback/general/enabled';

    const XML_PATH_WHATSAPP_NUMBER = 'cashback/general/whatsapp_no';

    const XML_PATH_CASHBACK_SUCCESS_MESSAGE = 'cashback/general/cashback_message';

    const XML_PATH_CASHBACK_CHECKOUT_MESSAGE = 'cashback/general/cashback_checkout_message';

    const XML_PATH_CASHBACK_EXPIRY_DAY = 'cashback/cashback_settings/expire_days';

    const XML_PATH_CASHBACK_CREDIT_DAY = 'cashback/cashback_settings/credit_days';

    const XML_PATH_VERIFICATION_TOKEN ='cashback/setting_whatsapp/token';

    const XML_PATH_PHONE_ID = 'cashback/setting_whatsapp/phonenoid';
    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public static function jsonEncode($valueToEncode)
    {
        try {
            $encodeValue = self::getJsonHelper()->jsonEncode($valueToEncode);
        } catch (Exception $e) {
            $encodeValue = '{}';
        }

        return $encodeValue;
    }
     /**
     * Convert and format price value for current application store
     *
     * @param      $amount
     * @param bool $format
     * @param bool $includeContainer
     * @param null $scope
     * @param null $currency
     *
     * @return float|string
     */
    public function convertPrice($amount, $format = true, $includeContainer = true, $scope = null, $currency = null)
    {
        return $format
            ? $this->getPriceCurrency()->convertAndFormat(
                $amount,
                $includeContainer,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $scope,
                $currency
            )
            : $this->getPriceCurrency()->convert($amount, $scope, $currency);
    }
      /**
     * @return PriceCurrencyInterface
     */
    public function getPriceCurrency()
    {
        if ($this->priceCurrency === null) {
            $this->priceCurrency = $this->objectManager->get(PriceCurrencyInterface::class);
        }

        return $this->priceCurrency;
    }
     /**
     * @return JsonHelper|mixed
     */
    public static function getJsonHelper()
    {
        return ObjectManager::getInstance()->get(JsonHelper::class);
    }
    
    /**
     * Get Cashback credit from quote
     * @param $quote
     *
     * @return float|mixed
     * @throws NoSuchEntityException
     */
    public function getCashbackCreditUsed($quote)
    {
        return (float) $quote->getCashbackCreditAmount();
    }
    
    /**
     * Calculate total amount for cashback discount
     *
     * @param Quote $quote
     * @param false $isCredit
     *
     * @return float|mixed
     * @throws NoSuchEntityException
     */
    public function getTotalAmountForDiscount(Quote $quote, $isCredit = false)
    {
        
        $discountTotal = $quote->getGrandTotal();
        // if (!$quote->isVirtual() && !$this->canUsedForTax($quote->getStoreId())) {
        //     $discountTotal -= $quote->getShippingAddress()->getTaxAmount();
        // }
        // if (!$quote->isVirtual() && !$this->canUsedForShipping($quote->getStoreId())) {
        //     $discountTotal -= $quote->getShippingAddress()->getShippingAmount();
        // }
       
        // if ($isCredit) {
        //     $discountTotal += $this->getCashbackCreditUsed($quote);
        // }

        return $discountTotal;
    }

    public function canUsedForTax($storeId)
    {
        return true;
    }
    public function canUsedForShipping($storeId)
    {
        return true;
    }
      /**
     * Get Customer Credit Balance
     *
     * @param null $customerId
     * @param bool $convert
     * @param bool $format
     *
     * @return float|int|string
     * @throws NoSuchEntityException
     */
    public function getCustomerBalance($customerId = null, $convert = true, $format = false)
    {
        $customer = $this->getCustomer($customerId);
        if (!$customer || !$customer->getId()) {
            return 0;
        }
        $credit = $this->objectManager->create(Credit::class);
        $credit->load($customer->getId(), 'customer_id');

        $balance = $credit->getBalance() ?: 0;
        $balance = !$convert ? $balance : $this->convertPrice($balance, false);

        return $format ? $this->formatPrice($balance) : $balance;
    }
     /**
     * @param      $amount
     * @param bool $includeContainer
     * @param null $scope
     * @param null $currency
     * @param int $precision
     *
     * @return float
     */
    public function formatPrice(
        $amount,
        $includeContainer = true,
        $scope = null,
        $currency = null,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->getPriceCurrency()->format($amount, $includeContainer, $precision, $scope, $currency);
    }
     /**
     * Get Customer object
     *
     * @param int $customerId
     *
     * @return Customer
     * @throws NoSuchEntityException
     */
    public function getCustomer($customerId = null)
    {
        $customer = null;
        if ($customerId instanceof Customer) {
            $customer = $customerId;
        } elseif ($customerId === null) {
            if ($this->_customerSession === null) {
                $this->_customerSession = $this->objectManager->get(CustomerSession::class);
            }
            if ($this->_customerSession->isLoggedIn()) {
                $customer = $this->_customerSession->getCustomer();
            }
        } elseif (is_numeric($customerId) && $customerId) {
            if ($this->_customerRegistry === null) {
                $this->_customerRegistry = $this->objectManager->get(CustomerRegistry::class);
            }
            $customer = $this->_customerRegistry->retrieve($customerId);
        }

        return $customer;
    }

    /**
     * Decodes the given $encodedValue string which is
     * encoded in the JSON format
     *
     * @param string $encodedValue
     *
     * @return mixed
     */
    public static function jsonDecode($encodedValue)
    {
        try {
            $decodeValue = self::getJsonHelper()->jsonDecode($encodedValue);
        } catch (Exception $e) {
            $decodeValue = [];
        }

        return $decodeValue;
    }

    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue('cashback/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get the cashback message from the system configuration
     *
     * @return string
     */
    public function getCashbackMessage($amount)
    {
        $message = $this->scopeConfig->getValue(
            self::XML_PATH_CASHBACK_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
        if ($message && $amount) {
            return str_replace('{{amount}}', $amount, $message);
        }
        return '';
    }

    public function getCashbackSuccessMessage($amount)
    {
        $message = $this->scopeConfig->getValue(
            self::XML_PATH_CASHBACK_SUCCESS_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
        if ($message && $amount) {
            return str_replace('{{amount}}', $amount, $message);
        }
        return '';
    }
    // 

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CASHBACK_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCashbackCheckoutMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CASHBACK_CHECKOUT_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get expiry days
     */
    public function getExpiryDays()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CASHBACK_EXPIRY_DAY,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get creditDays
     */
    public function getCreditDays()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CASHBACK_CREDIT_DAY,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get whatsapp number
     */
    public function getWhatsappNumber()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WHATSAPP_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }
     /**
     * Get Config vERIFCATION Token
     */
    public function getConfigVerificationToken()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_VERIFICATION_TOKEN, $storeScope);
    }
     /**
     * Retrieve formatting date
     *
     * @param null|string|DateTime|DateTimeInterface $date
     * @param int $format
     * @param bool $showTime
     * @param null|string $timezone
     *
     * @return string
     * @throws Exception
     */
    public function formatDate(
        $date,
        $format = IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof DateTimeInterface ? $date : new DateTime();

        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }

    /**
     * Get Phone Id
     */
    public function getConfigPhoneId()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_PHONE_ID, $storeScope);
    }
    /**
     * Update cashback table and cashback transaction history
     */
    public function updateCashbackTransaction($order, $amount, $customer_id)
    {
        $this->logger->info(__METHOD__);
        $this->logger->info($amount);
        $amount = -($amount);
        $this->logger->info($amount);
        $collectionDatas = $this->cashbackCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('status', 1);
        foreach ($collectionDatas as $collectionData) {
            while ($amount > 0) {
                $this->logger->info("enter while loop");
                $available_amount = $collectionData->getAmount() - $collectionData->getData('used_amount');
                $this->logger->info("availble amount ".$available_amount);
                $usedAmount = min($amount, $available_amount);
                $this->logger->info("aused amount ".$usedAmount);
                // Update the status if no amount is left
                if ($available_amount - $usedAmount <= 0) {
                    $collectionData->setData('status', '0');  
                }
                // Accumulate the used amount
                $totalUsedAmount = $collectionData->getData('used_amount') + $usedAmount;
                $collectionData->setData('used_amount', $totalUsedAmount)->save();
                // Log and create transaction history data
                $this->logger->info("Collection Data ID: " . $collectionData->getId());
                $data = [
                    'cashback_id' => $collectionData->getId(),
                    'order_id' => $order->getId(),
                    'amount_used' => $usedAmount,
                    'type' => "used"
                ];
                $this->addCashbackTransactionHistory($data);
                
                // Decrease the amount
                $amount -= $usedAmount;
                $this->logger->info("reamaining amount ".$amount);
        
                // Break if the amount is fully processed
                if ($amount >= 0) {
                    break;
                }
            }
        }
    }
    /**
     * Update transaction history
     */
    public function addCashbackTransactionHistory($data)
    {
        try {
            $transaction = $this->cashbackTransaction->create();
            $transaction->addData($data);
            $transaction->save();
            $this->logger->info('Cashback transaction history saved successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error saving cashback transaction history: ' . $e->getMessage());
            // You might want to rethrow the exception or handle it as per your application's needs.
        }
    }

     /**
     * Apply Credit
     *
     * @param float $amount
     * @param Quote $quote
     *
     * @return $this
     * @throws LocalizedException
     */
    public function applyCredit($amount, $quote)
    {
        $balance = $this->getCustomerBalance($quote->getCustomerId());
        $this->logger->info(__METHOD__);
        if ($amount < 0 || $amount > $balance) {
            throw new LocalizedException(__('Invalid credit amount.'));
        }
        $this->logger->info("apply credit ".$amount);
        $quote->setCashbackCreditAmount($amount);

        $this->collectTotals($quote);
        $this->logger->info("quoteData".json_encode($quote->getData()));
        return $this;
    }

     /**
     * Collect and save total
     *
     * @param null $quote
     *
     * @return $this
     */
    protected function collectTotals($quote = null)
    {
       
        if ($quote === null) {
            /** @var Quote $quote */
            try {
                $quote = $this->getCheckoutSession()->getQuote();
            } catch (NoSuchEntityException $e) {
                return $this;
            } catch (LocalizedException $e) {
                return $this;
            }
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);

        $this->_quoteRepository->save($quote->collectTotals());

        return $this;
    }

}
