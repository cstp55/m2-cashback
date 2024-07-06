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
namespace Aprilo\Cashback\Block\Adminhtml\Order\Items;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Items\Column\Name;

/**
 * Class Cashback
 * @package Aprilo\Cashback\Block\Adminhtml\Order\Items
 */
class Cashback extends Name
{
    
    /**
     * @return array
     * @throws LocalizedException
     */
    public function getOrderOptions()
    {
        $item = $this->getItem();

       
        $totalQty = $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
        $cashbackPercentage = $item->getCashbackPercentage();
        $cashbackAmount = $item->getCashbackAmount();
        if ($totalQty && abs($cashbackPercentage) && $cashbackAmount) {
            
            $cashbackOptions[] = [
                'label'       => __('Cashback'),
                'value'       => $cashbackAmount,
                'custom_view' => true,
            ];
        }

        return $cashbackOptions;
    }
}
