<?php

namespace Codelab\Cashback\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class Cashback
 * @package Codelab\Cashback\Block\Adminhtml\Customer\Edit\Tab
 */
class Cashback extends Template implements TabInterface
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * Get Customer Id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Get Tab Label
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Cashback');
    }

    /**
     * Get Tab Title
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Cashback');
    }

    /**
     * Can show Tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return $this->getCustomerId() && $this->_authorization->isAllowed('Mageplaza_Cashback::customer');
    }

    /**
     * Is Hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return !$this->getCustomerId() && $this->_authorization->isAllowed('Mageplaza_Cashback::customer');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('cashback/customer/cashback', ['_current' => true]);
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}
