<?php

namespace Codelab\Cashback\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Codelab\Cashback\Helper\Data as DataHelper;


class Credit extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'cashback_cashback_credit';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'codelab_cashback_credit';

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * Credit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $helper
     * @param TransactionFactory $transactionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $helper,
        TransactionFactory $transactionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helper             = $helper;
        $this->_transactionFactory = $transactionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Credit::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        parent::beforeSave();

        return $this;
    }
}
