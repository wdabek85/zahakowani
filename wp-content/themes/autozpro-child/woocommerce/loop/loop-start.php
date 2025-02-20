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

// Sprawdzenie, czy użytkownik jest na stronie głównej
if (is_front_page() || is_home() || is_product()) {
    $layout = 'grid'; // Na stronie głównej zawsze "grid"
} else {
    // Sprawdzenie, czy użytkownik wybrał widok w GET lub jest zapisane w ciasteczkach
    if (isset($_GET['layout'])) {
        $layout = $_GET['layout'];
    } else {
        $layout = 'list'; // Domyślnie lista (chyba że użytkownik wybierze inaczej)
    }
}

// Ustawienie klasy dla listy lub gridu
if ($layout == 'list') {
    wc_set_loop_prop('product-class', 'autozpro-products products-list');
} else {
    wc_set_loop_prop('product-class', 'autozpro-products products');
}

?>
<div class="products-wap">
    <ul class="<?php echo esc_attr(wc_get_loop_prop('product-class', 'products')); ?> columns-<?php echo esc_attr(wc_get_loop_prop('columns', 4)); ?>">