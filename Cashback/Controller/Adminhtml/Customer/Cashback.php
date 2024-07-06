<?php
/**
 * Aprilo Software.
 *
 * @category Aprilo Software Private Limited
 * @package aprilo_Cashback
 * @author Aprilo
 * @copyright Copyright (c) Aprilo Software Private Limited (https://Aprilo.com)
 * @license https://store.Aprilo.com/license.html
 */
namespace Aprilo\Cashback\Controller\Adminhtml\Customer;

use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Framework\View\Result\Layout;

/**
 * Class Cashback
 * @package Aprilo\Cashback\Controller\Adminhtml\Customer
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
