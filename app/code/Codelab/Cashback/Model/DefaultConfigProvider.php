<?php
namespace Codelab\Cashback\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Codelab\Cashback\Helper\Data as Helper;
use Zend_Serializer_Exception;

class DefaultConfigProvider implements ConfigProviderInterface
{

    /**
     * DefaultConfigProvider constructor.
     *
     * @param MpDtHelper $mpDtHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Helper $helper,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        // if (!$this->helper->isEnabled()) {
        //     return [];
        // }

        return ['cashbackConfig' => $this->getCashbackConfig()];
    }

    public function getCashbackConfig()
    {
        return ['checkout_message'=>$this->helper->getCashbackCheckoutMessage()];
    }
}
