<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Autozpro_Elementor_Autoparts_Finder extends Elementor\Widget_Base {

    public function get_categories() {
        return array('autozpro-addons');
    }

    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'autozpro-autoparts-finder';
    }

    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__('Auto parts Finder', 'autozpro');
    }

    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-tabs';
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        $this->start_controls_section(
            'section_autoparts_finder',
            [
                'label' => esc_html__('Finder', 'autozpro'),
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Render tabs widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $term_slug = Autozpro_Woocommerce_AutoParts::get_instance()->get_attribute_slug();

        if (!$term_slug) {
            return;
        }

        $term_ids   = get_terms(array(
            'taxonomy'   => $term_slug,
            'hide_empty' => false,
            'fields'     => 'id',
        ));
        $since_year = date('Y');
        $ultil_year = 1900;
        if ($term_ids) {
            foreach ($term_ids as $id) {
                $year_start = get_term_meta($id, 'autozpro_year_since', true);

                if (!empty($year_start) && preg_match('/^\d{4}$/', $year_start)) {
                    $since_year = (int)$year_start < $since_year ? $year_start : $since_year;
                    echo 'e';
                }

                $year_end = get_term_meta($id, 'autozpro_year_until', true);

                if (!empty($year_end) && preg_match('/^\d{4}$/', $year_end)) {
                    $ultil_year = (int)$year_end < $ultil_year ? $ultil_year : $year_end;
                } else {
                    $ultil_year = date('Y');
                }
            }
        }
    }

}

$widgets_manager->register(new Autozpro_Elementor_Autoparts_Finder());
