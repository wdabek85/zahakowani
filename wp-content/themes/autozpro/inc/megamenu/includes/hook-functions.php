<?php

defined('ABSPATH') || exit();

/**
 * Hook to delete post elementor related with this menu
 */
add_action("before_delete_post", "autozpro_megamenu_on_delete_menu_item", 9);
function autozpro_megamenu_on_delete_menu_item($post_id) {
    if (is_nav_menu_item($post_id)) {
        $related_id = autozpro_megamenu_get_post_related_menu($post_id);
        if ($related_id) {
            wp_delete_post($related_id, true);
        }
    }
}


add_filter('elementor/editor/footer', 'autozpro_megamenu_add_back_button_inspector');
function autozpro_megamenu_add_back_button_inspector() {
    if (!isset($_GET['autozpro-menu-editable']) || !$_GET['autozpro-menu-editable']) {
        return;
    }
    ?>
    <script type="text/template" id="tmpl-elementor-panel-footer-content">
        <div id="elementor-panel-footer-back-to-admin"
             class="elementor-panel-footer-tool elementor-leave-open tooltip-target"
             data-tooltip="<?php esc_attr_e('Back', 'autozpro'); ?>">
            <i class="eicon-close" aria-hidden="true"></i>
        </div>
        <div id="elementor-panel-footer-settings"
             class="elementor-panel-footer-tool elementor-leave-open tooltip-target"
             data-tooltip="<?php esc_attr_e('Settings', 'autozpro'); ?>">
            <i class="eicon-cog" aria-hidden="true"></i>
            <span class="elementor-screen-only"><?php echo esc_html__('Page Settings', 'autozpro'); ?></span>
        </div>
        <div id="elementor-panel-footer-navigator" class="elementor-panel-footer-tool tooltip-target"
             data-tooltip="<?php esc_attr_e('Navigator', 'autozpro'); ?>">
            <i class="eicon-navigator" aria-hidden="true"></i>
            <span class="elementor-screen-only">
                    <?php echo esc_html__('Navigator', 'autozpro'); ?>
                </span>
        </div>
        <div id="elementor-panel-footer-responsive" class="elementor-panel-footer-tool">
            <i class="eicon-device-desktop tooltip-target" aria-hidden="true"
               data-tooltip="<?php esc_attr_e('Responsive Mode', 'autozpro'); ?>"></i>
            <span class="elementor-screen-only">
					<?php echo esc_html__('Responsive Mode', 'autozpro'); ?>
				</span>
        </div>
        <div id="elementor-panel-footer-history" class="elementor-panel-footer-tool elementor-leave-open tooltip-target"
             data-tooltip="<?php esc_attr_e('History', 'autozpro'); ?>">
            <i class="eicon-history" aria-hidden="true"></i>
            <span class="elementor-screen-only"><?php echo esc_html__('History', 'autozpro'); ?></span>
        </div>
        <div id="elementor-panel-footer-saver-publish" class="elementor-panel-footer-tool">
            <button id="elementor-panel-saver-button-publish"
                    class="elementor-button elementor-button-success elementor-saver-disabled">
					<span class="elementor-state-icon">
						<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
					</span>
                <span id="elementor-panel-saver-button-publish-label">
						<?php echo esc_html__('Publish', 'autozpro'); ?>
					</span>
            </button>
        </div>
        <div id="elementor-panel-saver-save-options" class="elementor-panel-footer-tool">
            <button id="elementor-panel-saver-button-save-options"
                    class="elementor-button elementor-button-success tooltip-target elementor-disabled"
                    data-tooltip="<?php esc_attr_e('Save Options', 'autozpro'); ?>">
                <i class="eicon-caret-up" aria-hidden="true"></i>
                <span class="elementor-screen-only"><?php echo esc_html__('Save Options', 'autozpro'); ?></span>
            </button>
        </div>
    </script>

    <?php
}

