<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-automator
 */

namespace Thrive\Automator\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

if ( ! class_exists( 'Tar_Suite', false ) ) {
	require_once __DIR__ . '/class-tar-suite-action.php';
}


class Ta_Suite extends Tar_Suite {
	public static function get_id() {
		return 'suite_ta_action';
	}

	public static function get_name() {
		return 'Thrive Apprentice';
	}

	public static function get_image() {
		return 'tap-apprentice-logo';
	}
}
