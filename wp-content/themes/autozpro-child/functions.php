<?php
/**
 * Theme functions and definitions.
 */
		 
 
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

add_action('woocommerce_single_product_summary', 'custom_short_description_with_link', 20);

function custom_short_description_with_link() {
    global $post;

    
    $short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);

    
    if (!$short_description) {
        return;
    }

    echo '<div class="woocommerce-product-details__short-description">';
    echo wpautop($short_description); 
    echo '<a class="description-link" href="#tab-title-description">Zobacz Pełen Opis ↓</a> <a class="description-link" style="margin-left:10px" href="https://twojastrona.pl/wp-content/uploads/2025/01/certyfikat.pdf" download>Pobierz certyfikat</a>'; 
    echo '</div>';
}



add_action('woocommerce_single_product_summary', 'display_dynamic_variants_with_highlight', 25);

function display_dynamic_variants_with_highlight() {
    $current_product_id = get_the_ID();

    
    if (have_rows('dynamiczne_warianty')) { 
        echo '<div class="variant-links">';
        echo '<h3>Warianty produktu:</h3>';

        while (have_rows('dynamiczne_warianty')) {
            the_row();

            $variant_title = get_sub_field('tytul_wariantu'); 
            $variant_url = get_sub_field('link_wariantu');   
            $variant_id = url_to_postid($variant_url);       

            $highlight_class = ($current_product_id == $variant_id) ? 'highlight' : '';

            if ($variant_title && $variant_url) {
                echo '<a href="' . esc_url($variant_url) . '" class="variant-link ' . esc_attr($highlight_class) . '">' . esc_html($variant_title) . '</a>';
            }
        }

        echo '</div>';
    }
}

add_filter('woocommerce_product_tabs', 'add_specification_tab_with_acf');

function add_specification_tab_with_acf($tabs) {
    $tabs['specification_tab'] = array(
        'title'    => __('Specyfikacja', 'your-text-domain'), 
        'priority' => 15, 
        'callback' => 'display_specification_tab_content' 
    );

    return $tabs;
}

function display_specification_tab_content() {
    if (have_rows('specifications')) { 
        while (have_rows('specifications')) {
            the_row();
            $value = get_sub_field('specification_value'); 
            echo $value;
        }
    } else {
        echo '<p>Brak specyfikacji dla tego produktu.</p>';
    }
}