add_action('wp_ajax_autozpro_load_menu_data', 'autozpro_megamenu_load_menu_data');
function autozpro_megamenu_load_menu_data() {
    $nonce   = !empty($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    $menu_id = !empty($_POST['menu_id']) ? absint($_POST['menu_id']) : false;
    if (!wp_verify_nonce($nonce, 'autozpro-menu-data-nonce') || !$menu_id) {
        wp_send_json(array(
            'message' => esc_html__('Access denied', 'autozpro')
        ));
    }

    $data = autozpro_megamenu_get_item_data($menu_id);

    $data = $data ? $data : array();
    if (isset($_POST['istop']) && absint($_POST['istop']) == 1) {
        if (class_exists('Elementor\Plugin')) {
            if (isset($data['enabled']) && $data['enabled']) {
                $related_id = autozpro_megamenu_get_post_related_menu($menu_id);
                if (!$related_id) {
                    autozpro_megamenu_create_related_post($menu_id);
                    $related_id = autozpro_megamenu_get_post_related_menu($menu_id);
                }

                if ($related_id && isset($_REQUEST['menu_id']) && is_admin()) {
                    $url                      = Elementor\Plugin::instance()->documents->get($related_id)->get_edit_url();
                    $data['edit_submenu_url'] = add_query_arg(array('autozpro-menu-editable' => 1), $url);
                }
            } else {
                $url                      = admin_url();
                $data['edit_submenu_url'] = add_query_arg(array('autozpro-menu-createable' => 1, 'menu_id' => $menu_id), $url);
            }
        }
    }

    $results = apply_filters('autozpro_menu_settings_data', array(
        'status' => true,
        'data'   => $data
    ));

    wp_send_json($results);

}

add_action('wp_ajax_autozpro_update_menu_item_data', 'autozpro_megamenu_update_menu_item_data');
function autozpro_megamenu_update_menu_item_data() {
    $nonce = !empty($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'autozpro-update-menu-item')) {
        wp_send_json(array(
            'message' => esc_html__('Access denied', 'autozpro')
        ));
    }

    $settings = !empty($_POST['autozpro-menu-item']) ? ($_POST['autozpro-menu-item']) : array();
    $menu_id  = !empty($_POST['menu_id']) ? absint($_POST['menu_id']) : false;

    do_action('autozpro_before_update_menu_settings', $settings);


    autozpro_megamenu_update_item_data($menu_id, $settings);

    do_action('autozpro_menu_settings_updated', $settings);
    wp_send_json(array('status' => true));
}

