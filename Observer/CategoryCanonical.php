<?php
/**
 * Product : B2bzanden Change Category Title
 *
 * @copyright Copyright © 2020 B2bzanden. All rights reserved.
 * @author    Isolde van Oosterhout & Hans Kuijpers
 */
namespace B2bzanden\ChangeCategoryTitle\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CategoryCanonical implements ObserverInterface
{
    protected $pageConfig;
    protected $request;
    protected $categoryHelper;
    protected $eavConfig;

    public function __construct(\Magento\Framework\View\Page\Config $pageConfig,
                                \Magento\Framework\App\RequestInterface $request,
                                \Magento\Catalog\Helper\Category $categoryHelper,
                                \Magento\Eav\Model\Config $eavConfig)
    {
        $this->pageConfig = $pageConfig;
        $this->request = $request;
        $this->categoryHelper =$categoryHelper;
        $this->eavConfig = $eavConfig;
    }

    public function execute(Observer $observer)
    {
        if ('catalog_category_view' != $observer->getEvent()->getFullActionName()) {
            return $this;
        }
        $brandUrlKey = $this->getB2bBrandUrlKey();
        if(isset($brandUrlKey) && strlen($brandUrlKey) > 0) {
            $productListBlock = $observer->getEvent()->getLayout()->getBlock('category.products.list');
            $category = $productListBlock->getLayer()->getCurrentCategory();
            $this->pageConfig->getAssetCollection()->remove($category->getUrl());
            $catUrl = $category->getUrl();
            if(substr($catUrl, -1) == '/') {
                $catUrl = $catUrl.$brandUrlKey;
            } else {
                $catUrl = $catUrl.'/'.$brandUrlKey;
            }
            $this->pageConfig->addRemotePageAsset(
                $catUrl,
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

    }

    public function getB2bBrandUrlKey()
    {
        $brandLabel = $this->getB2bBrandLabel();
        if(isset($brandLabel) && strlen($brandLabel) > 0) {
            $unwantedChars = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
                'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
                'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
                'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'ü' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', '&' => '_', ' / ' => '_', '/' => '_');
            $stringCleaned = strtr($brandLabel, $unwantedChars);
            // remove any spaces
            $stringCleaned = trim($stringCleaned);
            $stringCleaned = str_replace(' ', '_', $stringCleaned);
            $stringCleaned = strtolower($stringCleaned);
            return $stringCleaned;
        }
        return '';
    }

    public function getB2bBrandLabel()
    {
        try {
            $brandOptionId = $this->request->getParam('vanderzanden_submerken');
        } catch (\Exception $exception) {
            // just catch
        }
        if(isset($brandOptionId) && $brandOptionId > 0) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', 'vanderzanden_submerken');
            $brandOptionlabel =  $attribute->getSource()->getOptionText($brandOptionId);
            if(isset($brandOptionlabel) && strlen($brandOptionlabel) > 0) {
                return $brandOptionlabel;
            }
        }
        return '';
    }
}
