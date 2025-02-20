<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     3.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
$data_settings = array();
$layout        = isset($_GET['layout']) ? $_GET['layout'] : apply_filters('autozpro_shop_layout', 'grid');
if ($layout == 'list') {
    wc_set_loop_prop('product-class', 'autozpro-products products-list');
} else {
    wc_set_loop_prop('product-class', 'autozpro-products products');
}
?>
<div class="products-wap">

    <ul class="<?php echo esc_attr(wc_get_loop_prop('product-class', 'products')); ?> columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?>">

