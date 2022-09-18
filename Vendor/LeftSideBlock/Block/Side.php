<?php
namespace Vendor\LeftSideBlock\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;
use \Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Pricing\Helper\Data as DataHelper;

class Side extends Template
{
    protected CollectionFactory $_productCollectionFactory;
    protected Visibility $_productVisibility;
    private $currency;
    private $imageHelper;
    private $dataHelper;
    public const PRODNUMTOSHOW = 3;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $productVisibility,
        ImageHelper $imageHelper,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productVisibility = $productVisibility;
        $this->imageHelper = $imageHelper;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        // выбираем требуемые атрибуты товара
        $collection->addAttributeToSelect(['name', 'price', 'url_key', 'small_image']);
        $collection->addAttributeToFilter('type_id', ['eq' => 'simple']);// только simple товары
        $collection->addWebsiteFilter();// фильтруем товары текущего сайта
        $collection->addStoreFilter();// фильтруем товары текущего магазина
        // устанавливаем фильтр видимости товаров
        $collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
        $collection->getSelect()->orderRand()->limit(self::PRODNUMTOSHOW);//перемешиваем и выбираем только 3 товара
        return $collection;
    }

    public function getImageUrl(Product $product):string//get the Image URL from ImageHelper
    {
        return $this->imageHelper->init($product, 'gift_messages_checkout_small_image')->getUrl();
    }

    public function getPriceWithCurrency(string $price):string//get the formatted price with currency
    {
        return $this->dataHelper->currency(number_format($price, 2), true, false);
    }
}