add_action('admin_footer', 'autozpro_megamenu_underscore_template');
function autozpro_megamenu_underscore_template() {
    global $pagenow;
    if ($pagenow === 'nav-menus.php') { ?>
        <script type="text/html" id="tpl-autozpro-menu-item-modal">
            <div id="autozpro-modal" class="autozpro-modal">
                <div id="autozpro-modal-body"
                     class="<%= data.edit_submenu === true ? 'edit-menu-active' : ( data.is_loading ? 'loading' : '' ) %>">
                    <% if ( data.edit_submenu !== true && data.is_loading !== true ) { %>
                    <form id="menu-edit-form">
                        <% } %>
                        <div class="autozpro-modal-content">
                            <% if ( data.edit_submenu === true ) { %>
                            <iframe src="<%= data.edit_submenu_url %>"/>
                            <% } else if ( data.is_loading === true ) { %>
                            <i class="fa fa-spin fa-spinner"></i>
                            <% } else { %>

                            <div class="form-group toggle-select-setting">
                                <label for="icon"><?php esc_html_e('Icon', 'autozpro') ?></label>
                                <select id="icon" name="autozpro-menu-item[icon]"
                                        class="form-control icon-picker autozpro-input-switcher autozpro-input-switcher-true"
                                        data-target=".icon-custom">
                                    <option value=""
                                    <%= data.icon == '' ? ' selected' : ''
                                    %>><?php echo esc_html__('No Use', 'autozpro') ?></option>
                                    <option value="1"
                                    <%= data.icon == 1 ? ' selected' : ''
                                    %>><?php echo esc_html__('Custom Class', 'autozpro') ?></option>
                                    <?php foreach (autozpro_megamenu_get_fontawesome_icons() as $value) : ?>
                                        <option value="<?php echo 'autozpro-icon-' . esc_attr($value) ?>"<%= data.icon == '<?php echo 'autozpro-icon-' . esc_attr($value) ?>' ? ' selected' : '' %>><?php echo esc_attr($value) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group icon-custom toggle-select-setting elementor-hidden">
                                <label for="icon-custom"><?php esc_html_e('Icon Class Name', 'autozpro') ?></label>
                                <input type="text" name="autozpro-menu-item[icon_custom]" class="input"
                                       id="icon-custom" value="<%= data.icon_custom %>">
                            </div>
                            <div class="form-group icon-custom toggle-select-setting elementor-hidden">
                                <label for="icon-mage"><?php esc_html_e('Icon Image', 'autozpro') ?></label>
                                <input type="text" name="autozpro-menu-item[icon_image]" class="input"
                                       value="<%= data.icon_image %>"
                                       id="icon-image"/>
                            </div>
                            <div class="form-group">
                                <label for="icon_color"><?php esc_html_e('Icon Color', 'autozpro') ?></label>
                                <input class="color-picker" name="autozpro-menu-item[icon_color]"
                                       value="<%= data.icon_color %>" id="icon_color"/>
                            </div>
                            <div class="form-group">
                                <label for="icon_size"><?php esc_html_e('Icon Size', 'autozpro') ?></label>
                                <input type="text" name="autozpro-menu-item[icon_size]" value="<%= data.icon_size %>"
                                       id="icon_size"/>
                                <span class="unit">px</span>
                            </div>

                            <div class="form-group submenu-setting toggle-select-setting">
                                <label><?php esc_html_e('Mega Submenu Enabled', 'autozpro') ?></label>
                                <select name="autozpro-menu-item[enabled]"
                                        class="autozpro-input-switcher autozpro-input-switcher-true"
                                        data-target=".submenu-width-setting">
                                    <option value="1"
                                    <%= data.enabled == 1? 'selected':''
                                    %>> <?php esc_html_e('Yes', 'autozpro') ?></opttion>
                                    <option value="0"
                                    <%= data.enabled == 0? 'selected':''
                                    %>><?php esc_html_e('No', 'autozpro') ?></opttion>
                                </select>
                                <button id="edit-megamenu" class="button button-primary button-large">
                                    <?php esc_html_e('Edit Megamenu Submenu', 'autozpro') ?>
                                </button>
                            </div>

                            <div class="form-group submenu-width-setting toggle-select-setting elementor-hidden">
                                <label><?php esc_html_e('Sub Megamenu Width', 'autozpro') ?></label>
                                <select name="autozpro-menu-item[customwidth]"
                                        class="autozpro-input-switcher autozpro-input-switcher-true"
                                        data-target=".submenu-subwidth-setting">
                                    <option value="1"
                                    <%= data.customwidth == 1? 'selected':''
                                    %>> <?php esc_html_e('Yes', 'autozpro') ?></opttion>
                                    <option value="0"
                                    <%= data.customwidth == 0? 'selected':''
                                    %>><?php esc_html_e('Full Width', 'autozpro') ?></opttion>
                                    <option value="2"
                                    <%= data.customwidth == 2? 'selected':''
                                    %>><?php esc_html_e('Stretch Width', 'autozpro') ?></opttion>
                                    <option value="3"
                                    <%= data.customwidth == 3? 'selected':''
                                    %>><?php esc_html_e('Container Width', 'autozpro') ?></opttion>
                                </select>
                            </div>

                            <div class="form-group submenu-width-setting submenu-subwidth-setting toggle-select-setting elementor-hidden">
                                <label for="menu_subwidth"><?php esc_html_e('Sub Mega Menu Max Width', 'autozpro') ?></label>
                                <input type="text" name="autozpro-menu-item[subwidth]"
                                       value="<%= data.subwidth?data.subwidth:'600' %>" class="input"
                                       id="menu_subwidth"/>
                                <span class="unit">px</span>
                            </div>

                            <div class="form-group submenu-width-setting submenu-subwidth-setting toggle-select-setting elementor-hidden">
                                <label><?php esc_html_e('Sub Mega Menu Position Left', 'autozpro') ?></label>
                                <select name="autozpro-menu-item[menuposition]">
                                    <option value="0"
                                    <%= data.menuposition == 0? 'selected':''
                                    %>><?php esc_html_e('No', 'autozpro') ?></opttion>
                                    <option value="1"
                                    <%= data.menuposition == 1? 'selected':''
                                    %>> <?php esc_html_e('Yes', 'autozpro') ?></opttion>
                                </select>
                            </div>

                            <% } %>
                        </div>
                        <% if ( data.is_loading !== true && data.edit_submenu !== true ) { %>
                        <div class="autozpro-modal-footer">
                            <a href="#" class="close button"><%= autozpro_memgamnu_params.i18n.close %></a>
                            <?php wp_nonce_field('autozpro-update-menu-item', 'nonce') ?>
                            <input name="menu_id" value="<%= data.menu_id %>" type="hidden"/>
                            <button type="submit" class="button button-primary button-large menu-save pull-right"><%=
                                autozpro_memgamnu_params.i18n.submit %>
                            </button>
                        </div>
                        <% } %>
                        <% if ( data.edit_submenu !== true && data.is_loading !== true ) { %>
                    </form>
                    <% } %>
                </div>
                <div class="autozpro-modal-overlay"></div>
            </div>
        </script>
    <?php }
}







