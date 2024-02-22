<?php
namespace Codelab\Cashback\Block\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Codelab\Cashback\Model\CashbackManagement;
/**
 * Class Discount
 * @package Codelab\Cashback\Block\Sales\Order
 */
class Discount extends Template
{
    /**
     * Add gift card discount total
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cashbackAmount = $objectManager->create('Codelab\Cashback\Model\CashbackManagement')->getCashbackUsingOrder($parent->getOrder()->getId());
        $source = $parent->getSource();

        if ($source->getGiftCardAmount() && abs($source->getGiftCardAmount()) > 0.001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'gift_card',
                    'value'      => $source->getGiftCardAmount(),
                    'base_value' => $source->getBaseGiftCardAmount(),
                    'label'      => __('Gift Cards')
                ]
            ), 'tax');
        }

        if ($source->getGiftCreditAmount() && abs($source->getGiftCreditAmount()) > 0.001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'gift_credit',
                    'value'      => $source->getGiftCreditAmount(),
                    'base_value' => $source->getBaseGiftCreditAmount(),
                    'label'      => __('Wallet')
                ]
            ), 'tax');
        }
        if ($cashbackAmount) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'cashback',
                    'value'      => $cashbackAmount,
                    'base_value' => $cashbackAmount,
                    'label'      => __('Cashback'),
                    'order' => 1
                ]
            ), 'subtotal');
        }
        if ($source->getCashbackCreditAmount() && abs($source->getCashbackCreditAmount()) > 0.001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'cashback_credit',
                    'value'      => $source->getCashbackCreditAmount(),
                    'base_value' => $source->getBaseCashbackCreditAmount(),
                    'label'      => __('Cashback Used')
                ]
            ), 'tax');
        }

        return $this;
    }
}
