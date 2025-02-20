<?php
/*
Plugin Name: WPC Product FAQs for WooCommerce
Plugin URI: https://wpclever.net/
Description: Ultimate solution to manage WooCommerce product FAQs.
Version: 2.2.3
Author: WPClever
Author URI: https://wpclever.net
Text Domain: wpc-product-faqs
Domain Path: /languages/
Requires Plugins: woocommerce
Requires at least: 4.0
Tested up to: 6.7
WC requires at least: 3.0
WC tested up to: 9.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) || exit;

! defined( 'WPCPF_VERSION' ) && define( 'WPCPF_VERSION', '2.2.3' );
! defined( 'WPCPF_LITE' ) && define( 'WPCPF_LITE', __FILE__ );
! defined( 'WPCPF_FILE' ) && define( 'WPCPF_FILE', __FILE__ );
! defined( 'WPCPF_URI' ) && define( 'WPCPF_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WPCPF_DIR' ) && define( 'WPCPF_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WPCPF_REVIEWS' ) && define( 'WPCPF_REVIEWS', 'https://wordpress.org/support/plugin/wpc-product-faqs/reviews/?filter=5' );
! defined( 'WPCPF_CHANGELOG' ) && define( 'WPCPF_CHANGELOG', 'https://wordpress.org/plugins/wpc-product-faqs/#developers' );
! defined( 'WPCPF_DISCUSSION' ) && define( 'WPCPF_DISCUSSION', 'https://wordpress.org/support/plugin/wpc-product-faqs' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WPCPF_URI );

include 'includes/dashboard/wpc-dashboard.php';
include 'includes/kit/wpc-kit.php';
include 'includes/hpos.php';

if ( ! function_exists( 'wpcpf_init' ) ) {
	add_action( 'plugins_loaded', 'wpcpf_init', 11 );

	function wpcpf_init() {
		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'wpcpf_notice_wc' );

			return null;
		}

		if ( ! class_exists( 'WPCleverWpcpf' ) && class_exists( 'WC_Product' ) ) {
			class WPCleverWpcpf {
				protected static $instance = null;

				public static function instance() {
					if ( is_null( self::$instance ) ) {
						self::$instance = new self();
					}

					return self::$instance;
				}

				function __construct() {
					add_action( 'init', [ $this, 'init' ] );

					// meta box
					add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
					add_action( 'save_post_wpc_product_faq', [ $this, 'save_product_faq' ] );

					// enqueue
					add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
					add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

					// settings page
					add_action( 'admin_menu', [ $this, 'admin_menu' ] );

					// settings link
					add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
					add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );

					// add tab
					add_filter( 'woocommerce_product_tabs', [ $this, 'product_tabs' ] );

					// ajax
					add_action( 'wp_ajax_wpcpf_add_faq', [ $this, 'ajax_add_faq' ] );
					add_action( 'wp_ajax_wpcpf_search_faq', [ $this, 'ajax_search_faq' ] );
					add_action( 'wp_ajax_wpcpf_search_term', [ $this, 'ajax_search_term' ] );

					// product data
					add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tabs' ] );
					add_action( 'woocommerce_product_data_panels', [ $this, 'product_data_panels' ] );
					add_action( 'woocommerce_process_product_meta', [ $this, 'process_product_meta' ] );
				}

				function init() {
					// load text-domain
					load_plugin_textdomain( 'wpc-product-faqs', false, basename( WPCPF_DIR ) . '/languages/' );

					$labels = [
						'name'          => _x( 'Product FAQs', 'Post Type General Name', 'wpc-product-faqs' ),
						'singular_name' => _x( 'Product FAQ', 'Post Type Singular Name', 'wpc-product-faqs' ),
						'add_new_item'  => esc_html__( 'Add New Product FAQ', 'wpc-product-faqs' ),
						'add_new'       => esc_html__( 'Add New', 'wpc-product-faqs' ),
						'edit_item'     => esc_html__( 'Edit Product FAQ', 'wpc-product-faqs' ),
						'update_item'   => esc_html__( 'Update Product FAQ', 'wpc-product-faqs' ),
						'search_items'  => esc_html__( 'Search Product FAQ', 'wpc-product-faqs' ),
					];

					$args = [
						'label'               => esc_html__( 'Product FAQ', 'wpc-product-faqs' ),
						'labels'              => $labels,
						'supports'            => [ 'title', 'editor' ],
						'hierarchical'        => false,
						'public'              => false,
						'show_ui'             => true,
						'show_in_menu'        => true,
						'show_in_nav_menus'   => true,
						'show_in_admin_bar'   => true,
						'menu_position'       => 28,
						'menu_icon'           => 'dashicons-testimonial',
						'can_export'          => true,
						'has_archive'         => false,
						'exclude_from_search' => true,
						'publicly_queryable'  => false,
						'capability_type'     => 'post',
						'show_in_rest'        => false,
					];

					register_post_type( 'wpc_product_faq', $args );

					// shortcode
					add_shortcode( 'wpc_product_faqs', [ $this, 'shortcode' ] );
					add_shortcode( 'wpcpf', [ $this, 'shortcode' ] );
				}

				function add_meta_boxes() {
					add_meta_box( 'wpcpf_configuration', esc_html__( 'Configuration', 'wpc-product-faqs' ), [
						$this,
						'configuration_callback'
					], 'wpc_product_faq', 'advanced', 'low' );
				}

				function configuration_callback( $post ) {
					$post_id = $post->ID;
					$type    = get_post_meta( $post_id, 'type', true ) ?: 'all';
					$terms   = get_post_meta( $post_id, 'terms', true ) ?: [];
					?>
                    <table class="wpcpf_configuration_table">
                        <tr class="wpcpf_configuration_tr">
                            <td class="wpcpf_configuration_td" colspan="2">
								<?php esc_html_e( 'Select which products you want to add this FAQs automatically. If "None" is set, you can still manually choose to add this in the "FAQs" tab of each individual product page.', 'wpc-product-faqs' ); ?>
                            </td>
                        </tr>
                        <tr class="wpcpf_configuration_tr">
                            <td class="wpcpf_configuration_th">
                                <label> <select name="wpcpf_type" class="wpcpf_type">
                                        <option value="none" <?php selected( $type, 'none' ); ?>><?php esc_html_e( 'None', 'wpc-product-faqs' ); ?></option>
                                        <option value="all" <?php selected( $type, 'all' ); ?>><?php esc_html_e( 'All products', 'wpc-product-faqs' ); ?></option>
										<?php
										$taxonomies = get_object_taxonomies( 'product', 'objects' ); //$taxonomies = get_taxonomies( [ 'object_type' => [ 'product' ] ], 'objects' );

										foreach ( $taxonomies as $taxonomy ) {
											echo '<option value="' . esc_attr( $taxonomy->name ) . '" ' . ( $type === $taxonomy->name ? 'selected' : '' ) . '>' . esc_html( $taxonomy->label ) . '</option>';
										}
										?>
                                    </select> </label>
                            </td>
                            <td class="wpcpf_configuration_td">
                                <div class="wpcpf_type_row wpcpf_type_terms">
									<?php
									if ( ! is_array( $terms ) ) {
										$terms = array_map( 'trim', explode( ',', $terms ) );
									}
									?>
                                    <label>
                                        <select class="wpcpf_terms_select" multiple="multiple" name="wpcpf_terms[]" data-<?php echo esc_attr( $type ); ?>="<?php echo esc_attr( implode( ',', $terms ) ); ?>">
											<?php
											if ( ! empty( $terms ) ) {
												foreach ( $terms as $t ) {
													if ( $term = get_term_by( 'slug', $t, $type ) ) {
														echo '<option value="' . esc_attr( $t ) . '" selected>' . esc_html( $term->name ) . '</option>';
													}
												}
											}
											?>
                                        </select> </label>
                                </div>
                            </td>
                        </tr>
                    </table>
					<?php
				}

				function save_product_faq( $post_id ) {
					if ( isset( $_POST['wpcpf_type'] ) ) {
						update_post_meta( $post_id, 'type', sanitize_text_field( $_POST['wpcpf_type'] ) );
					}

					if ( isset( $_POST['wpcpf_terms'] ) ) {
						update_post_meta( $post_id, 'terms', self::sanitize_array( $_POST['wpcpf_terms'] ) );
					}
				}

				function enqueue_scripts() {
					wp_enqueue_style( 'wpcpf-frontend', WPCPF_URI . 'assets/css/frontend.css', [], WPCPF_VERSION );
				}

				function admin_enqueue_scripts() {
					wp_enqueue_style( 'wpcpf-backend', WPCPF_URI . 'assets/css/backend.css', [ 'woocommerce_admin_styles' ], WPCPF_VERSION );
					wp_enqueue_script( 'wpcpf-backend', WPCPF_URI . 'assets/js/backend.js', [
						'jquery',
						'jquery-ui-sortable',
						'wc-enhanced-select',
						'selectWoo'
					], WPCPF_VERSION, true );
				}

				function action_links( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$how = '<a href="' . esc_url( admin_url( 'admin.php?page=wpclever-wpcpf&tab=how' ) ) . '">' . esc_html__( 'How to use?', 'wpc-product-faqs' ) . '</a>';
						array_unshift( $links, $how );
					}

					return (array) $links;
				}

				function row_meta( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$row_meta = [
							'support' => '<a href="' . esc_url( WPCPF_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-product-faqs' ) . '</a>',
						];

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Product FAQs', 'wpc-product-faqs' ), esc_html__( 'Product FAQs', 'wpc-product-faqs' ), 'manage_options', 'wpclever-wpcpf', [
						$this,
						'admin_menu_content'
					] );
				}

				function admin_menu_content() {
					$active_tab = sanitize_key( $_GET['tab'] ?? 'how' );
					?>
                    <div class="wpclever_settings_page wrap">
                        <h1 class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Product FAQs', 'wpc-product-faqs' ) . ' ' . esc_html( WPCPF_VERSION ); ?></h1>
                        <div class="wpclever_settings_page_desc about-text">
                            <p>
								<?php printf( /* translators: stars */ esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'wpc-product-faqs' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
                                <br/>
                                <a href="<?php echo esc_url( WPCPF_REVIEWS ); ?>" target="_blank"><?php esc_html_e( 'Reviews', 'wpc-product-faqs' ); ?></a> |
                                <a href="<?php echo esc_url( WPCPF_CHANGELOG ); ?>" target="_blank"><?php esc_html_e( 'Changelog', 'wpc-product-faqs' ); ?></a> |
                                <a href="<?php echo esc_url( WPCPF_DISCUSSION ); ?>" target="_blank"><?php esc_html_e( 'Discussion', 'wpc-product-faqs' ); ?></a>
                            </p>
                        </div>
                        <div class="wpclever_settings_page_nav">
                            <h2 class="nav-tab-wrapper">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpcpf&tab=how' ) ); ?>" class="<?php echo esc_attr( $active_tab === 'how' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'How to use?', 'wpc-product-faqs' ); ?>
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpc_product_faq' ) ); ?>" class="nav-tab">
									<?php esc_html_e( 'Global FAQs', 'wpc-product-faqs' ); ?>
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-kit' ) ); ?>" class="nav-tab">
									<?php esc_html_e( 'Essential Kit', 'wpc-product-faqs' ); ?>
                                </a>
                            </h2>
                        </div>
                        <div class="wpclever_settings_page_content">
							<?php if ( $active_tab === 'how' ) { ?>
                                <div class="wpclever_settings_page_content_text">
                                    <p>
										<?php esc_html_e( '1. Global FAQs: Switch to Global FAQs tab to add the FAQs then you can use these FAQs in each product.', 'wpc-product-faqs' ); ?>
                                    </p>
                                    <p>
										<?php esc_html_e( '2. Product basis FAQs: When adding/editing the product you can choose the FAQs tab then add some global/custom FAQs as you want.', 'wpc-product-faqs' ); ?>
                                    </p>
                                </div>
							<?php } ?>
                        </div><!-- /.wpclever_settings_page_content -->
                        <div class="wpclever_settings_page_suggestion">
                            <div class="wpclever_settings_page_suggestion_label">
                                <span class="dashicons dashicons-yes-alt"></span> Suggestion
                            </div>
                            <div class="wpclever_settings_page_suggestion_content">
                                <div>
                                    To display custom engaging real-time messages on any wished positions, please install
                                    <a href="https://wordpress.org/plugins/wpc-smart-messages/" target="_blank">WPC Smart Messages</a> plugin. It's free!
                                </div>
                                <div>
                                    Wanna save your precious time working on variations? Try our brand-new free plugin
                                    <a href="https://wordpress.org/plugins/wpc-variation-bulk-editor/" target="_blank">WPC Variation Bulk Editor</a> and
                                    <a href="https://wordpress.org/plugins/wpc-variation-duplicator/" target="_blank">WPC Variation Duplicator</a>.
                                </div>
                            </div>
                        </div>
                    </div>
					<?php
				}

				function ajax_add_faq() {
					self::faq( '', [
						'type'    => sanitize_key( $_POST['type'] ?? 'custom' ),
						'title'   => esc_html__( 'FAQ title', 'wpc-product-faqs' ),
						'editor'  => sanitize_key( $_POST['editor'] ?? '' ),
						'content' => ''
					], true );

					wp_die();
				}

				function ajax_search_faq() {
					$return = [];

					$search_results = new WP_Query( [
						'post_type'           => 'wpc_product_faq',
						's'                   => sanitize_text_field( $_GET['q'] ),
						'post_status'         => 'publish',
						'ignore_sticky_posts' => 1,
						'posts_per_page'      => 50
					] );

					if ( $search_results->have_posts() ) {
						while ( $search_results->have_posts() ) {
							$search_results->the_post();
							$title    = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
							$return[] = [ $search_results->post->ID, $title ];
						}
					}

					wp_send_json( $return );
				}

				function ajax_search_term() {
					$return = [];

					$args = [
						'taxonomy'   => sanitize_text_field( $_REQUEST['taxonomy'] ),
						'orderby'    => 'id',
						'order'      => 'ASC',
						'hide_empty' => false,
						'fields'     => 'all',
						'name__like' => sanitize_text_field( $_REQUEST['q'] ),
					];

					$terms = get_terms( $args );

					if ( count( $terms ) ) {
						foreach ( $terms as $term ) {
							$return[] = [ $term->slug, $term->name ];
						}
					}

					wp_send_json( $return );
				}

				function faq( $key, $faq, $new = false ) {
					if ( empty( $key ) || ( strlen( $key ) > 4 ) ) {
						$key = self::generate_key();
					}
					?>
                    <div class="wpcpf-faq">
                        <span class="wpcpf-faq-move button"><?php esc_attr_e( 'Move', 'wpc-product-faqs' ); ?></span>
                        <span class="wpcpf-faq-label">#<?php echo esc_attr( $faq['type'] ); ?></span>
                        <input class="wpcpf-faq-remove button" type="button" value="<?php esc_attr_e( 'Remove', 'wpc-product-faqs' ); ?>"/>
						<?php
						if ( $faq['type'] === 'custom' ) {
							$editor_id = ! empty( $faq['editor'] ) ? $faq['editor'] : 'wpcpf-editor-' . $key; ?>
                            <div class="wpcpf-faq-line">
                                <input type="hidden" name="<?php echo esc_attr( 'wpcpf_faqs[' . $key . '][type]' ); ?>" value="<?php echo esc_attr( $faq['type'] ); ?>"/>
                                <label>
                                    <input type="text" name="<?php echo esc_attr( 'wpcpf_faqs[' . $key . '][title]' ); ?>" placeholder="<?php esc_attr_e( 'FAQ title', 'wpc-product-faqs' ); ?>" style="width: 100%" value="<?php echo esc_attr( $faq['title'] ); ?>" required/>
                                </label>
                            </div>
                            <div class="wpcpf-faq-line">
								<?php
								if ( $new ) {
									echo '<textarea id="' . esc_attr( $editor_id ) . '" name="' . esc_attr( 'wpcpf_faqs[' . $key . '][content]' ) . '" rows="10"></textarea>';
								} else {
									wp_editor( $faq['content'], $editor_id, [
										'textarea_name' => esc_attr( 'wpcpf_faqs[' . $key . '][content]' ),
										'textarea_rows' => 10
									] );
								}
								?>
                            </div>
						<?php } else { ?>
                            <div class="wpcpf-faq-line">
                                <input type="hidden" name="<?php echo esc_attr( 'wpcpf_faqs[' . $key . '][type]' ); ?>" value="<?php echo esc_attr( $faq['type'] ); ?>"/>
                                <label>
                                    <select class="wpcpf-faq-search" multiple="multiple" name="<?php echo esc_attr( 'wpcpf_faqs[' . $key . '][title][]' ); ?>">
										<?php
										if ( ! is_array( $faq['title'] ) ) {
											$selected_faqs = explode( ',', $faq['title'] );
										} else {
											$selected_faqs = $faq['title'];
										}

										if ( ! empty( $selected_faqs ) ) {
											foreach ( $selected_faqs as $selected_faq ) {
												if ( ! empty( $selected_faq ) && ( $selected_faq_data = get_post( $selected_faq ) ) ) {
													echo '<option value="' . esc_attr( $selected_faq_data->ID ) . '" selected>' . esc_html( $selected_faq_data->post_title ) . '</option>';
												}
											}
										}
										?>
                                    </select> </label>
                                <input type="hidden" name="<?php echo esc_attr( 'wpcpf_faqs[' . $key . '][content]' ); ?>" value="auto"/>
                            </div>
						<?php } ?>
                    </div>
					<?php
				}

				function new_faq() {
					?>
                    <div class="wpcpf-new-faq">
                        <label> <select class="wpcpf-new-faq-type">
                                <option value="global"><?php esc_html_e( 'Global FAQ', 'wpc-product-faqs' ); ?></option>
                                <option value="custom"><?php esc_html_e( 'Custom FAQ', 'wpc-product-faqs' ); ?></option>
                            </select> </label>
                        <input type="button" class="button wpcpf-add" value="<?php esc_attr_e( '+ Add new FAQ', 'wpc-product-faqs' ); ?>"/>
                    </div>
					<?php
				}

				function sanitize_array( $arr ) {
					foreach ( (array) $arr as $k => $v ) {
						if ( is_array( $v ) ) {
							$arr[ $k ] = self::sanitize_array( $v );
						} else {
							$arr[ $k ] = sanitize_text_field( $v );
						}
					}

					return $arr;
				}

				function shortcode( $attrs ) {
					$output = '';
					$attrs  = shortcode_atts( [ 'id' => null ], $attrs, 'wpcpf' );

					if ( ! $attrs['id'] ) {
						global $product;
						$attrs['id'] = $product->get_id();
					}

					if ( $attrs['id'] ) {
						$output = self::product_faqs( $attrs['id'] );
					}

					return apply_filters( 'wpcpf_shortcode', $output, $attrs['id'] );
				}

				function product_tabs( $tabs ) {
					global $product;

					if ( $product ) {
						$product_id = $product->get_id();

						if ( $product_id && self::product_has_faqs( $product_id ) ) {
							$tabs['wpcpf'] = [
								'title'    => esc_html__( 'FAQs', 'wpc-product-faqs' ),
								'callback' => [ $this, 'tab_content' ]
							];
						}
					}

					return $tabs;
				}

				function tab_content( $key, $tab ) {
					global $product;

					if ( $product ) {
						$product_id = $product->get_id();

						if ( $product_id ) {
							echo '<h2>' . esc_html( $tab['title'] ) . '</h2>';
							echo self::product_faqs( $product_id );
						}
					}
				}

				function product_has_faqs( $product_id ) {
					// global faqs
					$args  = [
						'post_type'    => 'wpc_product_faq',
						'meta_key'     => 'type',
						'meta_value'   => 'none',
						'meta_compare' => '!=',
					];
					$query = new WP_Query( $args );

					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							$post_id = get_the_ID();
							$type    = ! empty( get_post_meta( $post_id, 'type', true ) ) ? get_post_meta( $post_id, 'type', true ) : 'none';

							if ( $type === 'all' ) {
								wp_reset_postdata();

								return true;
							} else {
								// terms
								if ( ( $terms = get_post_meta( $post_id, 'terms', true ) ) && ! empty( $terms ) ) {
									if ( ! is_array( $terms ) ) {
										$terms = explode( ',', get_post_meta( $post_id, 'terms', true ) );
									}

									if ( has_term( $terms, $type, $product_id ) ) {
										wp_reset_postdata();

										return true;
									}
								}
							}
						}

						wp_reset_postdata();
					}

					// product faqs
					$product_faqs = get_post_meta( $product_id, 'wpcpf_faqs', true );

					if ( ! empty( $product_faqs ) ) {
						foreach ( $product_faqs as $product_faq ) {
							if ( $product_faq['type'] === 'global' ) {
								if ( ! empty( $product_faq['title'] ) ) {
									return true;
								}
							} else {
								if ( ! empty( $product_faq['title'] ) && ! empty( $product_faq['content'] ) ) {
									return true;
								}
							}
						}
					}

					return false;
				}

				function product_faqs( $product_id ) {
					$content = '';
					$faqs    = [];

					// global faqs
					$args  = [
						'post_type'    => 'wpc_product_faq',
						'meta_key'     => 'type',
						'meta_value'   => 'none',
						'meta_compare' => '!=',
					];
					$query = new WP_Query( $args );

					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							$post_id = get_the_ID();
							$type    = ! empty( get_post_meta( $post_id, 'type', true ) ) ? get_post_meta( $post_id, 'type', true ) : 'none';

							if ( $type === 'all' ) {
								$faqs[] = [
									'type'    => 'all',
									'title'   => get_the_title(),
									'content' => get_the_content(),
								];
							} else {
								if ( ( $terms = get_post_meta( $post_id, 'terms', true ) ) && ! empty( $terms ) ) {
									if ( ! is_array( $terms ) ) {
										$terms = explode( ',', $terms );
									}

									if ( has_term( $terms, $type, $product_id ) ) {
										$faqs[] = [
											'type'    => 'terms',
											'title'   => get_the_title(),
											'content' => get_the_content(),
										];
									}
								}
							}
						}

						wp_reset_postdata();
					}

					// product faqs
					$product_faqs = get_post_meta( $product_id, 'wpcpf_faqs', true );

					if ( ! empty( $product_faqs ) ) {
						foreach ( $product_faqs as $product_faq ) {
							if ( $product_faq['type'] === 'global' ) {
								if ( ! empty( $product_faq['title'] ) ) {
									if ( ! is_array( $product_faq['title'] ) ) {
										$global_ids = explode( ',', $product_faq['title'] );
									} else {
										$global_ids = $product_faq['title'];
									}

									foreach ( $global_ids as $global_id ) {
										if ( $global_faq = get_post( $global_id ) ) {
											$faqs[] = [
												'type'    => esc_attr( $product_faq['type'] ),
												'title'   => $global_faq->post_title,
												'content' => $global_faq->post_content
											];
										}
									}
								}
							} else {
								if ( ! empty( $product_faq['title'] ) && ! empty( $product_faq['content'] ) ) {
									$faqs[] = [
										'type'    => esc_attr( $product_faq['type'] ),
										'title'   => $product_faq['title'],
										'content' => $product_faq['content']
									];
								}
							}
						}
					}

					if ( ! empty( $faqs ) ) {
						$content .= '<div class="wpcpf-faqs">';

						foreach ( $faqs as $faq ) {
							$faq['content'] = str_replace( '[wpcpf', '[wpcpf_ignored', $faq['content'] );
							$faq['content'] = str_replace( '[wpc_product_faqs', '[wpc_product_faqs_ignored', $faq['content'] );

							$content .= '<div class="wpcpf-faq wpcpf-faq-' . esc_attr( $faq['type'] ) . '">';
							$content .= '<div class="wpcpf-faq-title">' . esc_html( $faq['title'] ) . '</div>';
							$content .= '<div class="wpcpf-faq-content">' . wp_kses_post( do_shortcode( $faq['content'] ) ) . '</div>';
							$content .= '</div><!-- /wpcpf-faq -->';
						}

						$content .= '</div><!-- /wpcpf-faqs -->';
					}

					return apply_filters( 'wpcpf_product_faqs', $content, $product_id );
				}

				function product_data_tabs( $tabs ) {
					$tabs['wpcpf'] = [
						'label'  => esc_html__( 'FAQs', 'wpc-product-faqs' ),
						'target' => 'wpcpf_settings'
					];

					return $tabs;
				}

				function product_data_panels() {
					global $post, $thepostid, $product_object;

					if ( $product_object instanceof WC_Product ) {
						$product_id = $product_object->get_id();
					} elseif ( is_numeric( $thepostid ) ) {
						$product_id = $thepostid;
					} elseif ( $post instanceof WP_Post ) {
						$product_id = $post->ID;
					} else {
						$product_id = 0;
					}

					if ( ! $product_id ) {
						?>
                        <div id='wpcpf_settings' class='panel woocommerce_options_panel wpcpf_settings'>
                            <p style="padding: 0 12px; color: #c9356e"><?php esc_html_e( 'Product wasn\'t returned.', 'wpc-product-faqs' ); ?></p>
                        </div>
						<?php
						return;
					}

					wp_enqueue_editor();
					?>
                    <div id='wpcpf_settings' class='panel woocommerce_options_panel wpcpf_settings'>
                        <div class="wpcpf-global">
                            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpc_product_faq' ) ); ?>" target="_blank"><?php esc_html_e( 'Manage Global FAQs', 'wpc-product-faqs' ); ?></a>
                        </div>
                        <div class="wpcpf-global">
							<?php echo sprintf( /* translators: shortcode */ esc_html__( 'You can use the shortcode %s to show this product FAQs.', 'wpc-product-faqs' ), '<strong>[wpcpf id="' . esc_attr( $product_id ) . '"]</strong>' ); ?>
                        </div>
                        <div class="wpcpf-faqs">
							<?php
							$faqs = get_post_meta( $product_id, 'wpcpf_faqs', true );

							if ( is_array( $faqs ) && ( count( $faqs ) > 0 ) ) {
								foreach ( $faqs as $key => $faq ) {
									self::faq( $key, $faq );
								}
							}
							?>
                        </div>
						<?php self::new_faq(); ?>
                    </div>
					<?php
				}

				function process_product_meta( $post_id ) {
					if ( isset( $_POST['wpcpf_faqs'] ) ) {
						update_post_meta( $post_id, 'wpcpf_faqs', self::sanitize_array( $_POST['wpcpf_faqs'] ) );
					} else {
						delete_post_meta( $post_id, 'wpcpf_faqs' );
					}
				}

				function generate_key() {
					$key         = '';
					$key_str     = apply_filters( 'wpcpf_key_characters', 'abcdefghijklmnopqrstuvwxyz0123456789' );
					$key_str_len = strlen( $key_str );

					for ( $i = 0; $i < apply_filters( 'wpcpf_key_length', 4 ); $i ++ ) {
						$key .= $key_str[ random_int( 0, $key_str_len - 1 ) ];
					}

					if ( is_numeric( $key ) ) {
						$key = self::generate_key();
					}

					return apply_filters( 'wpcpf_generate_key', $key );
				}
			}

			return WPCleverWpcpf::instance();
		}

		return null;
	}
}

if ( ! function_exists( 'wpcpf_notice_wc' ) ) {
	function wpcpf_notice_wc() {
		?>
        <div class="error">
            <p><strong>WPC Product FAQs</strong> requires WooCommerce version 3.0 or greater.</p>
        </div>
		<?php
	}
}
