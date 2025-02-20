<?php


/**
 * Main class of plugin for admin
 */
class Autozpro_Woocommerce_AutoParts {

    /**
     * Class constructor.
     */

    protected static $fields = array();

    /**
     * Form fields.
     *
     * @var array
     */
    protected static $admin_fields = array();
    /**
     * Form instance.
     *
     * @var array
     */
    protected static $instance;

    /**
     * Get the singleton instance of this class
     *
     * @return Autozpro_Woocommerce_AutoParts
     */
    public static function get_instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        add_option('autozpro_sputnik_vehicle_fields', '', '', 'yes');
        add_filter('woocommerce_get_sections_products', array($this, 'auto_parts_add_settings_tab'));
        add_filter('woocommerce_get_settings_products', array($this, 'auto_parts_get_settings'), 10, 2);
        add_filter('autozpro_sputnik_ajax_url', array($this, 'ajax_url'));

        add_action('wp_ajax_autozpro_sputnik_vehicle_select_load_data', array($this, 'ajax_vehicle_select_load_data'));
        add_action('wp_ajax_nopriv_autozpro_sputnik_vehicle_select_load_data', array($this, 'ajax_vehicle_select_load_data'));


        self::$fields = apply_filters('autozpro_admin_fields_filter_vehicle', array(

            array(
                'type'        => 'year',
                'slug'        => 'produced',
                "label"       => "Year",
                'placeholder' => esc_html__('Choose Year', 'autozpro'),
            ),

            array(
                'type'        => 'text',
                'slug'        => 'make',
                "label"       => "Make",
                'placeholder' => esc_html__('Select make', 'autozpro'),
            ),

            array(
                'type'        => 'text',
                'slug'        => 'model',
                "label"       => "Model",
                'placeholder' => esc_html__('Select model', 'autozpro'),
            ),

            array(
                'type'        => 'text',
                'slug'        => 'part',
                "label"       => "part",
                'placeholder' => esc_html__('Select part', 'autozpro'),
            ),

        ));

        foreach (self::$fields as $field) {
            if ('year' === $field['type']) {
                self::$admin_fields[] = array(
                    'name'     => 'autozpro_' . $field['slug'] . '_since',
                    'type'     => 'number',
                    'required' => 'required',
                    // translators: %s field name, for example: Year.
                    'title'    => sprintf(esc_html__('Vehicle %s (since)', 'autozpro'), $field['label']),
                );
                self::$admin_fields[] = array(
                    'name'  => 'autozpro_' . $field['slug'] . '_until',
                    'type'  => 'number',
                    // translators: %s field name, for example: Year.
                    'title' => sprintf(esc_html__('Vehicle %s (until)', 'autozpro'), $field['label']),
                );
            } else {
                self::$admin_fields[] = array(
                    'name'     => 'autozpro_' . $field['slug'],
                    'required' => 'required',
                    // translators: %s field name, for example: Model.
                    'title'    => sprintf(esc_html__('Vehicle %s', 'autozpro'), $field['label']),
                );
            }
        }

