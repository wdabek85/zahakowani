<?php
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!autozpro_is_woocommerce_activated()) {
    return;
}

class Autozpro_Elementor_Widget_Products_Upsell extends Elementor\Widget_Base {

    public function get_name() {
        return 'autozpro-product-upsell';
    }

    public function get_title() {
        return esc_html__( 'Product Upsells', 'autozpro' );
    }

    public function get_icon() {
        return 'eicon-product-upsell';
    }

    public function get_keywords() {
        return [ 'woocommerce', 'shop', 'store', 'upsell', 'product' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_upsell_content',
            [
                'label' => esc_html__( 'Upsells', 'autozpro' ),
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'autozpro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 12,
                'default' => '4',
                'tablet_default' => '3',
                'mobile_default' => '2',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__( 'Order By', 'autozpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__( 'Date', 'autozpro' ),
                    'title' => esc_html__( 'Title', 'autozpro' ),
                    'price' => esc_html__( 'Price', 'autozpro' ),
                    'popularity' => esc_html__( 'Popularity', 'autozpro' ),
                    'rating' => esc_html__( 'Rating', 'autozpro' ),
                    'rand' => esc_html__( 'Random', 'autozpro' ),
                    'menu_order' => esc_html__( 'Menu Order', 'autozpro' ),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__( 'Order', 'autozpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc' => esc_html__( 'ASC', 'autozpro' ),
                    'desc' => esc_html__( 'DESC', 'autozpro' ),
                ],
            ]
        );

        $this->end_controls_section();

        parent::register_controls();

        $this->start_injection( [
            'at' => 'before',
            'of' => 'section_design_box',
        ] );

        $this->start_controls_section(
            'section_heading_style',
            [
                'label' => esc_html__( 'Heading', 'autozpro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_heading',
            [
                'label' => esc_html__( 'Heading', 'autozpro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'Hide', 'autozpro' ),
                'label_on' => esc_html__( 'Show', 'autozpro' ),
                'default' => 'yes',
                'return_value' => 'yes',
                'prefix_class' => 'show-heading-',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => esc_html__( 'Color', 'autozpro' ),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-wc-products .products > h2' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'show_heading!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'selector' => '{{WRAPPER}}.elementor-wc-products .products > h2',
                'condition' => [
                    'show_heading!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'heading_text_align',
            [
                'label' => esc_html__( 'Text Align', 'autozpro' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'autozpro' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'autozpro' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'autozpro' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-wc-products .products > h2' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
                    'show_heading!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'heading_spacing',
            [
                'label' => esc_html__( 'Spacing', 'autozpro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-wc-products .products > h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'show_heading!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        $this->end_injection();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $limit = '-1';
        $columns = 4;
        $orderby = 'rand';
        $order = 'desc';

        $class = 'woocommerce';
        if ( ! empty( $settings['columns'] ) ) {
            $columns = $settings['columns'];

            wc_set_loop_prop('columns', $settings['columns']);
            if (!empty($settings['columns_widescreen'])) {
                $class .= ' columns-widescreen-' . $settings['columns_widescreen'];
            }

            if (!empty($settings['columns_laptop'])) {
                $class .= ' columns-laptop-' . $settings['columns_laptop'];
            }

            if (!empty($settings['columns_tablet_extra'])) {
                $class .= ' columns-tablet-extra-' . $settings['columns_tablet_extra'];
            }

            if (!empty($settings['columns_tablet'])) {
                $class .= ' columns-tablet-' . $settings['columns_tablet'];
            } else {
                $class .= ' columns-tablet-2';
            }

            if (!empty($settings['columns_mobile_extra'])) {
                $class .= ' columns-mobile-extra-' . $settings['columns_mobile_extra'];
            }

            if (!empty($settings['columns_mobile'])) {
                $class .= ' columns-mobile-' . $settings['columns_mobile'];
            } else {
                $class .= ' columns-mobile-1';
            }
        }

        if ( ! empty( $settings['orderby'] ) ) {
            $orderby = $settings['orderby'];
        }

        if ( ! empty( $settings['order'] ) ) {
            $order = $settings['order'];
        }

        ob_start();
        ?>
    <div class="<?php echo esc_attr($class); ?>">
        <?php
        woocommerce_upsell_display( $limit, $columns, $orderby, $order );
        ?></div>
        <?php
        $upsells_html = ob_get_clean();

        if ( $upsells_html ) {
            $upsells_html = str_replace( '<ul class="products', '<ul class="products elementor-grid', $upsells_html );

            echo wp_kses_post( $upsells_html );
        }
    }

    public function render_plain_content() {}

    public function get_group_name() {
        return 'woocommerce';
    }
}
$widgets_manager->register(new Autozpro_Elementor_Widget_Products_Upsell());
