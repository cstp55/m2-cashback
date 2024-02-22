<?php
namespace Codelab\Cashback\Block\Product\ProductList;

class Upsell extends \Magento\Catalog\Block\Product\ProductList\Upsell
{
    /**
     * Prepare data for the upsell products list.
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Upsell
     */
    protected function _prepareData()
    {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product \Magento\Catalog\Model\Product */
        $this->_itemCollection = $product->getUpSellProductCollection()->addAttributeToSelect(
            $this->_catalogConfig->getProductAttributes()
        )->setPositionOrder()->addStoreFilter();
        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        // Add cashback attribute to the collection
        $this->_itemCollection->addAttributeToSelect('cashback');

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }
}
