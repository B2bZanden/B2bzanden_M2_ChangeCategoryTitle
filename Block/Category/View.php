<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace B2bzanden\ChangeCategoryTitle\Block\Category;

/**
 * Class View
 * @package Magento\Catalog\Block\Category
 */
class View extends \Magento\Catalog\Block\Category\View
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    protected $request;

    protected $eavConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    )
    {
        $this->_categoryHelper = $categoryHelper;
        $this->_catalogLayer = $layerResolver->get();
        $this->_coreRegistry = $registry;
        $this->request = $request;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
    }

    /**
     * @return \Magento\Catalog\Block\Category\View
     */
    protected function _prepareLayout()
    {
        \Magento\Framework\View\Element\Template::_prepareLayout();

        $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);

        $category = $this->getCurrentCategory();
        if ($category) {
            $title = $category->getMetaTitle();
            if ($title) {
                $this->pageConfig->getTitle()->set($title);
            }
            $description = $category->getMetaDescription();
            if ($description) {
                $this->pageConfig->setDescription($description);
            }
            $keywords = $category->getMetaKeywords();
            if ($keywords) {
                $this->pageConfig->setKeywords($keywords);
            }
            if ($this->_categoryHelper->canUseCanonicalTag()) {
                $this->pageConfig->addRemotePageAsset(
                    $category->getUrl(),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $title = $this->getB2bCategoryTitle($category);
                $pageMainTitle->setPageTitle($title);
                //$pageMainTitle->setPageTitle($this->getCurrentCategory()->getName());
            }
        }

        return $this;
    }

    public function getB2bCategoryTitle($category)
    {
        $categoryName = $this->getCurrentCategory()->getName();

        if (!isset($category)) {
            return $categoryName;
        }

        $suffix = null;

        if (in_array($category->getLevel(), [2, 3])) {
            $suffix = ' onderdelen';
        }

        return $categoryName . $suffix;
    }

//    public function getB2bCategoryTitle($category)
//    {
//        try {
//            $brandOptionId = $this->getRequest()->getParam('vanderzanden_submerken');
//        } catch (\Exception $exception) {
//            // just catch
//        }
//        if (isset($brandOptionId) && $brandOptionId > 0) {
//            $attribute = $this->eavConfig->getAttribute('catalog_product', 'vanderzanden_submerken');
//            $brandOptionlabel = $attribute->getSource()->getOptionText($brandOptionId);
//        }
//        if (isset($category)) {
//            switch ($category->getLevel()) {
//                case 2:
//                case 3:
//                    $prefix = '';
//                    $name = $this->getCurrentCategory()->getName();
//                    $suffix = ' onderdelen';
//                    break;
//
//                case 4:
//                case 5:
//                    $prefix = $category->getParentCategory()->getName() . ' ';
//                    $name = lcfirst($this->getCurrentCategory()->getName());
//                    $suffix = '';
//                    break;
//
//                default:
//                    $prefix = '';
//                    $name = $this->getCurrentCategory()->getName();
//                    $suffix = '';
//
//            }
//            if (isset($brandOptionlabel)) {
//                if ($brandOptionlabel === $this->getCurrentCategory()->getName()) {
//                    $brandOptionlabel = '';
//                }
//
//                return $brandOptionlabel . ' ' . $prefix . $name . $suffix;
//            } else {
//                return $prefix . $name . $suffix;
//            }
//        }
//        return $this->getCurrentCategory()->getName();
//    }
}