        $attribute_slug = $this->get_attribute_slug();
        if ($attribute_slug) {
            add_action($attribute_slug . '_add_form_fields', array($this, 'add_form_fields'));
            add_action($attribute_slug . '_edit_form_fields', array($this, 'edit_form_fields'));
            add_action('create_' . $attribute_slug, array($this, 'save_form'));
            add_action('edit_' . $attribute_slug, array($this, 'save_form'));
        }
    }

    public function escape($value) {
        return sanitize_text_field(esc_html($value));
    }

    /**
     * Adds lang query parameter to the AJAX url.
     *
     * @param string $url AJAX url.
     * @return string
     * @since 1.6.0
     *
     */
    public function ajax_url(string $url = ''): string {
        if (empty($url)) {
            $url = admin_url('admin-ajax.php');
        }

        return $url;
    }

    /**
     * Returns data for vehicle select controls.
     */
    public function ajax_vehicle_select_load_data() {
        if (
            !isset($_POST['nonce'])
            || !wp_verify_nonce(
                sanitize_key(wp_unslash($_POST['nonce'])),
                'autozpro_sputnik_vehicle_select_load_data'
            )
        ) {
            wp_send_json_error(
                array(
                    'message' => esc_html__('Action failed. Please refresh the page and retry.', 'autozpro'),
                )
            );
        }

        if (!isset($_POST['data']['for'])) {
            wp_send_json_error();
        }

        $data_for = sanitize_text_field(wp_unslash($_POST['data']['for']));

        $index = -1;

        foreach (self::$fields as $field_index => $field) {
            if ($data_for === $field['slug']) {
                $index = $field_index;
                break;
            }
        }

        if (-1 === $index) {
            wp_send_json_error();
        }

        $values        = array();
        $fields_before = array_slice(self::$fields, 0, $index);

        foreach ($fields_before as $field) {
            if (!isset($_POST['data']['values'][$field['slug']])) {
                wp_send_json_error();
            }

            $values[$field['slug']] = sanitize_text_field(
                wp_unslash($_POST['data']['values'][$field['slug']])
            );
        }


        $options = $this->get_options($values);

        wp_send_json_success($options);
    }

    public function auto_parts_add_settings_tab($settings_tab) {

        $settings_tab['product_autoparts'] = esc_html__('Auto Parts', 'autozpro');
        return $settings_tab;
    }

    public function auto_parts_get_settings($settings, $current_section) {
        if ('product_autoparts' == $current_section) {

            $custom_settings = array(

                array(
                    'name' => esc_html__('Auto Parts', 'autozpro'),
                    'type' => 'title',
                    'id'   => 'autozpro_options_auto_parts'
                ),

                array(
                    'name'     => esc_html__('Vehicle Attribute', 'autozpro'),
                    'type'     => 'select',
                    'desc_tip' => true,
                    'id'       => 'autozpro_options_vehicle_attribute',
                    'options'  => $this->product_attributes_options()
                ),

                array('type' => 'sectionend', 'id' => 'autozpro_options_auto_parts'),

            );

            return $custom_settings;
        } else {
            return $settings;
        }

    }

    public function product_attributes_options() {
        $options    = array(
            '' => esc_html__('Not selected', 'autozpro')
        );
        $attributes = wc_get_attribute_taxonomies();
        foreach ($attributes as $attribute) {
            $options[$attribute->attribute_name] = sprintf(esc_html('%1$s [%2$s]'), $attribute->attribute_label, $attribute->attribute_id);
        }
        return $options;
    }

    public function get_attribute_slug() {

        $attribute_name = autozpro_get_theme_option('vehicle_attribute');

        if (empty($attribute_name)) {
            return null;
        }

        return sanitize_key(wc_attribute_taxonomy_name($attribute_name));
    }

    public function add_form_fields() {
        foreach (self::$admin_fields as $field) {
            $type     = 'text';
            $required = '';
            if (isset($field['type']) && $field['type'] != '') {
                $type = $field['type'];
            }
            if (isset($field['required']) && $field['required'] != '') {
                $required = $field['required'];
            }
            ?>
            <div class="form-field term-<?php echo esc_attr($field['name']); ?>-wrap">
                <label for="tag-<?php echo esc_attr($field['name']); ?>">
                    <?php echo esc_html($field['title']); ?>
                </label>
                <input
                        name="<?php echo esc_attr($field['name']); ?>"
                        id="tag-<?php echo esc_attr($field['name']); ?>"
                        type="<?php echo esc_attr($type); ?>"
                        value=""
                    <?php echo esc_attr($required); ?>
                >
            </div>
            <?php
        }
    }

    /**
     * Adds a fields to the vehicle edit form.
     *
     * @param WP_Term $term WordPress term object.
     */
    public function edit_form_fields(WP_Term $term) {
        $original_term_id = $this->get_original_term_id($term);

        foreach (self::$admin_fields as $field) {
            $field_value = get_term_meta($original_term_id, $field['name'], true);
            $type        = 'text';
            $required    = '';
            if (isset($field['type']) && $field['type'] != '') {
                $type = $field['type'];
            }
            if (isset($field['required']) && $field['required'] != '') {
                $required = $field['required'];
            }
            ?>
            <tr class="form-field term-<?php echo esc_attr($field['name']); ?>-wrap">
                <th scope="row">
                    <label for="<?php echo esc_attr($field['name']); ?>">
                        <?php echo esc_html($field['title']); ?>
                    </label>
                </th>
                <td>
                    <input
                            name="<?php echo esc_attr($field['name']); ?>"
                            id="<?php echo esc_attr($field['name']); ?>"
                            type="<?php echo esc_attr($type); ?>"
                            value="<?php echo esc_attr($field_value); ?>"
                        <?php echo esc_attr($required); ?>

                        <?php disabled($term->term_id !== $original_term_id); ?>
                    >
                </td>
            </tr>
            <?php
        }
    }

    public static function get_original_term_id($term) {
        $term                  = get_term($term);
        $default_language_code = apply_filters('wpml_default_language', null);

        return absint(apply_filters('wpml_object_id', $term->term_id, $term->taxonomy, true, $default_language_code));
    }

    public function save_form($term_id) {
        foreach (self::$admin_fields as $field) {
            if (!isset($_POST[$field['name']])) {
                return;
            }
        }

        if (!current_user_can('edit_term', $term_id)) {
            return;
        }
        if (!isset($_POST['_wpnonce']) && !isset($_POST['_wpnonce_add-tag'])) {
            return;
        }

        // Sanitization here would be redundant.
        // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if (
            !wp_verify_nonce(wp_unslash($_POST['_wpnonce_add-tag']), 'add-tag')
            && !wp_verify_nonce(wp_unslash($_POST['_wpnonce']), 'update-tag_' . $term_id)
        ) {
            return;
        }
        // phpcs:enable

        $term = get_term($term_id);

        if (!($term instanceof WP_Term)) {
            return;
        }

        $original_term_id = $this->get_original_term_id($term);

        foreach (self::$admin_fields as $field) {
            if ($term_id !== $original_term_id) {
                delete_term_meta($term_id, $field['name']);
            } else {
                $field_value = sanitize_text_field(wp_unslash($_POST[$field['name']]));

                update_term_meta($term_id, $field['name'], $field_value);

            }
        }

        update_option('autozpro_sputnik_vehicle_fields', $this->get_compatibility());
    }

    public function get_compatibility() {
        $array      = array();
        $attributes = get_terms(
            array(
                'taxonomy'   => array($this->get_attribute_slug()),
                'hide_empty' => false,
            )
        );

        foreach (self::$fields as $index => $field) {
            $slug = $field['slug'];
            foreach ($attributes as $key => $attribute) {
                // Get all attribute meta data
                $meta = get_term_meta($attribute->term_id);
                if ('year' === $field['type']) {
                    $year_until = '';
                    if (isset($meta['autozpro_' . $slug . '_since'][0]) && $meta['autozpro_' . $slug . '_since'][0] != '') {
                        if (isset($meta['autozpro_' . $slug . '_until'][0]) && $meta['autozpro_' . $slug . '_until'][0] != '') {
                            $year_until = '-' . $meta['autozpro_' . $slug . '_until'][0];
                        }
                        $array[$attribute->term_id][$field['slug']] = $meta['autozpro_' . $slug . '_since'][0] . $year_until;
                    }


                } else {
                    if (isset($meta['autozpro_' . $slug][0]) && $meta['autozpro_' . $slug][0] != '') {
                        $array[$attribute->term_id][$field['slug']] = $meta['autozpro_' . $slug][0];
                    }
                }
            }
        }
        return json_encode($array);
    }

    /**
     * Returns vehicle link.
     *
     * @param string|array $vehicle Vehicle or vehicle slug.
     *
     * @return string
     * @since 1.4.0
     *
     */
    public static function get_vehicle_link($vehicle) {
        if (!class_exists('WooCommerce')) {
            return '';
        }
        $term_object    = get_term($vehicle);
        $attribute_name = autozpro_get_theme_option('vehicle_attribute', 'compatibility');

        return add_query_arg(
            array(
                'filter_' . $attribute_name => $term_object->slug,
            ),
            get_permalink(wc_get_page_id('shop'))
        );
    }

    public function get_options(array $values): array {

        $attributes      = json_decode(get_option('autozpro_sputnik_vehicle_fields'), true);
        $fields          = array_slice(self::$fields, 0, count($values) + 1);
        $return_vehicles = count($values) === count(self::$fields) - 1;
        $options         = array();
        if ($attributes) {
            if ($values) {
                foreach ($values as $key => $value) {
                    foreach ($attributes as $index => $attribute) {
                        if ($key == 'produced') {
                            $array_year = explode('-', $attribute[$key]);
                            if (isset($array_year[1]) || $array_year[1] != '') {
                                if ((int)$array_year[1] < (int)$value || (int)$array_year[0] > (int)$value) {
                                    unset($attributes[$index]);
                                }
                            } else {
                                if ($attribute[$key] != $value) {
                                    unset($attributes[$index]);
                                }
                            }

                        } else {
                            if ($attribute[$key] != $value) {
                                unset($attributes[$index]);
                            }
                        }
                    }
                }
            }

            foreach ($fields as $index => $field) {
                $slug       = $field['slug'];
                $last_field = count($fields) === $index + 1;
                if ($last_field) {


                    if ($return_vehicles) {

                        foreach ($attributes as $key => $attribute) {
                            $value = array();
                            if ($attribute[$slug]) {
                                $value['title'] = $attribute[$slug];
                                $value['value'] = $this->get_vehicle_link($key);
                                $options[]      = $value;
                            }
                        }
                    } else {
                        $array = array();

                        foreach ($attributes as $key => $attribute) {
                            if ($attribute[$slug]) {
                                if ($field['type'] == 'year') {
                                    $array_year = explode('-', $attribute[$slug]);
                                    $array[]    = $array_year[0];
                                } else {
                                    $array[] = $attribute[$slug];
                                }
                            }
                        }
                        $array = array_unique($array);
                        sort($array);
                        foreach ($array as $key => $item) {
                            $options[$key]['title'] = $item;
                            $options[$key]['value'] = $item;
                        }
                    }
                }
            }
        }

        return $options;
    }

    public function vehicle_select_shortcode() { ?>
        <form role="search" method="get" class="autozpro-block-finder__form" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="autozpro-vehicle-select search_compatibility " data-ajax-url="<?php echo esc_url(apply_filters('autozpro_sputnik_ajax_url', '')); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('autozpro_sputnik_vehicle_select_load_data')); ?>">
                <div class="autozpro-vehicle-select__body">
                    <?php
                    static $index = 0;
                    $index++;
                    ?>
                    <?php foreach (self::$fields as $index => $field) : ?>
                        <?php
                        $options      = 0 === $index ? $this->get_options(array()) : array();
                        $item_classes = array('autozpro-vehicle-select__item');

                        if (0 !== $index) {
                            $item_classes[] = 'autozpro-vehicle-select__item--disabled';
                        }
                        ?>
                        <div class=" <?php echo esc_attr(implode(' ', $item_classes)); ?>" data-label="<?php echo esc_attr($field['label']); ?>">
                            <select class="autozpro-vehicle-select__item-control" name="<?php echo esc_attr($field['slug']); ?>" aria-label="<?php echo esc_attr($field['label']); ?>" <?php disabled(0 !== $index); ?>>
                                <option value="null"><?php echo sprintf('%s', esc_html($field['placeholder'])); ?></option>
                                <?php foreach ($options as $option) : ?>
                                    <option value="<?php echo esc_attr(wp_json_encode($option['value'])); ?>">
                                        <?php echo esc_html($option['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="autozpro-vehicle-select__item-loader"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="post_type" value="product"/>
                <button class="autozpro-block-finder__button" type="submit">
                    <span><?php echo esc_html__('Go', 'autozpro'); ?></span>
                    <i class="autozpro-icon-angle-right"></i>
                </button>
            </div>
        </form>
        <?php
    }
}

Autozpro_Woocommerce_AutoParts::get_instance();