<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

require_once dirname( __FILE__ ) . "/Tve_Dash_Font_Import_Manager_View.php";
require_once dirname( __FILE__ ) . "/Tve_Dash_Font_Import_Manager_Data.php";

if ( class_exists( 'Tve_Dash_Font_Import_Manager' ) ) {
	return;
}

class Tve_Dash_Font_Import_Manager {
	const OPTION_NAME = 'thrive_custom_font_pack';

	protected static $instance;

	protected $view;

	protected $messages = array();

	protected function __construct() {
		$this->view = new Tve_Dash_Font_Import_Manager_View( dirname( dirname( __FILE__ ) ) . "/views" );
	}

	public function mainPage() {
		if ( ! empty( $_POST['attachment_id'] ) ) {
			$this->handlePost();
		}

		$font_pack = get_option( static::OPTION_NAME, array() );

		if ( ! empty( $font_pack['css_file'] ) ) {
			wp_enqueue_style( 'thrive_custom_fonts_manager', $font_pack['css_file'] );
		}

		$data['font_pack'] = $font_pack;
		$data['messages']  = $this->messages;

		$this->view->render( 'main', $data );
	}

	protected function handlePost() {
		$handler = new Tve_Dash_Font_Import_Manager_Data();

		if ( ! empty( $_POST['attachment_id'] ) && $_POST['attachment_id'] != - 1 ) {
			$maybe_zip_file = get_attached_file( absint( $_POST['attachment_id'] ) );
			$maybe_zip_url  = wp_get_attachment_url( absint( $_POST['attachment_id'] ) );

			try {
				$new_font_pack                  = $handler->processZip( $maybe_zip_file, $maybe_zip_url );
				$new_font_pack['attachment_id'] = absint( $_POST['attachment_id'] );

				update_option( static::OPTION_NAME, $new_font_pack );

				$this->messages['success'][] = __( "Font pack saved !", 'thrive-dash' );

			} catch ( Exception $e ) {
				$this->messages['error'][] = $e->getMessage();
			}
		} else {

			try {
				$font_pack = get_option( static::OPTION_NAME, array() );
				if ( ! empty( $font_pack['attachment_id'] ) && is_file( $font_pack['zip_path'] ) ) {
					$handler->deleteDir( $font_pack['folder'] );
					delete_option( static::OPTION_NAME );
				}
				$this->messages['success'][] = __( "Font pack removed", 'thrive-dash' );
			} catch ( Exception $e ) {
				$this->messages['error'][] = $e;
			}
		}
	}

	public static function getInstance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new Tve_Dash_Font_Import_Manager();
		}

		return static::$instance;
	}

	public static function getImportedFonts() {
		$font_pack = get_option( static::OPTION_NAME, array() );
		if ( empty( $font_pack ) ) {
			return array();
		}

		$fonts = array();
		foreach ( $font_pack['font_families'] as $name ) {
			$fonts[] = array(
				'family'   => $name,
				'variants' => array(
					'regular',
				),
				'subsets'  => array(
					'latin'
				)
			);
		}

		return $fonts;
	}

	public static function getCssFile() {
		$font_pack = get_option( static::OPTION_NAME, array() );
		if ( empty( $font_pack ) ) {
			return null;
		}

		$css_file = str_replace( array( 'http:', 'https:' ), '', $font_pack['css_file'] );

		return $css_file;
	}

	/**
	 * @param $font string font-family
	 *
	 * @return bool
	 */
	public static function isImportedFont( $font ) {
		$font_pack = get_option( static::OPTION_NAME, array() );
		if ( empty( $font_pack ) ) {
			return false;
		}

		return in_array( $font, $font_pack['font_families'] );
	}
}

if ( ! class_exists( 'Thrive_Font_Import_Manager' ) ) {
	class Thrive_Font_Import_Manager extends Tve_Dash_Font_Import_Manager {
	}
}
