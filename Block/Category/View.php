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

    protected function _prepareLayout()
    {
        \Magento\Framework\View\Element\Template::_prepareLayout();

        $category = $this->getCurrentCategory();

        if ($category) {
            switch ($category->getLevel()) {
                case 2:
                case 3:
                    $prefix = '';
                    $name = $this->getCurrentCategory()->getName();
                    $suffix = ' onderdelen';
                    break;

                case 4:
                case 5:
                    $prefix = $category->getParentCategory()->getName() . ' ';
                    $name = lcfirst($this->getCurrentCategory()->getName());
                    $suffix = '';
                    break;

                default:
                    $prefix = '';
                    $name = $this->getCurrentCategory()->getName();
                    $suffix = '';

            }

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            $pageMainTitle->setPageTitle($prefix . $name . $suffix);
        }

        return $this;
    }
}
