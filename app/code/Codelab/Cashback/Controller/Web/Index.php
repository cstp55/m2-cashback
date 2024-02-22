<?php

declare(strict_types=1);

namespace Codelab\Cashback\Controller\Web;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\RemoteServiceUnavailableException;

class Index extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Catalog\Block\Product\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\OrderByWhatsappBot\Model\WhatsappBotFactory
     */
    protected $botFactory;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    /** @var \Webkul\OrderByWhatsappBot\Helper\Data */
    protected $helper;

    /** @var \Magento\Framework\Serialize\Serializer\Json */
    protected $json;

    /** @var \Magento\Framework\Webapi\Rest\Request */
    protected $request;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $jsonResultFactory;

   /**
    * Recipient email config path
    */

    public const XML_PATH_VERIFY_TOKEN = 'cashback/setting_whatsapp/Verify_token';
    
    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Codelab\Cashback\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Codelab\Cashback\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->json = $json;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $inputJSON = $this->request->getContent();
        $input = json_decode($inputJSON, true);
        try {
            if (!$input) {
                //Verify the verify_token
                $verify_token = $this->getVerifyToken(); //this is verify token getting from configuration
                $data = $this->getRequest()->getParams();
                if (isset($data['hub_mode']) && isset($data['hub_verify_token'])) {
                    if ($data['hub_mode'] === "subscribe" && $data['hub_verify_token'] === $verify_token) {
                        $this->getResponse()
                            ->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_200)
                            ->setContent($data['hub_challenge']);
                        return ;
                    } else {
                        $result = $this->jsonResultFactory->create();
                        /** Send 403 status for this custom REST API */
                        $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_FORBIDDEN);
                        $result->setData(['error_message' => __('Verfication failed')]);
                        return $result;
                    }
                } else {
                    $this->messageManager->addError(__("Unauthorised access"));
                    return $this->resultRedirectFactory->create()
                    ->setPath('/');
                }
            }
        } catch (RemoteServiceUnavailableException $e) {
            $this->logger->critical($e);
            $this->getResponse()->setStatusHeader(403, '1.1', 'Service Unavailable')->sendResponse();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->getResponse()->setHttpResponseCode(500);
        }
    }

    /**
     * Get verify token value from configuraition
     */
    public function getVerifyToken()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $token =  $this->scopeConfig->getValue(self::XML_PATH_VERIFY_TOKEN, $storeScope);
        if ($token) {
            return $token;
        } else {
            return "webkul";
        }
    }
    
    /**
     * Update message Status
     *
     * @param [type] $messageId
     * @return void
     */
    public function sendReadStatus($messageId)
    {
        $this->helper->sendMessageByWeb($messageId);
    }
}