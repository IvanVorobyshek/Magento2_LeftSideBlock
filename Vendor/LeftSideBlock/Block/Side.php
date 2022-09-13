<?php
namespace Vendor\LeftSideBlock\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

class Side extends Template
{
    protected CollectionFactory $_productCollectionFactory;
    protected Visibility $_productVisibility;
    private $currency;
    const PRODNUMTOSHOW = 3;

    public function __construct( 
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $productVisibility, 
        array $data = [])
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productVisibility = $productVisibility;
        parent::__construct($context, $data);
    }

    public function getProductCollection() {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*'); // выбираем все атрибуты товара
        $collection->addAttributeToFilter('type_id', ['eq' => 'simple']);// только simple товары
        $collection->addWebsiteFilter(); // фильтруем товары текущего сайта
        $collection->addStoreFilter(); // фильтруем товары текущего магазина
        $collection->setVisibility($this->_productVisibility->getVisibleInSiteIds()); // устанавливаем фильтр видимости товаров
        $collection->getSelect()->orderRand()->limit(self::PRODNUMTOSHOW);//перемешиваем и выбираем только 3 товара
        return $collection;
    }

    public function getCurrency(){
        if ($this->currency === null){
            $objectManager = ObjectManager::getInstance(); 
            $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
            $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode(); 
            $currencyCode = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode); 
            $this->currency = $currencyCode->getCurrencySymbol();
            return $this->currency;
        } else{
            return $this->currency;
        }        
    }

    

}
