<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Audio_Video' ) ) {

	class YITH_WC_Audio_Video {

		protected static $_instance;


		public function __construct() {

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'wp_enqueue_scripts', array( $this, 'include_video_scripts' ), 20 );

			if ( is_admin() ) {
				YITH_Featured_Audio_Video_Admin();
			} else {

				/**load zoom magnifier module*/
				if ( ywcfav_check_is_zoom_magnifier_is_active() && ! ywcfav_check_is_product_is_exclude_from_zoom() ) {
					YITH_Featured_Audio_Video_Zoom_Magnifier();
				} else {
					YITH_Featured_Audio_Video_Frontend();
				}
			}

		}

		/** return single instance of class
		 * @author YITHEMES
		 * @since 2.0.0
		 * @return YITH_WC_Audio_Video
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		public function include_video_scripts() {

			if ( is_product() ) {

				wp_enqueue_style( 'ywcfav_style', YWCFAV_ASSETS_URL . 'css/ywcfav_frontend.css', array(), YWCFAV_VERSION );
				wp_enqueue_script( 'vimeo-api', YWCFAV_ASSETS_URL . 'js/lib/vimeo_player.js', array(), YWCFAV_VERSION, true );
				wp_enqueue_script( 'youtube-api', YWCFAV_ASSETS_URL . 'js/lib/youtube_api.js', array( 'jquery' ), YWCFAV_VERSION, true );

				wp_register_script( 'ywcfav_video', YWCFAV_ASSETS_URL . 'js/ywcfav_video.js', array(
						'jquery',
					'youtube-api',
					'vimeo-api'
				), YWCFAV_VERSION, true );

				$script_args = array(
					'product_gallery_trigger_class'   => '.' . ywcfav_get_product_gallery_trigger()
				);

				wp_localize_script( 'ywcfav_video', 'ywcfav_args', $script_args );
			}
		}

	}

}


if ( ! function_exists( 'YITH_Featured_Video' ) ) {
	function YITH_Featured_Video() {
		return YITH_WC_Audio_Video::get_instance();
	}
}