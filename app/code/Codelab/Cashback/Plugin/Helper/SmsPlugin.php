<?php
namespace Codelab\Cashback\Plugin\Helper;

class SmsPlugin
{
    protected $whatsappApi; // Service for WhatsApp API
    protected $config; // Configuration service to get WhatsApp number

    public function __construct(
        \Codelab\Cashback\Helper\Data $helper,
        \Codelab\Cashback\Logger\Logger $logger
    ) {
        $this->helper = $helper;
        $this->logger =$logger;
    }

    public function afterSendSms(
        \Mageplaza\GiftCard\Helper\Sms $subject,
        $result,
        $giftCard,
        $type
    ) {
        $message = $subject->generateMessageContent($giftCard, $type);
        $this->sendMessageByWeb($message);
        return $result;
    }
    public function sendMessageByWeb($msgBody = null)
    {

        try {
            $newHeader = [];
            foreach ($this->getHttpHeader() as $key => $head) {
                $newHeader[] = $key.": ".$head;
            }
            $url = 'https://graph.facebook.com/v16.0/'.
            $this->helper->getConfigPhoneId().'/messages?access_token='.$this->helper->getConfigToken();
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $msgBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $newHeader);
            $response = curl_exec($ch);
            if (curl_error($ch)) {
                $this->logger->info(curl_error($ch));
            }
            $this->logger->info($response);
            curl_close($ch);
            $responseData = $this->json->unserialize($response);
        } catch (LocalizedException $th) {
            throw new LocalizedException(__('Unauthorized request'));
        } catch (\Exception $th) {
            throw $th;
        }
    }
     /**
     * Get Header
     */
    public function getHttpHeader()
    {
        $headerData = [
            'Content-Type' => 'application/json'
        ];
        // if ($this->getConfigToken()) {
        //     $headerData['Authorization'] = 'Bearer ' . $this->getConfigToken();
        // }
        return $headerData;
    }    
}
