<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\Item;

class Product extends Item
{
    protected $product_id;
    protected $product_category;
    protected $ean;
    protected $product_name;
    protected $product_description;
    protected $product_link;
    protected $product_image;
    protected $additional_product_images;
    protected $base_price;
    protected $promo_price;
    protected $quantity;
    protected $product_attributes;
    protected $variants;
	protected $lowest_price;
}
