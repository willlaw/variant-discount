<?php
/**
 * Variant Discount plugin for Craft CMS
 *
 * Discount and Sales are limited to specific variant SKUs by searching the discount description field for the keyword 'ONLY' and the variant's SKU. e.g. Discount ONLY applies to SKU78439 and SKU94300
 *
 * @author    Luke Holder
 * @copyright Copyright (c) 2017 Luke Holder
 * @link      http://craftcms.stackexchange.com/users/91/luke-holder
 * @package   VariantDiscount
 * @since     1.0.1
 */

namespace Craft;

class VariantDiscountPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();
        
        /* Function: Applies Discounts to specific line items */
        /*  This would only be called when the discount has matched with everything else about the product. */
        craft()->on('commerce_discounts.onBeforeMatchLineItem',
        function($event){
            $lineItem = $event->params['lineItem'];
            $discount = $event->params['discount'];

            if (stripos($discount->description, 'only') === false) {
               return; /* do nothing, and let the discount match as it normally would, because the discount does not have 'only' in the description. */
            } else {
                if (stripos($discount->description, $lineItem->sku) === false) {
                    $event->performAction = false; /* since this SKU is not in the description string, then don't apply this discount */
                }
            }
        });


      /* Function: Applies Sales logic to specific variants (Currently broken) */
        craft()->on('commerce_sales.onBeforeMatchProductAndSale',
        function($event){
            $sale = $event->params['sale'];
            $product = $event->params['product'];

            $criteria = craft()->elements->getCriteria('Commerce_Variant');
            $criteria->productId = $product->id;
            $allVariants = $criteria->find();

            $allSkus = '';
            foreach ($allVariants as $variant)
            {
                $allSkus .= ' ' . $variant->sku;
            }

            if (stripos($sale->description, 'only') === false) {
               return; /* do nothing, and let the sale match as it normally would, because the sale does not have 'only' in the description. */
            } else {
                if (stripos($sale->description, $allSkus) === false) {
                    $event->performAction = false; /* since this SKU is not in the description string, then don't apply this discount */
                }
            }
        });
        
    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Variant Discount');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t("Discount is limited to specific variant SKU's by searching the discount description field for the keyword ONLY and the variants SKU. e.g. Discount ONLY applies to SKU78439 and SKU94300");
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Luke Holder';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'http://craftcms.stackexchange.com/users/91/luke-holder';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }
}
