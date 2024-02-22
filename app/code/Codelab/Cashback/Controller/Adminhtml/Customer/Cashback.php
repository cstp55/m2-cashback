<?php

namespace Codelab\Cashback\Controller\Adminhtml\Customer;

use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Framework\View\Result\Layout;

/**
 * Class Cashback
 * @package Codelab\Cashback\Controller\Adminhtml\Customer
 */
class Cashback extends Index
{
    /**
     * Execute
     *
     * @return Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();

        return $this->resultLayoutFactory->create();
    }
}