function enqueue_custom_scripts() {
    wp_enqueue_script(
        'scroll-tabs',
        get_stylesheet_directory_uri() . '/js/scroll-tabs.js',
        array(),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_scripts' );

function enqueue_toggle_description_script() {
    if (is_product()) { 
        wp_enqueue_script(
            'toggle-description', 
            get_stylesheet_directory_uri() . '/js/toggle-description.js', 
            array(), 
            '1.0', 
            true 
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_toggle_description_script');

function enqueue_toggle_delivery_card() {
    if (is_product()) { 
        wp_enqueue_script(
            'delivery-card', 
            get_stylesheet_directory_uri() . '/js/delivery-card.js', 
            array(), 
            '1.0', 
            true 
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_toggle_delivery_card');

function enqueue_tabs_open() {
    wp_enqueue_script(
        'tabs-open',
        get_stylesheet_directory_uri() . '/js/tabs-open.js',
        array(),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_tabs_open' );

function enqueue_scroll_fixed_tabs_script() {
    if (is_product()) { // Sprawdzamy, czy to strona produktu WooCommerce
        wp_enqueue_script(
            'scroll-fixed-tabs', // Unikalny identyfikator skryptu
            get_stylesheet_directory_uri() . '/js/scroll-fixed-tabs.js', // Ścieżka do pliku
            array('jquery'), // Zależności (możesz dodać jQuery, jeśli go używasz)
            filemtime(get_stylesheet_directory() . '/js/scroll-fixed-tabs.js'), // Automatyczna wersja oparta na czasie modyfikacji
            true // Ładowanie w stopce (footer)
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_scroll_fixed_tabs_script');



// Dodanie trzech osobnych pól na etykiety w edycji produktu
function add_custom_product_labels_fields() {
    woocommerce_wp_text_input( array(
        'id'          => '_custom_product_label_1',
        'label'       => 'Etykieta 1 (np. ZESTAW)',
        'placeholder' => 'Wpisz etykietę',
        'desc_tip'    => 'true',
        'description' => 'Pierwsza etykieta produktu.'
    ));
    
    woocommerce_wp_text_input( array(
        'id'          => '_custom_product_label_2',
        'label'       => 'Etykieta 2 (np. NOWOŚĆ)',
        'placeholder' => 'Wpisz etykietę',
        'desc_tip'    => 'true',
        'description' => 'Druga etykieta produktu.'
    ));

    woocommerce_wp_text_input( array(
        'id'          => '_custom_product_label_3',
        'label'       => 'Etykieta 3 (np. PROMOCJA)',
        'placeholder' => 'Wpisz etykietę',
        'desc_tip'    => 'true',
        'description' => 'Trzecia etykieta produktu.'
    ));
}
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_product_labels_fields' );

// Zapisanie wartości etykiet
function save_custom_product_labels_fields( $post_id ) {
    $labels = ['_custom_product_label_1', '_custom_product_label_2', '_custom_product_label_3'];

    foreach ($labels as $label) {
        $label_value = isset($_POST[$label]) ? sanitize_text_field($_POST[$label]) : '';
        update_post_meta($post_id, $label, $label_value);
    }
}
add_action( 'woocommerce_process_product_meta', 'save_custom_product_labels_fields' );

function modify_product_title_with_multiple_labels() {
    global $product;
    if (is_front_page() || is_home() || is_product()) {
        return;
    }
    // Pobranie wartości etykiet
    $label1 = get_post_meta($product->get_id(), '_custom_product_label_1', true);
    $label2 = get_post_meta($product->get_id(), '_custom_product_label_2', true);
    $label3 = get_post_meta($product->get_id(), '_custom_product_label_3', true);
    
    // Pobranie tytułu produktu
    $product_title = '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';

    // Tworzenie nowego tytułu z etykietami wewnątrz <h3>
    echo '<h3 class="woocommerce-loop-product__title">';

    // Wyświetlenie każdej etykiety, jeśli istnieje
    if (!empty($label1)) {
        echo '<span class="custom-label label-1">' . esc_html($label1) . '</span> ';
    }
    if (!empty($label2)) {
        echo '<span class="custom-label label-2">' . esc_html($label2) . '</span> ';
    }
    if (!empty($label3)) {
        echo '<span class="custom-label label-3">' . esc_html($label3) . '</span> ';
    }

    echo $product_title;
    
    echo '</h3>';
}

// Usunięcie domyślnego tytułu WooCommerce
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
remove_action('autozpro_woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
remove_all_actions('woocommerce_shop_loop_item_title');
remove_all_actions('autozpro_woocommerce_shop_loop_item_title');

// Dodanie nowej funkcji wyświetlającej tytuł z etykietami
add_action('autozpro_woocommerce_shop_loop_item_title', 'modify_product_title_with_multiple_labels', 10);

// Wyświetlanie etykiet w widoku kategorii (przed tytułem produktu)
function display_custom_product_labels() {
    global $product;
    if (is_front_page() || is_home() || is_product()) {
        return;
    }
    // Pobranie wartości trzech etykiet
    $label1 = get_post_meta($product->get_id(), '_custom_product_label_1', true);
    $label2 = get_post_meta($product->get_id(), '_custom_product_label_2', true);
    $label3 = get_post_meta($product->get_id(), '_custom_product_label_3', true);
    
    // Jeśli istnieją jakiekolwiek etykiety, wyświetlamy je
    if (!empty($label1) || !empty($label2) || !empty($label3)) {
        echo '<div class="custom-labels-container">'; // Opakowanie dla wszystkich etykiet
        
        if (!empty($label1)) {
            echo '<span class="custom-label label-1">' . esc_html($label1) . '</span> ';
        }
        if (!empty($label2)) {
            echo '<span class="custom-label label-2">' . esc_html($label2) . '</span> ';
        }
        if (!empty($label3)) {
            echo '<span class="custom-label label-3">' . esc_html($label3) . '</span> ';
        }

        echo '</div>';
    }
}

// Dodanie etykiet w widoku kategorii przed tytułem produktu
add_action('woocommerce_before_shop_loop_item_title', 'display_custom_product_labels', 10);


function change_template_part($template, $slug, $name) {
    // Pobierz domyślny układ: na stronie głównej wymuszamy 'grid', a na reszcie 'list'
    if (is_front_page() || is_home() || is_product()) {
        $layout = isset($_GET['layout']) ? $_GET['layout'] : apply_filters('autozpro_shop_layout', 'grid');
    } else {
        $layout = isset($_GET['layout']) ? $_GET['layout'] : 'list'; // Na reszcie domyślnie "list"
    }

    // Jeśli WooCommerce próbuje załadować content-product.php, zamień na content-product-list.php tylko poza stroną główną
    if ($slug == 'content' && $name == 'product' && $layout == 'list') {
        $template = get_stylesheet_directory() . '/woocommerce/content-product-list.php';
    }

    return $template;
}
add_filter('wc_get_template_part', 'change_template_part', 10, 3);


// Dodajemy checkbox do panelu edycji produktu
function add_made_in_poland_checkbox() {
    global $post;
    $value = get_post_meta($post->ID, '_made_in_poland', true);
    ?>
    <div class="options_group">
        <p class="form-field">
            <label for="made_in_poland"><?php _e('Wyprodukowano w Polsce', 'autozpro'); ?></label>
            <input type="checkbox" id="made_in_poland" name="made_in_poland" value="yes" <?php checked($value, 'yes'); ?>>
        </p>
    </div>
    <?php
}
add_action('woocommerce_product_options_general_product_data', 'add_made_in_poland_checkbox');

// Zapisujemy wartość checkboxa
function save_made_in_poland_checkbox($post_id) {
    $value = isset($_POST['made_in_poland']) ? 'yes' : 'no';
    update_post_meta($post_id, '_made_in_poland', $value);
}
add_action('woocommerce_process_product_meta', 'save_made_in_poland_checkbox');

add_action('after_setup_theme', function() {
    remove_theme_support('wc-product-gallery-zoom');
});

