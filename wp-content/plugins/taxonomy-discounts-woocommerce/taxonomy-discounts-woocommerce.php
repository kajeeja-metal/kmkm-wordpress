<?php
/**
 * Plugin Name: Taxonomy/Term and Role based Discounts for WooCommerce
 * Plugin URI: https://www.webdados.pt/produtos-e-servicos/internet/desenvolvimento-wordpress/taxonomy-term-based-discounts-for-woocommerce/
 * Description: "Taxonomy/Term based Discounts for WooCommerce" lets you configure discount/pricing rules for products based on any product taxonomy terms and WordPress user roles
 * Version: 1.4.1
 * Author: Webdados
 * Author URI: https://www.webdados.pt
 * Text Domain: taxonomy-discounts-woocommerce
 * Domain Path: /languages
 * Requires at least: 4.7
 * Tested up to: 5.1
 * WC tested up to: 3.5.5
**/

/* Partially WooCommerce CRUD ready - Term metas are still fetched from the database using WP_Query for filtering and performance reasons */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* WooCommerce, are you there? */
$wc_taxonomy_discounts_woocommerce_active_plugins = (array) get_option( 'active_plugins', array() );
if ( is_multisite() ) $wc_taxonomy_discounts_woocommerce_active_plugins = array_merge( $wc_taxonomy_discounts_woocommerce_active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
if ( in_array( 'woocommerce/woocommerce.php', $wc_taxonomy_discounts_woocommerce_active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $wc_taxonomy_discounts_woocommerce_active_plugins ) ) {

	/* Our own order class and the main classes */
	add_action( 'plugins_loaded', 'wctd_init', 1 );
	function wctd_init() {
		$GLOBALS['WC_Taxonomy_Discounts_Webdados'] = WC_Taxonomy_Discounts_Webdados();
	}

	/* Main class */
	function WC_Taxonomy_Discounts_Webdados() {
		return WC_Taxonomy_Discounts_Webdados::instance();
	}

} else {

	//if ( current_user_can( 'activate_plugins' ) ) { //Not available at the moment

		add_action( 'admin_notices', function() {
			?>
			<div id="message" class="error">
				<p><?php
					_e( '<strong>Taxonomy/Term and Role based Discounts for WooCommerce</strong> is enabled but not effective. It requires <strong>WooCommerce</strong> in order to work.',  'taxonomy-discounts-woocommerce' );
				?></p>
			</div>
			<?php
		} );

	//}

}


class WC_Taxonomy_Discounts_Webdados {
	private static $instance;

	public static function init() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Taxonomy_Discounts_Webdados();
		}
	}

	public static function instance() {
		if ( self::$instance == null ) {
			self::init();
		}
		return self::$instance;
	}

	public function __construct() {

		$this->version = '1.3';
		$this->discount_rule_meta_key = 'tdw_discount_rule';
		$this->discount_rules = false;
		$this->wc3 = true;
		$this->get_price_filter = 'woocommerce_product_get_price';
		$this->get_price_filter_priority = 10;
		//$this->enable_time = defined( 'WCTD_ENABLE_TIME' ) && WCTD_ENABLE_TIME;
		$this->enable_time = true; //Since 0.9

		//Load textdomain
		add_action( 'init', array( &$this, 'set_languages' ) );

		if ( is_admin() ) {

			if ( !defined( 'DOING_AJAX' ) ) {
				//Add menu item
				add_action( 'admin_menu', array( &$this, 'add_to_admin_menu' ), 99 );
				//Add CSS and JS
				add_action( 'admin_enqueue_scripts' , array( &$this, 'admin_css_js' ) );
			}
			//Ajax calls
			add_action( 'wp_ajax_tdw_form_add_choose_taxonomy' , array( &$this, 'ajax_form_add_choose_taxonomy' ) );
			add_action( 'wp_ajax_tdw_form_add_submit' , array( &$this, 'ajax_form_add_submit' ) );
			add_action( 'wp_ajax_tdw_form_edit_submit' , array( &$this, 'ajax_form_edit_submit' ) );
			add_action( 'wp_ajax_tdw_rules_table' , array( &$this, 'admin_page_rules_table_ajax' ) );
			add_action( 'wp_ajax_tdw_delete_rule' , array( &$this, 'ajax_delete_rule' ) );

		} else {

			add_action( 'plugins_loaded', array( &$this, 'init_public_filters' ) );

		}

	}

	/* Plugin URL */
	public static function plugin_url() {
		return plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) );
	}

	/**
	 * Loads the language domain
	 *
	 * @access public
	 * @return array
	 */
	public function set_languages() {
		//load_plugin_textdomain( 'taxonomy-discounts-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		load_plugin_textdomain( 'taxonomy-discounts-woocommerce' );
	}

	/* Init Public Filters */
	public function init_public_filters() {
		//WooCommerce 3.0?
		$this->wc3 = version_compare( WC_VERSION, '3.0', '>=' );
		if ( !$this->wc3 ) {
			$this->get_price_filter = 'woocommerce_get_price';
		}
		if ( defined( 'WCTD_GET_PRICE_FILTER_PRIO' ) && intval( WCTD_GET_PRICE_FILTER_PRIO ) >0 ) $this->get_price_filter_priority = intval( WCTD_GET_PRICE_FILTER_PRIO );
		//Maybe the price filters should come here...
		//Price - Product and listings page
		add_filter( $this->get_price_filter, array( &$this, 'on_get_price' ), $this->get_price_filter_priority, 2 );
		add_action( 'woocommerce_before_mini_cart', array( &$this, 'remove_price_filters' ) );
		add_action( 'woocommerce_after_mini_cart', array( &$this, 'add_price_filters' ) );
		//On sale?
		add_filter( 'woocommerce_product_is_on_sale', array( &$this, 'on_get_product_is_on_sale' ), 10, 2 );
		//Add the actions to trigger price adjustments
		add_action( 'woocommerce_cart_loaded_from_session', array( &$this, 'on_cart_loaded_from_session' ), 99, 1 );
		add_action( 'woocommerce_before_calculate_totals', array( &$this, 'remove_price_filters' ), 98, 1 );
		add_action( 'woocommerce_before_calculate_totals', array( &$this, 'on_calculate_totals' ), 99, 1 );
		add_action( 'woocommerce_after_calculate_totals', array( &$this, 'add_price_filters' ), 99 );
		//Hook into cart prices output
		add_filter( 'woocommerce_cart_item_price', array( &$this, 'on_display_cart_item_price_html' ), 99, 3 );
		//Variation prices html
		add_filter( 'woocommerce_available_variation', array( &$this, 'woocommerce_available_variation' ), 99, 3 );
		//Coupon is valid?
		add_filter( 'woocommerce_coupon_is_valid', array( &$this, 'woocommerce_fixed_cart_coupon_is_valid' ), 10, 2 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( &$this, 'woocommerce_coupon_get_discount_amount' ), 10, 5 );
		// BETA - Show discount information on the loop and product page
		if ( defined( 'WCTD_LOOP_DISC_INFO_ACTION' ) && trim( WCTD_LOOP_DISC_INFO_ACTION ) != '' && defined( 'WCTD_LOOP_DISC_INFO_PRIO' ) && intval( WCTD_LOOP_DISC_INFO_PRIO ) > 0 ) {
			add_action( WCTD_LOOP_DISC_INFO_ACTION, array( &$this, 'discount_information' ), WCTD_LOOP_DISC_INFO_PRIO );
		}
		if ( defined( 'WCTD_PERC_SALE_BADGE' ) && WCTD_PERC_SALE_BADGE ) {
			add_filter( 'woocommerce_sale_flash', array( &$this, 'woocommerce_sale_flash' ), 10, 3 );
		}
		// BETA - Show percentage discount on the sale badge
		if ( defined( 'WCTD_LOOP_DISC_INFO_ACTION' ) && trim( WCTD_LOOP_DISC_INFO_ACTION ) != '' && defined( 'WCTD_LOOP_DISC_INFO_PRIO' ) && intval( WCTD_LOOP_DISC_INFO_PRIO ) > 0 ) {
		}
		//KuantoKusta plugin integration
		add_filter( 'kuantokusta_product_node_default_current_price', array( &$this, 'kuantokusta_product_node_default_current_price' ), 10, 3 );
		add_filter( 'kuantokusta_product_node_variation_current_price', array( &$this, 'kuantokusta_product_node_variation_current_price' ), 10, 3 );
	}

	/* Add item to menu */
	public function add_to_admin_menu() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$slug = add_submenu_page(
				'woocommerce',
				__( 'Taxonomy/Term and Role based Discounts for WooCommerce', 'taxonomy-discounts-woocommerce' ),
				__( 'Taxonomy Discounts', 'taxonomy-discounts-woocommerce' ),
				'manage_woocommerce',
				'wc_taxonomy_discounts_webdados',
				array( &$this, 'admin_page' )
			);
		}
	}

	/* Admin page CSS and JS */
	public function admin_css_js( $hook ) {
		if ( $hook == 'woocommerce_page_wc_taxonomy_discounts_webdados' ) {
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
			wp_enqueue_style( 'tdw_admin_styles', self::plugin_url() . '/admin/styles.css', array( 'jquery-ui-style' ), $this->version );
			wp_enqueue_script( 'tdw_admin_js', self::plugin_url() . '/admin/functions.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version );
			$translation_array = array(
				'are_you_sure_delete_rule' => __( 'Are you sure you want to permanently delete this discount rule?', 'taxonomy-discounts-woocommerce' ),
			);
			wp_localize_script( 'tdw_admin_js', 'strings', $translation_array );
		}
	}

	/**
	 * Remove / restore WPML terms filters
	 *
	 * @access public
	 */
	public function remove_wpml_terms_filters() {
		if ( class_exists( 'SitePress' ) ) {
			global $sitepress;
			remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );
			remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ) );
			remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
		}
	}
	public function restore_wpml_terms_filters() {
		if ( class_exists( 'SitePress' ) ) {
			global $sitepress;
			add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 3 );
			add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ) );
			add_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10, 2 );
		}
	}

	/**
	 * Get the terms (with WPML or not)
	 *
	 * @access public
	 * @return array
	 */
	public function get_terms( $taxonomy, $args ) {
		self::remove_wpml_terms_filters();
		$terms = (array) get_terms( $taxonomy, $args );
		self::restore_wpml_terms_filters();
		return $terms;
	}
	public function get_term( $term_id, $taxonomy ) {
		self::remove_wpml_terms_filters();
		$term = get_term( $term_id, $taxonomy );
		self::restore_wpml_terms_filters();
		return $term;
	}

	/**
	 * Display or retrieve the HTML dropdown list of categories. (WPML compatible)
	 *
	 * @access public
	 * @return array
	 */
	public function wp_dropdown_categories( $args ) {
		$return = ! ( isset( $args['echo'] ) ? $args['echo'] : true );
		$args['echo'] = false;
		self::remove_wpml_terms_filters();
		$dropdow = wp_dropdown_categories( $args );
		self::restore_wpml_terms_filters();
		if ( $return ) {
			return $dropdow;
		} else {
			echo $dropdow;
		}
	}

	/* Display or retrieve the HTML dropdown list of roles */
	public function wp_dropdown_roles( $action, $selected ) {
		?>
		<select name="tdw-form-<?php echo $action; ?>-role" id="tdw-form-<?php echo $action; ?>-role">
			<option value="">- <?php _e( 'all users', 'taxonomy-discounts-woocommerce' ); ?> -</option>
			<option value="_logged_in_"<?php selected( $selected, '_logged_in_' ); ?>>- <?php _e( 'logged in users', 'taxonomy-discounts-woocommerce' ); ?> -</option>
			<?php wp_dropdown_roles( $selected ); ?>
		</select>
		<?php
	}

	/* Rules types names */
	public function get_rule_type_name( $type ) {
		switch ( $type ) {
			case 'percentage':
				return __( 'Percentage', 'taxonomy-discounts-woocommerce' );
				break;
			case 'x-for-y':
				return __( 'Buy x get y free', 'taxonomy-discounts-woocommerce' );
				break;
			default:
				return 'error';
				break;
		}
	}

	/* Get discount rules */
	public function get_discount_rules() {
		if ( ! $this->discount_rules ) { //Not on cache?
			$discount_rules = array();
			$taxonomy_objects = self::get_product_taxonomies();
			if ( count( $taxonomy_objects ) > 0 ) {
				global $wpdb;
				$taxonomies = array();
				foreach ( $taxonomy_objects as $tax => $taxonomy ) {
					$taxonomies[] = $tax;
				}
				$args = array(
					'hide_empty' => false,
					'meta_query' => array(
						array(
							'key' => $this->discount_rule_meta_key,
						)
					)
				);
				//$terms = get_terms( $taxonomies , $args );
				$terms = self::get_terms( $taxonomies , $args );
				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
						//We should not be using SQL
						$term_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE term_id = %d AND meta_key = %s", $term->term_id, $this->discount_rule_meta_key ) );
						foreach ( $term_meta as $term_meta_1 ) {
							$term_meta_value = maybe_unserialize( $term_meta_1->meta_value );
							$term_meta_value['active'] = isset( $term_meta_value['active'] ) ? $term_meta_value['active'] : false;
							$term_meta_value['disable_coupon'] = isset( $term_meta_value['disable_coupon'] ) ? $term_meta_value['disable_coupon'] : false;
							$term_meta_value['term_id'] = intval( $term->term_id );
							$term_meta_value['meta_id'] = intval( $term_meta_1->meta_id );
							if ( $term_meta_value['type']=='percentage' && ! isset( $term_meta_value['min-qtt'] ) ) $term_meta_value['min-qtt'] = 0; //Since 0.3
							if ( $term_meta_value['type']=='percentage' && ! isset( $term_meta_value['aggr-var'] ) ) $term_meta_value['aggr-var'] = 0; //Since 0.6
							if ( isset( $term_meta_value['priority'] ) ) {
								if ( !isset( $discount_rules[ $term_meta_value['priority'] ] ) ) {
									$discount_rules[ $term_meta_value['priority'] ] = array();
								}
								if( !isset( $discount_rules[ $term_meta_value['priority'] ][ $term->term_id ] ) ) {
									$discount_rules[ $term_meta_value['priority'] ][ $term->term_id ] = array();
								}
								$discount_rules[ $term_meta_value['priority'] ][ $term->term_id ][] = $term_meta_value;
							}
						}
					}
				}
			}
			ksort( $discount_rules );
			$this->discount_rules = $discount_rules; //Set on cache
		}
		return $this->discount_rules;
	}

	/* Rule applies based on user role? */
	public function valid_rule_user_role( $rule ) {
		$user_role = isset( $rule['user_role'] ) && trim( $rule['user_role'] ) != '' ? trim( $rule['user_role'] ) : '';
		switch ( $user_role ) {
			case '':
				return true;
				break;
			case '_logged_in_':
				return is_user_logged_in();
				break;
			default:
				if ( is_user_logged_in() ) {
					$user = wp_get_current_user();
					return in_array( $user_role, (array) $user->roles );
				} else {
					return false;
				}
				break;
		}
	}

	/* Rule applies based on date? */
	public function valid_rule_date( $rule ) {
		if ( $rule['active'] ) {
			$execute_rules = true;
			$now = current_time( 'timestamp' );
			$from = false;
			if ( isset( $rule['from'] ) && trim( $rule['from'] ) != '' ) {
				if ( $this->enable_time ) {
					if ( strlen( trim( $rule['from'] ) ) > 10 ) {
						$from = trim( $rule['from'] );
					} else {
						$from = substr( trim( $rule['from'] ), 0, 10 ).' 00:00:00';
					}
				} else {
					$from = substr( trim( $rule['from'] ), 0, 10 ).' 00:00:00';
				}
				$from = strtotime( $from );
			}
			$to = false;
			if ( isset( $rule['to'] ) && trim( $rule['to'] ) != '' ) {
				if ( $this->enable_time ) {
					if ( strlen( trim( $rule['to'] ) ) > 10 ) {
						$to = trim( $rule['to'] );
					} else {
						$to = substr( trim( $rule['to'] ), 0, 10 ).' 23:59:59';
					}
				} else {
					$to = substr( trim( $rule['to'] ), 0, 10 ).' 23:59:59';
				}
				$to = strtotime( $to );
			}
			if ( $from && $to && ! ( $now >= $from && $now <= $to ) ) {
				$execute_rules = false;
			} elseif ( $from && ! $to && ! ( $now >= $from ) ) {
				$execute_rules = false;
			} elseif ( $to && ! $from && ! ( $now <= $to ) ) {
				$execute_rules = false;
			}
		} else {
			$execute_rules = false;
		}
		return $execute_rules;
	}

	/* Product get price */
	public function on_get_price( $base_price, $_product, $force_calculation = false, $qty = 1 ) {
		if ( is_numeric( $base_price ) ) {
			$composite_ajax = did_action( 'wp_ajax_woocommerce_show_composited_product' ) | did_action( 'wp_ajax_nopriv_woocommerce_show_composited_product' ) | did_action( 'wc_ajax_woocommerce_show_composited_product' );
			if (
				is_product() //Product page
				||
				is_shop() //Shop page
				||
				is_product_category() //Category archive
				||
				is_product_tag() //Tag archive
				||
				is_product_taxonomy() //Custom taxonomy archive
				||
				$force_calculation //Forced
				||
				$composite_ajax //Ajax composite products
				||
				( isset( $GLOBALS['woocommerce_loop'] ) && isset( $GLOBALS['woocommerce_loop']['name'] ) && $GLOBALS['woocommerce_loop']['name'] == 'product' && isset( $GLOBALS['woocommerce_loop']['is_shortcode'] ) && $GLOBALS['woocommerce_loop']['is_shortcode'] == true ) //WooCommerce products shortcode
			) {
				$discount_rules = self::get_discount_rules();
				if ( count( $discount_rules ) ) {

					//$product_id = @isset( $_product->variation_id ) ? @$_product->variation_id : @$_product->id;
					$product_type = $this->wc3 ? $_product->get_type() : $_product->product_type;
					if ( $product_type == 'variation' ) {
						$product_id = $this->wc3 ? $_product->get_id() : $_product->variation_id;
						$product_id_base = $this->wc3 ? $_product->get_parent_id() : $_product->parent_id;
					} else {
						$product_id = $this->wc3 ? $_product->get_id() : $_product->id;
						$product_id_base = $product_id;
					}

					$discount_price = false;

					foreach( $discount_rules as $priority => $terms ) {
						foreach ( $terms as $term_id => $rules ) {
							foreach ( $rules as $rule ) {
								if ( self::valid_rule_user_role( $rule ) && self::valid_rule_date( $rule ) && isset( $rule['type'] ) && has_term( $term_id, $rule['taxonomy'], $product_id_base ) ) {
									switch( $rule['type'] ) {
										case 'percentage':
											if ( isset( $rule['value'] ) && is_numeric( $rule['value'] ) && $rule['value'] > 0 ) {
												if ( floatval( $rule['min-qtt'] ) == 0 || floatval( $rule['min-qtt'] ) == 1 || $qty >= floatval( $rule['min-qtt'] ) ) {
													$discount_price = $base_price - ( $base_price * ( floatval( $rule['value'] ) / 100 ) );
													return $discount_price;
												}
											}
											break;
										case 'x-for-y':
											//This is a quantity based rule. No way to get the price at this time. Only in cart... - Or can we? BETA
											if ( $qty >= floatval( $rule['x'] ) ) {
												$multiplier = floor( $qty / floatval( $rule['x'] ) );
												$qtt_discounted = floatval( $rule['y'] ) * $multiplier;
												$discount_price = ( ( $base_price * $qty ) - ( $base_price * $qtt_discounted ) ) / $qty;
												return $discount_price;
											}
											break;
										default:
											break;
									}
								}
							}
						}
					}

					return $base_price;

				} else {
					return $base_price;
				}
			} else {
				return $base_price;
			}
		} else {
			return $base_price;
		}
	}

	/* KuantoKusta plugin integration */
	public function kuantokusta_product_node_default_current_price( $price, $product, $product_type ) {
		return $this->on_get_price( $price, $product, true );
	}
	public function kuantokusta_product_node_variation_current_price( $price, $product, $variation ) {
		return $this->on_get_price( $price, $variation, true );
	}

	/* Adjust prices */
	public function remove_price_filters( $cart ) {
		if ( is_object( $cart ) ) {
			if ( ! $cart->is_empty() ) {
				remove_filter( $this->get_price_filter, array( &$this, 'on_get_price' ), 10, 2 );
			}
		}
	}
	public function add_price_filters() {
		add_filter( $this->get_price_filter, array( &$this, 'on_get_price' ), 10, 2 );
	}
	public function on_cart_loaded_from_session( $cart ) {
		if ( sizeof( $cart->cart_contents ) > 0 ) {
			foreach ( $cart->cart_contents as $cart_item_key => $values ) {
				if ( isset( $cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] ) ) {
					$base_price = WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts']['base_price'];
					if ( $this->wc3 ) {
						WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $base_price );
					} else {
						WC()->cart->cart_contents[ $cart_item_key ]['data']->price = $base_price;
					};
					unset( $cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] );
				}
			}
		}
		self::on_calculate_totals( $cart );
	}
	public function on_calculate_totals( $cart ) {
		if ( sizeof( $cart->cart_contents ) > 0 ) {
			$discount_rules = self::get_discount_rules();
			$variations = array();
			if ( count( $discount_rules ) ) {
				foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
					$_product = $cart->cart_contents[ $cart_item_key ]['data'];
					$product_type = $this->wc3 ? $_product->get_type() : $_product->product_type;
					switch ( $product_type ) {
						case 'variation':
							$product_id = $this->wc3 ? $_product->get_id() : $_product->variation_id;
							$product_id_base = $this->wc3 ? $_product->get_parent_id() : $_product->parent_id;
							$product = new WC_Product_Variation( $product_id ); //Why is this needed?
							break;
						default:
							$product_id = $this->wc3 ? $_product->get_id() : $_product->id;
							$product_id_base = $product_id;
							$product = new WC_Product( $product_id ); //Why is this needed?
							break;
					}

					$base_price = floatval(
						isset( WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] )
						?
						WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts']['base_price']
						:
						( $this->wc3 ? WC()->cart->cart_contents[ $cart_item_key ]['data']->get_price() : WC()->cart->cart_contents[ $cart_item_key ]['data']->price ) //Keep compatibility with other plugins that might mess with the cart value
					);
					$display_price = floatval(
						isset( WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] )
						?
						WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts']['display_price']
						: (
							get_option( 'woocommerce_tax_display_cart' ) == 'excl'
							?
							( $this->wc3 ? wc_get_price_excluding_tax( $_product ) : $_product->get_price_excluding_tax() )
							:
							( $this->wc3 ? wc_get_price_including_tax( $_product ) : $_product->get_price_including_tax() )
						)
					);
					$discount_price = $base_price;
					$discount_display_price = $display_price;
					foreach( $discount_rules as $priority => $terms ) {
						foreach ( $terms as $term_id => $rules ) {
							foreach ( $rules as $rule_key => $rule ) {
								if ( self::valid_rule_user_role( $rule ) && self::valid_rule_date( $rule ) && isset( $rule['type'] ) && has_term( $term_id, $rule['taxonomy'], $cart_item['product_id'] ) ) {
									switch( $rule['type'] ) {
									case 'percentage':
										if ( isset( $_product->variation_id ) && isset( $rule['aggr-var'] ) && $rule['aggr-var'] && isset( $rule['value'] ) && is_numeric( $rule['value'] ) && floatval( $rule['value'] ) > 0 ) {
											//Aggregate variations
											if ( isset( $variations[ $term_id ][ $rule_key ][ $product_id ] ) ) {
												$variations[ $term_id ][ $rule_key ][ $product_id ]['quantity'] += $cart_item['quantity'];
											} else {
												$variations[ $term_id ][ $rule_key ][ $product_id ] = array(
													'quantity' => $cart_item['quantity']
												);
											}
										} else {
											if ( isset( $rule['value'] ) && is_numeric( $rule['value'] ) && floatval( $rule['value'] ) > 0 && $cart_item['quantity'] >= floatval( $rule['min-qtt'] ) ) {
												$discount_price = $base_price - ( $base_price * ( floatval( $rule['value'] ) / 100 ) );
												$discount_display_price = $display_price - ( $display_price * ( floatval( $rule['value'] ) / 100 ) );
											}
										}
										break;
									case 'x-for-y':
										if ( isset( $rule['x'] ) && is_numeric( $rule['x'] ) && floatval( $rule['x'] ) > 0 && isset( $rule['y'] ) && is_numeric( $rule['y'] ) && floatval( $rule['y'] ) > 0 && floatval( $rule['x'] ) > floatval( $rule['y'] ) ) {
											if ( $cart_item['quantity'] >= floatval( $rule['x'] ) ) {
												$multiplier = floor( $cart_item['quantity'] / floatval( $rule['x'] ) );
												$qtt_discounted = floatval( $rule['y'] ) * $multiplier;
												$discount_price = ( ( $base_price * $cart_item['quantity'] ) - ( $base_price * $qtt_discounted ) ) / $cart_item['quantity'];
												$discount_display_price = ( ( $display_price * $cart_item['quantity'] ) - ( $display_price * $qtt_discounted ) ) / $cart_item['quantity'];
											}
										}
										break;
									default:
										break;
									}
								}
								if ( $discount_price < $base_price ) {
									$applied_rule=$rule;
									break;
								}
							}
							if ( $discount_price < $base_price ) break;
						}
						if ( $discount_price < $base_price ) break;
					}
					//Round with the defined WooCommerce decimals
					if ( $discount_price < $base_price ) {
						$discount_price = round( $discount_price, wc_get_price_decimals() );
						$discount_display_price = round( $discount_display_price, wc_get_price_decimals() );
						if ( $this->wc3 ) {
							WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $discount_price );
						} else {
							WC()->cart->cart_contents[ $cart_item_key ]['data']->price = $discount_price;
						};
						$discount_data = array(
							'applied_rule' => $applied_rule,
							'base_price' => $base_price,
							'display_price' => $display_price,
							'discount_price' => $discount_price,
							'discount_display_price' => $discount_display_price,
						);
						WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] = $discount_data;
					} else {
						if ( $this->wc3 ) {
							WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $base_price );
						} else {
							WC()->cart->cart_contents[ $cart_item_key ]['data']->price = $base_price;
						};
						unset( WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] );
					}

				}
				//Aggregated variations BETA - Not tested for recursion with other non-aggregated variations rules
				if ( count( $variations) > 0 ) {
					foreach( $discount_rules as $priority => $terms ) {
						foreach ( $terms as $term_id => $rules ) {
							foreach ( $rules as $rule_key => $rule ) {
								if ( self::valid_rule_user_role( $rule ) && self::valid_rule_date( $rule ) && isset( $rule['type'] ) && $rule['type']=='percentage' && isset( $rule['aggr-var'] ) && $rule['aggr-var'] && isset( $rule['value'] ) && is_numeric( $rule['value'] ) && floatval( $rule['value'] ) > 0 ) {
									if ( isset( $variations[ $term_id ][ $rule_key ] ) && is_array( $variations[ $term_id ][ $rule_key ] ) && count( $variations[ $term_id ][ $rule_key ] ) > 0 ) {
										foreach ( $variations[ $term_id ][ $rule_key ] as $temp_id_product => $temp_product ) {
											$rule_applied_for_product = false;
											if ( $temp_product['quantity'] >= floatval( $rule['min-qtt'] ) ) {
												//Apply discount to all instances of this variation
												foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
													$_product = $cart->cart_contents[ $cart_item_key ]['data'];
													if ( isset( $_product->variation_id ) ) { //Just double test if this is a variation
														$product_id = $this->wc3 ? $_product->get_id() : $_product->id;
														//$product = new WC_Product( $product_id ); //Why is this here?
														$base_price = (
															isset( WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] )
															?
															WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts']['base_price']
															:
															( $this->wc3 ? WC()->cart->cart_contents[ $cart_item_key ]['data']->get_price() : WC()->cart->cart_contents[ $cart_item_key ]['data']->price ) //Keep compatibility with other plugins that might mess with the cart value
														);
														$display_price = (
															isset( WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] )
															?
															WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts']['display_price']
															: (
																get_option( 'woocommerce_tax_display_cart' ) == 'excl'
																?
																( $this->wc3 ? wc_get_price_excluding_tax( $_product ) : $_product->get_price_excluding_tax() )
																:
																( $this->wc3 ? wc_get_price_including_tax( $_product ) : $_product->get_price_including_tax() )
															)
														);
														//Normal
														$discount_price = $base_price;
														$discount_display_price = $display_price;
														//Discount
														$discount_price = $base_price - ( $base_price * ( floatval( $rule['value'] ) / 100 ) );
														$discount_display_price = $display_price - ( $display_price * ( floatval( $rule['value'] ) / 100 ) );
														if ( $discount_price < $base_price ) {
															$rule_applied_for_product = true;
															$applied_rule = $rule;
															if ( $this->wc3 ) {
																WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $discount_price ); //Duplicates the discount on each cart load - NOP
															} else {
																WC()->cart->cart_contents[ $cart_item_key ]['data']->price = $discount_price;
															};
															$discount_data = array(
																'applied_rule' => $applied_rule,
																'base_price' => $base_price,
																'display_price' => $display_price,
																'discount_price' => $discount_price,
																'discount_display_price' => $discount_display_price,
															);
															WC()->cart->cart_contents[ $cart_item_key ]['taxonomy_discounts'] = $discount_data;
														}
													}
												}
												//Avoid recursion - If a rule was applied we'll just unset any other rules/variations for this same product
												if( $rule_applied_for_product ) {
													foreach ( $variations as $temp2_term_id => $temp2_rules ) {
														foreach ( $temp2_rules as $temp2_rulekey => $temp2_products ) {
															foreach ( $temp2_products as $temp2_id_product => $temp2_product ) {
																if ( $temp2_id_product==$temp_id_product ) {
																	unset( $variations[$temp2_term_id][$temp2_rulekey][$temp2_id_product] );
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				} //Aggregated variations
			}
		}
	}

	/* Hook into cart prices */
	public function on_display_cart_item_price_html( $html, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['taxonomy_discounts'] ) ) {
			$html = '<del>' . wc_price( $cart_item['taxonomy_discounts']['display_price'] ) . '</del><ins> ' . wc_price( $cart_item['taxonomy_discounts']['discount_display_price'] ) . '</ins>';
		}
		return $html;
	}

	/* Variation display prices - This is a big hack... */
	public function woocommerce_available_variation( $data, $product, $variation ) {
		if ( is_product() ) {
			$discount_rules = self::get_discount_rules();
			if ( count( $discount_rules ) ) {
				$product_id = $this->wc3 ? $product->get_id() : $product->id;
				$variation_id = $this->wc3 ? $variation->get_id() : $variation->id;
				$variation_price = floatval( $this->wc3 ? $variation->get_price() : $variation->price );
				$sale_price = $variation_price;
				foreach( $discount_rules as $priority => $terms ) {
					foreach ( $terms as $term_id => $rules ) {
						foreach ( $rules as $rule_key => $rule ) {
							if ( self::valid_rule_user_role( $rule) && self::valid_rule_date( $rule) && isset( $rule['type'] ) && has_term( $term_id, $rule['taxonomy'], $product_id ) ) {
								switch( $rule['type'] ) {
									case 'percentage':
										if ( isset( $rule['value'] ) && is_numeric( $rule['value'] ) && $rule['value'] > 0 ) {
											if ( floatval( $rule['min-qtt'] ) == 0 || floatval( $rule['min-qtt'] ) == 1 ) {
												$sale_price = $variation_price - ( $variation_price * ( floatval( $rule['value'] ) / 100 ) );
											}
										}
										break;
									case 'x-for-y':
										//This is a quantity based rule. No way to get the price at this time. Only in cart...
										break;
									default:
										break;
								}
							}
							if ( $sale_price<$variation_price ) break;
						}
						if ( $sale_price<$variation_price ) break;
					}
					if ( $sale_price<$variation_price ) break;
				}
			}
			if ( $sale_price < $variation_price ) {
				$show_variation_price = true;
				if ( $this->wc3 ) {
					$variation->set_price( $sale_price );
				} else {
					$variation->price = $sale_price;
				}
				//Change variation data
				$data['price'] = $sale_price;
				$data['price_html'] = $show_variation_price ? '<span class="price">' . $variation->get_price_html() . '</span>' : '';
			}
		}
		return $data;
	}

	/* Can a "fixed_cart" coupon be used on top of our discounts? */
	public function woocommerce_fixed_cart_coupon_is_valid( $valid, $coupon ) {
		if ( $valid ) {
			if ( $coupon->is_type( 'fixed_cart' ) ) {
				if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						if ( isset( $cart_item['taxonomy_discounts'] ) && $cart_item['taxonomy_discounts']['applied_rule']['disable_coupon'] ) {
							return false;
						}
					}
				}
			}
		}
		return $valid;
	}

	/* Coupon extra discount amount */
	function woocommerce_coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		//if ( !$coupon->is_type( 'fixed_cart' ) ) { //On fixed cart coupons we should allow the discount, or the final coupon
			if ( floatval( $discount ) > 0 ) {
				if ( isset( $cart_item['taxonomy_discounts'] ) && $cart_item['taxonomy_discounts']['applied_rule']['disable_coupon'] ) {
					$discount = 0;
				}
			}
		//}
		return $discount;
	}

	/* On sale? */
	public function on_get_product_is_on_sale( $is_on_sale, $product ) {
		if ( $is_on_sale ) {
			return $is_on_sale;
		}
		if ( $product->is_type( 'variable' ) ) {
			$is_on_sale = false;
			$prices = $product->get_variation_prices();
			if ( $prices['price'] !== $prices['regular_price'] ) { //This is not working
				$is_on_sale = true;
			} else {
				//Try testing with global product price - This may not be 100% accurate
				$global_price = floatval( ( $this->wc3 ? $product->get_price() : $product->price ) );
				$min_price = 0;
				if ( $global_price > 0 ) {
					foreach ( $prices['price'] as $temp_price ) {
						if ( floatval( $temp_price ) < $min_price || $min_price == 0 ) $min_price = floatval( $temp_price );
					}
					if  ( $global_price<$min_price ) $is_on_sale = true;
				}
			}
		} else {
			$discount_price = self::on_get_price( ( $this->wc3 ? $product->get_price() : $product->price ), $product, true );
			$regular_price = $product->get_regular_price();
			if ( empty( $regular_price ) || empty( $discount_price ) ) {
				return $is_on_sale;
			} else {
				$is_on_sale = $regular_price != $discount_price;
			}
		}
		return $is_on_sale;
	}

	/* BETA - Discount information */
	public function discount_information() {
		$info = '';
		$found = false;
		$discount_rules = self::get_discount_rules();
		if ( count( $discount_rules ) ) {
			global $product;
			$product_id = $this->wc3 ? $product->get_id() : $product->id;
			foreach( $discount_rules as $priority => $terms ) {
				foreach ( $terms as $term_id => $rules ) {
					foreach ( $rules as $rule_key => $rule ) {
						if ( self::valid_rule_user_role( $rule ) && self::valid_rule_date( $rule ) && isset( $rule['type'] ) && has_term( $term_id, $rule['taxonomy'], $product_id ) ) {
							switch( $rule['type'] ) {
								case 'percentage':
									if ( isset( $rule['value'] ) && is_numeric( $rule['value'] ) && $rule['value']>0 ) {
										$found = true;
										if ( floatval( $rule['min-qtt'] ) == 0 || floatval( $rule['min-qtt'] ) == 1 ) {
											$info = sprintf( __( '<strong>%d%%</strong> discount', 'taxonomy-discounts-woocommerce' ), intval( $rule['value'] ) );
										} else {
											$info = sprintf( __( 'From <strong>%d</strong> bought, <strong>%d%%</strong> discount', 'taxonomy-discounts-woocommerce' ), floatval( $rule['min-qtt'] ), intval( $rule['value'] ) );
										}
									}
									break;
								case 'x-for-y':
									if ( isset( $rule['x'] ) && is_numeric( $rule['x'] ) && floatval( $rule['x'] ) > 0 && isset( $rule['y'] ) && is_numeric( $rule['y'] ) && floatval( $rule['y'] ) > 0 && floatval( $rule['x'] ) > floatval( $rule['y'] ) ) {
										$found = true;
										$info = sprintf( __( 'For each <strong>%s</strong> bought <strong>%s</strong> is free', 'taxonomy-discounts-woocommerce' ), $rule['x'], $rule['y'] );
									}
									break;
								default:
									break;
							}
							if ( $info != '' ) {
								?>
								<div class="wctd_discount_information"><?php echo $info; ?></div>
								<?php
							}
							if ( $found ) break;
						}
					}
					if ( $found ) break;
				}
				if ( $found ) break;
			}
		}
	}

	/* BETA - Discount information */
	public function woocommerce_sale_flash( $html,  $post,  $product ) {
		$found = false;
		$discount_rules = self::get_discount_rules();
		if ( count( $discount_rules ) ) {
			global $product;
			$product_id = $this->wc3 ? $product->get_id() : $product->id;
			foreach( $discount_rules as $priority => $terms ) {
				foreach ( $terms as $term_id => $rules ) {
					foreach ( $rules as $rule_key => $rule ) {
						if ( self::valid_rule_user_role( $rule ) && self::valid_rule_date( $rule ) && isset( $rule['type'] ) && has_term( $term_id, $rule['taxonomy'], $product_id ) ) {
							switch( $rule['type'] ) {
								case 'percentage':
									if ( isset( $rule['value'] ) && is_numeric( $rule['value'] ) && $rule['value']>0 ) {
										$found = true;
										if ( floatval( $rule['min-qtt'] ) == 0 || floatval( $rule['min-qtt'] ) == 1 ) {
											$html = sprintf( '<span class="onsale">- %d&percnt;</span>', intval( $rule['value'] ) );
										}
										//We need to keep it short
										//else {
										//	$html = sprintf( __( 'From <strong>%d</strong> bought, <strong>%d%%</strong> discount', 'taxonomy-discounts-woocommerce' ), floatval( $rule['min-qtt'] ), intval( $rule['value'] ) );
										//}
									}
									break;
								//We need to keep it short
								//case 'x-for-y':
								//	if ( isset( $rule['x'] ) && is_numeric( $rule['x'] ) && floatval( $rule['x'] ) > 0 && isset( $rule['y'] ) && is_numeric( $rule['y'] ) && floatval( $rule['y'] ) > 0 && floatval( $rule['x'] ) > floatval( $rule['y'] ) ) {
								//		$found = true;
								//		$info = sprintf( __( 'For each <strong>%s</strong> bought <strong>%s</strong> is free', 'taxonomy-discounts-woocommerce' ), $rule['x'], $rule['y'] );
								//	}
								//	break;
								default:
									break;
							}
							if ( $found ) break;
						}
					}
					if ( $found ) break;
				}
				if ( $found ) break;
			}
		}
		return $html;
	}

	/* Admin page (should be in a separate file) */
	public function admin_page() {
		?>
		<div class="wrap woocommerce">

			<h1><?php _e( 'Taxonomy/Term and Role based Discounts for WooCommerce', 'taxonomy-discounts-woocommerce' ); ?> <?php echo $this->version; ?></h1>

			<p>
				<a href="https://wordpress.org/support/plugin/taxonomy-discounts-woocommerce/reviews/?filter=5" target="_blank">&starf;&starf;&starf;&starf;&starf; <?php _e( 'Rate this plugin on WordPress.org', 'taxonomy-discounts-woocommerce' ); ?></a>
				|
				<a href="https://profiles.wordpress.org/webdados/#content-plugins" target="_blank"><?php _e( 'Check out our other plugins', 'taxonomy-discounts-woocommerce' ); ?></a>
				|
				<a href="https://www.paypal.me/Wonderm00n" target="_blank"><?php _e( 'Buy me a beer', 'taxonomy-discounts-woocommerce' ); ?></a>
			</p>

			<div id="tdw-rules-table">
				<?php self::admin_page_rules_table(); ?>
			</div>

			<?php
			$taxonomy_objects = self::get_product_taxonomies();
			if ( count( $taxonomy_objects ) > 0 ) {
				?>
				<div id="tdw-form-add-div">
					<form id="tdw-form-add" method="post">
						<h2><?php _e( 'Add new Taxonomy/Term based discount rule:', 'taxonomy-discounts-woocommerce' ); ?></h2>

						<!-- Taxonomy and term -->
						<div id="tdw-form-add-div-1">
							<p class="tdw-float-left">
								<label for="tdw-form-add-taxonomy"><strong><?php _e( 'Taxonomy', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<select id="tdw-form-add-taxonomy" name="tdw-form-add-taxonomy">
									<option value="">- &nbsp;<?php _e( 'choose', 'taxonomy-discounts-woocommerce' ); ?>&nbsp; -</option>
									<?php
									foreach( $taxonomy_objects as $tax => $taxonomy ) {
										?>
										<option value="<?php echo $tax; ?>"><?php echo $taxonomy->labels->singular_name; ?> (<?php echo $tax; ?>)</option>
										<?php
									}
									?>
								</select>
							</p>
							<p id="tdw-form-add-choose-term" class="tdw-float-left">
							</p>
						</div>
						<div class="clear"></div>

						<!-- Discount configuration -->
						<div id="tdw-form-add-div-2" class="tdw-hidden">
							<p id="tdw-form-add-choose-role" class="tdw-float-left">
								<label for="tdw-form-add-type"><strong><?php _e( 'User role', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<?php echo $this->wp_dropdown_roles( 'add', '' ); ?>
							</p>
							<div class="clear"></div>
							<p id="tdw-form-add-choose-type" class="tdw-float-left">
								<label for="tdw-form-add-type"><strong><?php _e( 'Discount type', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<select id="tdw-form-add-type" name="tdw-form-add-type">
									<option value="">- &nbsp;<?php _e( 'choose', 'taxonomy-discounts-woocommerce' ); ?>&nbsp; -</option>
									<option value="percentage"><?php echo self::get_rule_type_name( 'percentage' ); ?></option>
									<option value="x-for-y"><?php echo self::get_rule_type_name( 'x-for-y' ); ?></option>
								</select>
							</p>
							<p id="tdw-form-add-choose-type-percentage" class="tdw-float-left tdw-hidden tdw-hide-empty-type">
								<label><strong><?php _e( 'Min. Qtt.', 'taxonomy-discounts-woocommerce' ); ?> / <?php _e( 'Discount', 'taxonomy-discounts-woocommerce' ); ?> / <?php _e( 'Aggregate variations', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<span><input type="number" id="tdw-form-add-percentage-min-qtt" name="tdw-form-add-percentage-min-qtt" min="0" step="1" placeholder="0"/></span>
								/
								<span><input type="number" id="tdw-form-add-percentage-value" name="tdw-form-add-percentage-value" min="1" max="99" step="1" placeholder="0" class="required"/>%</span>
								/
								<span>
									<select id="tdw-form-add-percentage-aggr-var" name="tdw-form-add-percentage-aggr-var">
										<option value="0"><?php _e( 'No', 'taxonomy-discounts-woocommerce' ); ?></option>
										<option value="1"><?php _e( 'Yes', 'taxonomy-discounts-woocommerce' ); ?></option>
									</select>
								</span>
							</p>
							<p id="tdw-form-add-choose-type-x-for-y" class="tdw-float-left tdw-hidden tdw-hide-empty-type">
								<label><strong><?php printf( __( 'For each <strong>%s</strong> bought <strong>%s</strong> is free', 'taxonomy-discounts-woocommerce' ), 'x', 'y' ); ?></strong>:</label>
								<br/>
								<span><input type="number" id="tdw-form-add-x-for-y-x" name="tdw-form-add-x-for-y-x" min="1" step="1" placeholder="x" class="required"/></span>
								/
								<span><input type="number" id="tdw-form-add-x-for-y-y" name="tdw-form-add-x-for-y-y" min="1" step="1" placeholder="y" class="required"/></span>
							</p>
						</div>
						<div class="clear"></div>

						<!-- Other settings -->
						<div id="tdw-form-add-div-3" class="tdw-hidden">
							<p id="tdw-form-add-choose-priority" class="tdw-float-left">
								<label for="tdw-form-add-priority"><strong><?php _e( 'Priority', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<input type="number" id="tdw-form-add-priority" name="tdw-form-add-priority" min="1" max="999" step="1" class="required"/>
							</p>
							<p id="tdw-form-add-choose-disable-coupon" class="tdw-float-left" title="<?php _e( 'Disable extra coupon discounts', 'taxonomy-discounts-woocommerce' ); ?>">
								<label for="tdw-form-add-disable-coupon"><strong><?php _e( 'Disable coupons', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<select id="tdw-form-add-disable-coupon" name="tdw-form-add-disable-coupon">
									<option value="1"><?php _e( 'Yes', 'taxonomy-discounts-woocommerce' ); ?></option>
									<option value="0"><?php _e( 'No', 'taxonomy-discounts-woocommerce' ); ?></option>
								</select>
							</p>
						</div>
						<div class="clear"></div>

						<!-- Active settings -->
						<div id="tdw-form-add-div-4" class="tdw-hidden">
							<p id="tdw-form-add-choose-active" class="tdw-float-left">
								<label for="tdw-form-add-priority"><strong><?php _e( 'Active', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<select id="tdw-form-add-active" name="tdw-form-add-active">
									<option value="1"><?php _e( 'Yes', 'taxonomy-discounts-woocommerce' ); ?></option>
									<option value="0"><?php _e( 'No', 'taxonomy-discounts-woocommerce' ); ?></option>
								</select>
							</p>
							<p id="tdw-form-add-choose-from" class="tdw-float-left">
								<label for="tdw-form-add-from"><strong><?php _e( 'From', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<input type="text" class="tdw-date-field" name="tdw-form-add-from" id="tdw-form-add-from" placeholder="<?php _e( 'yyyy-mm-dd', 'taxonomy-discounts-woocommerce' ); ?>" maxlength="10"/>
								<?php if ( $this->enable_time ) { ?><input type="text" class="tdw-time-field" name="tdw-form-add-from-time" id="tdw-form-add-from-time" placeholder="00:00:00" maxlength="8"/><?php } ?>
							</p>
							<p id="tdw-form-add-choose-to" class="tdw-float-left">
								<label for="tdw-form-add-to"><strong><?php _e( 'To', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
								<br/>
								<input type="text" class="tdw-date-field" name="tdw-form-add-to" id="tdw-form-add-to" placeholder="<?php _e( 'yyyy-mm-dd', 'taxonomy-discounts-woocommerce' ); ?>" maxlength="10"/>
								<?php if ( $this->enable_time ) { ?><input type="text" class="tdw-time-field" name="tdw-form-add-to-time" id="tdw-form-add-to-time" placeholder="23:59:59" maxlength="8"/><?php } ?>
							</p>
						</div>
						<div class="clear"></div>

						<!-- Submit -->
						<div>
							<p id="tdw-form-add-choose-submit" class="tdw-float-left tdw-hidden">
								<input type="submit" class="button-primary" value="<?php _e( 'Save', 'taxonomy-discounts-woocommerce' ) ?>"/>
							</p>
						</div>
						<div class="clear"></div>

					</form>
				</div>
			<?php } ?>

		</div>
		<?php
	}

	public function admin_page_rules_table_ajax() {
		self::admin_page_rules_table();
		wp_die();
	}
	public function admin_page_rules_table() {
		$discount_rules = self::get_discount_rules();
		$priority = 0;
		?>
		<h2><?php _e( 'Discount rules', 'taxonomy-discounts-woocommerce' ); ?></h2>
		<form id="tdw-form-edit" method="post">
			<table class="wp-list-table widefat striped">
				<thead>
					<tr>
						<th title="<?php _e( 'Priority', 'taxonomy-discounts-woocommerce' ); ?>"><?php _e( 'Pri.', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th><?php _e( 'Taxonomy', 'taxonomy-discounts-woocommerce' ); ?> / <?php _e( 'Term', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th><?php _e( 'User role', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th><?php _e( 'Type', 'taxonomy-discounts-woocommerce' ); ?> / <?php _e( 'Discount', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th title="<?php _e( 'Disable extra coupon discounts', 'taxonomy-discounts-woocommerce' ); ?>"><?php _e( 'Dis. coupons', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th><?php _e( 'Active', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th><?php _e( 'From', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th><?php _e( 'To', 'taxonomy-discounts-woocommerce' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( count( $discount_rules) > 0 ) {
						foreach( $discount_rules as $priority => $terms ) {
							foreach ( $terms as $term_id => $rules ) {
								foreach ( $rules as $rule ) {
									$taxonomy = get_taxonomy( $rule['taxonomy'] );
									$term = self::get_term( $term_id, $rule['taxonomy'] );
									?>
									<tr>
										<td><?php echo $priority; ?></td>
										<td>
											<?php echo $taxonomy->labels->singular_name.' / <strong>'.$term->name.'</strong>'; ?>
											<div class="row-actions">
												<span class="edit">
													<a href="#" data-meta-id="<?php echo $rule['meta_id']; ?>"><?php _e( 'Edit', 'taxonomy-discounts-woocommerce' ); ?></a>
													|
												</span>
												<span class="trash deleterule">
													<a href="#" data-meta-id="<?php echo $rule['meta_id']; ?>"><?php _e( 'Delete Permanently', 'taxonomy-discounts-woocommerce' ); ?></a>
												</span>
											</div>
										</td>
										<td>
											<?php
											$user_role = isset( $rule['user_role'] ) && trim( $rule['user_role'] ) != '' ? trim( $rule['user_role'] ) : '';
											switch( $user_role ) {
												case '':
													_e( 'All users', 'taxonomy-discounts-woocommerce' );
													break;
												case '_logged_in_':
													_e( 'Logged in users', 'taxonomy-discounts-woocommerce' );
													break;
												default:
													global $wp_roles;
													echo $wp_roles->roles[ $user_role ]['name'];
													break;
											}
											?>
										</td>
										<td>
											<?php echo self::get_rule_type_name( $rule['type'] ); ?>
											<br/>
											<?php
											switch( $rule['type'] ) {
												case 'percentage':
													printf( __( 'From <strong>%d</strong> bought, <strong>%d%%</strong> discount', 'taxonomy-discounts-woocommerce' ), floatval( $rule['min-qtt'] ), intval( $rule['value'] ) );
													if ( isset( $rule['aggr-var'] ) && $rule['aggr-var'] ) echo ', '.__( 'aggr. var.', 'taxonomy-discounts-woocommerce' );
													break;
												case 'x-for-y':
													printf( __( 'For each <strong>%s</strong> bought <strong>%s</strong> is free', 'taxonomy-discounts-woocommerce' ), intval( $rule['x'] ), intval( $rule['y'] ) );
													break;
											}
											?>
										</td>
										<td><?php echo $rule['disable_coupon'] ? '<span class="dashicons dashicons-yes"></span>' : '&nbsp;'; ?></td>
										<td><?php echo $rule['active'] ? '<span class="dashicons dashicons-yes"></span>' : '&nbsp;'; ?></td>
										<td><?php echo isset( $rule['from'] ) && trim( $rule['from'] ) != '' ? trim( $rule['from'] ) : '&infin;'; ?></td>
										<td><?php echo isset( $rule['to'] ) && trim( $rule['to'] ) != '' ? trim( $rule['to'] ) : '&infin;'; ?></td>
										<td></td>
									</tr>
									<tr id="tdw-edit-rule-<?php echo $rule['meta_id']; ?>" class="tdw-hidden tdw-edit-rule">
										<td>
											<input type="number" id="tdw-form-edit-priorit-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-priority" value="<?php echo $priority; ?>" min="1" max="999" step="1" class="required"/>
										</td>
										<td>
											<input type="hidden" id="tdw-form-edit-type-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-type" value="<?php echo $rule['type']; ?>"/>
											<input type="hidden" id="tdw-form-edit-term-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-term" value="<?php echo $term_id; ?>"/>
											<div class="row-actions">
												<span class="trash editcancel">
													<a href="#" data-meta-id="<?php echo $rule['meta_id']; ?>"><?php _e( 'Cancel', 'taxonomy-discounts-woocommerce' ); ?></a>
												</span>
											</div>
										</td>
										<td>
											<?php echo $this->wp_dropdown_roles( 'edit', $user_role ); ?>
										</td>
										<td><?php
										switch( $rule['type'] ) {
											case 'percentage':
												?>
												<span title="<?php echo esc_attr(__( 'Min. Qtt.', 'taxonomy-discounts-woocommerce' ) ); ?>"><input type="number" id="tdw-form-edit-percentage-min-qtt-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-percentage-min-qtt" min="0" step="1" placeholder="0" value="<?php echo intval( $rule['min-qtt'] ); ?>"/></span>
												/
												<span title="<?php echo esc_attr(__( 'Discount', 'taxonomy-discounts-woocommerce' ) ); ?>"><input type="number" id="tdw-form-edit-percentage-value-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-percentage-value" min="1" max="99" step="1" placeholder="0" value="<?php echo intval( $rule['value'] ); ?>" class="required"/>%</span>
												/
												<span>
													<select id="tdw-form-add-percentage-aggr-var" name="tdw-form-add-percentage-aggr-var">
														<option value="0"<?php if ( ! $rule['aggr-var'] ) echo ' selected="selected"'; ?>><?php _e( 'Do not aggr. var.', 'taxonomy-discounts-woocommerce' ); ?></option>
														<option value="1"<?php if ( $rule['aggr-var'] ) echo ' selected="selected"'; ?>><?php _e( 'Aggr. var.', 'taxonomy-discounts-woocommerce' ); ?></option>
													</select>
												</span>
												<?php
												break;
											case 'x-for-y':
												?>
												<span><input type="number" id="tdw-form-edit-x-for-y-x-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-x-for-y-x" min="1" step="1" placeholder="x" value="<?php echo intval( $rule['x'] ); ?>" class="required"/></span>
												/
												<span><input type="number" id="tdw-form-edit-x-for-y-y-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-x-for-y-y" min="1" step="1" placeholder="y" value="<?php echo intval( $rule['y'] ); ?>" class="required"/></span>
												<?php
												break;
										}
										?></td>
										<td>
											<select id="tdw-form-edit-disable-coupon-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-disable-coupon">
												<option value="1"<?php if ( $rule['disable_coupon'] ) echo ' selected="selected"'; ?>><?php _e( 'Yes', 'taxonomy-discounts-woocommerce' ); ?></option>
												<option value="0"<?php if ( ! $rule['disable_coupon'] ) echo ' selected="selected"'; ?>><?php _e( 'No', 'taxonomy-discounts-woocommerce' ); ?></option>
											</select>
										</td>
										<td>
											<select id="tdw-form-edit-active-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-active">
												<option value="1"<?php if ( $rule['active'] ) echo ' selected="selected"'; ?>><?php _e( 'Yes', 'taxonomy-discounts-woocommerce' ); ?></option>
												<option value="0"<?php if ( ! $rule['active'] ) echo ' selected="selected"'; ?>><?php _e( 'No', 'taxonomy-discounts-woocommerce' ); ?></option>
											</select>
										</td>
										<td>
											<input type="text" class="tdw-date-field" id="tdw-form-edit-from-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-from" placeholder="<?php _e( 'yyyy-mm-dd', 'taxonomy-discounts-woocommerce' ); ?>" value="<?php echo substr( trim ( $rule['from'] ), 0, 10 ); ?>" maxlength="10"/>
											<?php if ( $this->enable_time ) { ?><input type="text" class="tdw-time-field" name="tdw-form-edit-from-time" id="tdw-form-add-from-time-<?php echo $rule['meta_id']; ?>" placeholder="00:00:00" value="<?php echo substr( trim( $rule['from'] ), 11, 19 ); ?>" maxlength="8"/><?php } ?>
										</td>
										<td>
											<input type="text" class="tdw-date-field" id="tdw-form-edit-to-<?php echo $rule['meta_id']; ?>" name="tdw-form-edit-to" placeholder="<?php _e( 'yyyy-mm-dd', 'taxonomy-discounts-woocommerce' ); ?>" value="<?php echo substr( trim( $rule['to'] ), 0, 10 ); ?>" maxlength="10"/>
											<?php if ( $this->enable_time ) { ?><input type="text" class="tdw-time-field" name="tdw-form-edit-to-time" id="tdw-form-add-to-time-<?php echo $rule['meta_id']; ?>" placeholder="23:59:59" value="<?php echo substr( trim( $rule['to'] ), 11, 19 ); ?>" maxlength="8"/><?php } ?>
										</td>
										<td>
											<input type="submit" class="button-primary" value="<?php _e( 'Save', 'taxonomy-discounts-woocommerce' ) ?>"/>
										</td>
									</tr>
									<?php
								}
							}
						}
					} else {
						?>
						<tr>
							<td colspan="7" style="text-align: center;"><?php _e( 'No Taxonomy/Term based discounts created yet', 'taxonomy-discounts-woocommerce' ); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</form>
		<input type="hidden" id="tdw-last-priority" value="<?php echo intval( $priority ); ?>"/>
		<input type="hidden" id="tdw-edit-form-id" value=""/>
		<?php
	}

	public function ajax_form_add_choose_taxonomy() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			?>
				<label for="tdw-form-add-term"><strong><?php _e( 'Term', 'taxonomy-discounts-woocommerce' ); ?></strong>:</label>
				<br/>
				<?php
				$terms = self::wp_dropdown_categories( array(
					'show_option_none'	=>	'- &nbsp;'.__( 'choose', 'taxonomy-discounts-woocommerce' ).'&nbsp; -',
					'option_none_value'	=>	'',
					'show_count'		=>	true,
					'hide_empty'		=>	false,
					'hierarchical'		=>	true,
					'name'				=>	'tdw-form-add-term',
					'id'				=>	'tdw-form-add-term',
					'taxonomy'			=>	$_POST['taxonomy'],
					'hide_if_empty'		=>	true,
					'echo'				=>	false,
				) );
				echo trim( $terms ) != '' ? trim( $terms ) : __( 'No terms available', 'taxonomy-discounts-woocommerce' );
				?>

			<?php
			wp_die();
		}
	}

	public function ajax_form_add_submit() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			if ( isset( $_POST['tdw-form-add-term'] ) && intval( $_POST['tdw-form-add-term'] ) > 0 ) {
				$data = array();
				switch ( $_POST['tdw-form-add-type'] ) {
					case 'percentage':
						if ( isset( $_POST['tdw-form-add-percentage-value'] ) && intval( $_POST['tdw-form-add-percentage-value'] ) >= 1 && intval( $_POST['tdw-form-add-percentage-value'] ) <= 99 ) {
							$data['min-qtt'] = floatval( $_POST['tdw-form-add-percentage-min-qtt'] );
							$data['value'] = intval( $_POST['tdw-form-add-percentage-value'] );
							$data['aggr-var'] = ( isset( $_POST['tdw-form-add-percentage-aggr-var'] ) && intval( $_POST['tdw-form-add-percentage-aggr-var'] ) == 1 ? true : false );
						}
						break;
					case 'x-for-y':
						if ( isset( $_POST['tdw-form-add-x-for-y-x'] ) && intval( $_POST['tdw-form-add-x-for-y-x'] ) >= 1 && isset( $_POST['tdw-form-add-x-for-y-y'] ) && intval( $_POST['tdw-form-add-x-for-y-y'] ) >= 1 && intval( $_POST['tdw-form-add-x-for-y-x'] ) > intval( $_POST['tdw-form-add-x-for-y-y'] ) ) {
							$data['x'] = intval( $_POST['tdw-form-add-x-for-y-x'] );
							$data['y'] = intval( $_POST['tdw-form-add-x-for-y-y'] );
						}
						break;
					default:
						break;
				}
				if ( count( $data ) > 0 ) {
					$data['user_role'] = isset( $_POST['tdw-form-add-role'] ) && trim( $_POST['tdw-form-add-role'] ) != '' ? trim( $_POST['tdw-form-add-role'] ) : '';
					$data['priority'] = intval( $_POST['tdw-form-add-priority'] );
					$data['type']=$_POST['tdw-form-add-type'];
					$data['active'] = ( isset( $_POST['tdw-form-add-active'] ) && intval( $_POST['tdw-form-add-active'] )==1 ? true : false );
					$data['disable_coupon'] = ( isset( $_POST['tdw-form-add-disable-coupon'] ) && intval( $_POST['tdw-form-add-disable-coupon'] )==1 ? true : false );
					$data['taxonomy'] = $_POST['tdw-form-add-taxonomy'];
					$data['from'] = trim( $_POST['tdw-form-add-from'] ) != '' ? trim( $_POST['tdw-form-add-from'] ).' '.( $this->enable_time ? ( isset( $_POST['tdw-form-add-from-time'] ) && trim( $_POST['tdw-form-add-from-time'] ) != '' ? trim( $_POST['tdw-form-add-from-time'] ) : '00:00:00' ) : '00:00:00' ) : '';
					$data['to'] = trim( $_POST['tdw-form-add-to'] ) != '' ? trim( $_POST['tdw-form-add-to'] ).' '.( $this->enable_time ? ( isset( $_POST['tdw-form-add-to-time'] ) && trim( $_POST['tdw-form-add-to-time'] ) != '' ? trim( $_POST['tdw-form-add-to-time'] ) : '23:59:59' ) : '23:59:59' ) : '';
					if ( trim( $data['from'] ) != '' &&  trim( $data['to'] ) != '' && trim( $data['to'] )<trim( $data['from'] ) ) {
						$temp = $data['to'];
						$data['to'] = $data['from'];
						$data['from'] = $temp;
					}
					add_woocommerce_term_meta( intval( $_POST['tdw-form-add-term'] ), $this->discount_rule_meta_key, $data );
					do_action( 'tdw_rule_add', intval( $_POST['tdw-form-add-term'] ), $data['taxonomy'], $data );
					echo '1';
				}
			}
			wp_die();
		}
	}

	public function ajax_form_edit_submit() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			if ( isset( $_POST['tdw-form-edit-term'] ) && intval( $_POST['tdw-form-edit-term'] ) > 0 && isset( $_POST['meta_id'] ) && intval( $_POST['meta_id'] ) > 0 ) {
				//We should not be using SQL
				global $wpdb;
				if ( $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE meta_id = %d and meta_key = %s", intval( $_POST['meta_id'] ), $this->discount_rule_meta_key ) ) ) {
					$results = $results[0];
					$old_data=maybe_unserialize( $results->meta_value );
					$data = array();
					switch ( $_POST['tdw-form-edit-type'] ) {
						case 'percentage':
							if ( intval( $_POST['tdw-form-edit-percentage-value'] ) >= 1 && intval( $_POST['tdw-form-edit-percentage-value'] ) <= 99 ) {
								$data['min-qtt'] = floatval( $_POST['tdw-form-edit-percentage-min-qtt'] );
								$data['value'] = intval( $_POST['tdw-form-edit-percentage-value'] );
								$data['aggr-var'] = ( isset( $_POST['tdw-form-add-percentage-aggr-var'] ) && intval( $_POST['tdw-form-add-percentage-aggr-var'] ) == 1 ? true : false );
							}
							break;
						case 'x-for-y':
							if ( intval( $_POST['tdw-form-edit-x-for-y-x'] ) >= 1 && intval( $_POST['tdw-form-edit-x-for-y-y'] ) >= 1 && intval( $_POST['tdw-form-edit-x-for-y-x'] ) > intval( $_POST['tdw-form-edit-x-for-y-y'] ) ) {
								$data['x'] = intval( $_POST['tdw-form-edit-x-for-y-x'] );
								$data['y'] = intval( $_POST['tdw-form-edit-x-for-y-y'] );
							}
							break;
						default:
							break;
					}
					if ( count( $data) > 0 ) {
						$data['user_role'] = isset( $_POST['tdw-form-edit-role'] ) && trim( $_POST['tdw-form-edit-role'] ) != '' ? trim( $_POST['tdw-form-edit-role'] ) : '';
						$data['priority'] = intval( $_POST['tdw-form-edit-priority'] );
						$data['type']=$_POST['tdw-form-edit-type'];
						$data['active'] = ( isset( $_POST['tdw-form-edit-active'] ) && intval( $_POST['tdw-form-edit-active'] ) == 1 ? true : false );
						$data['disable_coupon'] = ( isset( $_POST['tdw-form-edit-disable-coupon'] ) && intval( $_POST['tdw-form-edit-disable-coupon'] ) == 1 ? true : false );
						$data['taxonomy'] = $old_data['taxonomy'];
						$data['from'] = trim( $_POST['tdw-form-edit-from'] ) != '' ? trim( $_POST['tdw-form-edit-from'] ).' '.( $this->enable_time ? ( isset( $_POST['tdw-form-edit-from-time'] ) && trim( $_POST['tdw-form-edit-from-time'] ) != '' ? trim( $_POST['tdw-form-edit-from-time'] ) : '00:00:00' ) : '00:00:00' ) : '';
						$data['to'] = trim( $_POST['tdw-form-edit-to'] ) != '' ? trim( $_POST['tdw-form-edit-to'] ).' '.( $this->enable_time ? ( isset( $_POST['tdw-form-edit-to-time'] ) && trim( $_POST['tdw-form-edit-to-time'] ) != '' ? trim( $_POST['tdw-form-edit-to-time'] ) : '23:59:59' ) : '23:59:59' ) : '';
						if ( trim( $data['from'] ) != '' &&  trim( $data['to'] ) != '' && trim( $data['to'] ) < trim( $data['from'] ) ) {
							$temp = $data['to'];
							$data['to'] = $data['from'];
							$data['from'] = $temp;
						}
						update_woocommerce_term_meta( intval( $_POST['tdw-form-edit-term'] ), $this->discount_rule_meta_key, $data, $old_data );
						do_action( 'tdw_rule_edit', intval( $_POST['tdw-form-edit-term'] ), $data['taxonomy'], $data, $old_data );
						echo '1';
					}
				}
			}
			wp_die();
		}
	}

	public function ajax_delete_rule() { /* Not CRUD ready */
		if ( current_user_can( 'manage_woocommerce' ) ) {
			if ( isset( $_POST['meta_id'] ) && intval( $_POST['meta_id'] ) > 0 ) {
				//Should use delete_woocommerce_term_meta - but we might have several rules for the same term_id, so we need to do it by meta_id
				global $wpdb;
				//We need to get details for the action fist...
				if ( $meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE meta_id = %d and meta_key = %s", intval( $_POST['meta_id'] ), $this->discount_rule_meta_key ) ) ) {
					$meta = $meta[0];
					$term_id = $meta->term_id;
					$data = maybe_unserialize( $meta->meta_value );
					//...and then delete it
					$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->termmeta WHERE meta_id = %d and meta_key = %s", intval( $_POST['meta_id'] ), $this->discount_rule_meta_key ) );
					do_action( 'tdw_rule_delete', $term_id, $data['taxonomy'], $data );
					echo '1';
				}
			}
			wp_die();
		}
	}

	public function get_product_taxonomies() {
		$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );
		return $taxonomy_objects;
	}


}

/* Helper - Get Product IDs on sale */
function wctd_get_product_ids_on_sale() {
	//The WooCommerce ones...
	$product_ids_on_sale = wc_get_product_ids_on_sale();
	//Ours
	$term_ids = array();
	$discount_rules = WC_Taxonomy_Discounts_Webdados()->get_discount_rules();
	foreach( $discount_rules as $priority => $terms ) {
		foreach ( $terms as $term_id => $rules ) {
			foreach ( $rules as $rule ) {
				if ( WC_Taxonomy_Discounts_Webdados()->valid_rule_user_role( $rule ) && WC_Taxonomy_Discounts_Webdados()->valid_rule_date( $rule ) && isset( $rule['type'] ) ) {
					$term_ids[] = $rule['term_id'];
				}
			}
		}
	}
	$term_ids = array_unique( $term_ids);
	if ( count( $term_ids ) > 0 ) {
		//This is not very WooCommerce 3.0 CRUD functions compatible...
		global $wpdb;
		$sql="
			SELECT post.ID, post.post_parent
			FROM `$wpdb->posts` AS post, `$wpdb->term_relationships` AS term_relationships
			WHERE
				post.post_type IN ( 'product', 'product_variation' )
				AND
				post.post_status = 'publish'
				AND
				term_relationships.term_taxonomy_id IN (".implode( ',', $term_ids).")
				AND
				post.ID=term_relationships.object_id
			GROUP BY post.ID;
		";
		$on_sale_posts = $wpdb->get_results( $sql );
		$product_ids_on_sale_1 = array_unique( array_map( 'absint', array_merge( wp_list_pluck( $on_sale_posts, 'ID' ), array_diff( wp_list_pluck( $on_sale_posts, 'post_parent' ), array( 0 ) ) ) ) );
		$product_ids_on_sale = array_unique( array_merge( $product_ids_on_sale, $product_ids_on_sale_1 ) );
	}
	return $product_ids_on_sale;
}

/* Helper - Get product (or variation) current/discounted price */
function wctd_get_product_current_price( $product, $qty = 1 ) {
	if ( is_int( $product ) ) {
		$product = wc_get_product( $product );
	}
	if ( is_object( $product ) ) {
		return WC_Taxonomy_Discounts_Webdados()->on_get_price( floatval( WC_Taxonomy_Discounts_Webdados()->wc3 ? $product->get_price() : $product->price ), $product, true, $qty );
	} else {
		return false;
	}
}

/* If you're reading this you must know what you're doing ;-) Greetings from sunny Portugal! */
