<?php

namespace Codelab\Cashback\Model\Transaction;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;
use Codelab\Cashback\Helper\Data;

/**
 * Gift Card action functionality model
 */
class Action implements OptionSourceInterface
{
    /**
     * Gift Card Status
     */
    const ACTION_ADMIN  = 1;
    const ACTION_REDEEM = 2;
    const ACTION_SPEND  = 3;
    const ACTION_REFUND = 4;
    const ACTION_REVERT = 5;
    const ACTION_CREDIT = 6;
    const ACTION_EXPIRED = 7;
    const ACTION_CANCELLED =8;
    const ACTION_PENDING = 9;
    const ACTION_ADMIN_DEBIT = 10;
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::ACTION_ADMIN  => __('Credit'),
            self::ACTION_SPEND  => __('Debit'),
            self::ACTION_REDEEM => __('Credit'),
            self::ACTION_REFUND => __('Credit'),
            self::ACTION_REVERT => __('Credit'),
            self::ACTION_CREDIT => __('Credit'),
            self::ACTION_EXPIRED => __('Debit'),
            self::ACTION_CANCELLED => __('Debit'),
            self::ACTION_PENDING => __('Pending'),
            self::ACTION_ADMIN_DEBIT =>__('Debit')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Get Action detail
     *
     * @param $action
     * @param $extraContent
     *
     * @return Phrase|string
     */
    public static function getActionDetail($action, $extraContent)
    {
        $message = '';
        if (!$extraContent) {
            return $message;
        }
        if (!is_array($extraContent)) {
            $extraContent = Data::jsonDecode($extraContent);
        }

        switch ($action) {
            case self::ACTION_ADMIN:
                $message = __('Changed By Admin');
                break;
            case self::ACTION_ADMIN_DEBIT:
                $message = __('Changed By Admin');
                break; 
            case self::ACTION_REDEEM:
                if(isset($extraContent['code'])) {
                    $message =  __(
                        'Redeemed From: %1',
                        $extraContent['code']
                    );
                    if(isset($extraContent['expired_at'])){
                        $message .=    __(
                        ', It will expire on %1',
                        $extraContent['expired_at']
                    );
                }
                } else {
                    $message =__('Redeemed');
                } 
                break;
            case self::ACTION_SPEND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Spend for order #%1',
                    $extraContent['order_increment_id']
                ) : __('Spent');
                break;
            case self::ACTION_REFUND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Refund on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Refund');
                break;
            case self::ACTION_REVERT:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Revert on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Revert');
                break;
            case self::ACTION_CREDIT:
                if(isset($extraContent['order_increment_id'])) {
                    $message =  __(
                        'Cashback on order #%1',
                        $extraContent['order_increment_id']
                    );
                        if(isset($extraContent['expired_at'])){
                            $message .=    __(
                            ', It will expire on %1',
                            $extraContent['expired_at']
                        );
                    }
                } else {
                    $message =__('Cashback');
                } 
                break;
            case self::ACTION_EXPIRED:
                $message = isset($extraContent['date']) ? __(
                    'Cashback Expired at  #%1',
                    $extraContent['date']
                ) : __('Cashback');
                break; 
            case self::ACTION_CANCELLED:
                    $message = isset($extraContent['date']) ? __(
                        'Order Cancelled at  #%1',
                        $extraContent['order_increment_id']
                    ) : __('Cashback');
                    break;  
            case self::ACTION_PENDING:
                $message = isset($extraContent['date']) ? 'KWD '.$extraContent['amount'].__(
                    'will be credited on %1',
                    $extraContent['date']
                ) : __('cashback will be credited');
                break;               
            default:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Cashback on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Cashback');
                break;
        }

        return $message;
    }

    /**
     * @param $action
     * @param $extraContent
     *
     * @return Phrase|string
     */
    public static function getActionLabel($action, $extraContent)
    {
        $message = '';
        if (!$extraContent) {
            return $message;
        }
        if (!is_array($extraContent)) {
            $extraContent = Data::jsonDecode($extraContent);
        }

        switch ($action) {
            case self::ACTION_ADMIN:
                $message = __('Changed By Admin');
                break;
            case self::ACTION_ADMIN_DEBIT:
                $message = __('Changed By Admin');
                break; 
            case self::ACTION_REDEEM:
                if(isset($extraContent['code'])) {
                    $message =  __(
                        'Redeemed From: %1',
                        $extraContent['code']
                    );
                    if(isset($extraContent['expired_at'])){
                        $message .=    __(
                        ', It will expire on %1',
                        $extraContent['expired_at']
                    );
                }
                } else {
                    $message =__('Redeemed');
                } 
                break;
            case self::ACTION_SPEND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Spend for order #%1',
                    $extraContent['order_increment_id']
                ) : __('Spent');
                break;
            case self::ACTION_REFUND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Refund on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Refund');
                break;
            case self::ACTION_REVERT:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Revert on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Revert');
                break;
            case self::ACTION_CREDIT:
                if(isset($extraContent['order_increment_id'])) {
                    $message =  __(
                        'Cashback on order #%1',
                        $extraContent['order_increment_id']
                    );
                        if(isset($extraContent['expired_at'])){
                            $message .=    __(
                            ', It will expire on %1',
                            $extraContent['expired_at']
                        );
                    }
                } else {
                    $message =__('Cashback');
                } 
                break;
            case self::ACTION_EXPIRED:
                $message = isset($extraContent['date']) ? __(
                    'Cashback Expired at  #%1',
                    $extraContent['date']
                ) : __('Cashback');
                break;
            case self::ACTION_CANCELLED:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Cancelled for order #%1',
                    $extraContent['order_increment_id']
                ) : __('Refund');
                break;
            case self::ACTION_PENDING:
                $message = isset($extraContent['date']) ? 'KWD '.$extraContent['amount'].__(
                    ' will be credited on %1',
                    $extraContent['date']
                ) : __('cashback will be credited');
                break;         
            default:
               $message= __('Action');
                break;
        }
        return $message;
    }
}
