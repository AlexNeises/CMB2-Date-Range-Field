<?php
/**
 * Plugin Name: CMB2 Time Range Field
 * Plugin URI:  http://webdevstudios.com
 * Description: Adds a time range field to CMB2
 * Version:     0.1.1
 * Author:      WebDevStudios
 * Author URI:  http://webdevstudios.com
 * Donate link: http://webdevstudios.com
 * License:     GPLv2
 * Text Domain: wds-cmb2-date-range-field
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 WebDevStudios (email : contact@webdevstudios.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Main initiation class
 */
class WDS_CMB2_Time_Range_Field {

	const VERSION = '0.1.1';

	protected $url      = '';
	protected $path     = '';
	protected $basename = '';

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.1.0
	 * @return WDS_CMB2_Date_Range_Field A single instance of this class.
	 */
	public static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
		}
		$instance->hooks();

		return $instance;
	}

	/**
	 * Sets up our plugin
	 * @since  0.1.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Add hooks and filters
	 * @since 0.1.0
	 */
	public function hooks() {
		register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'cmb2_render_time_range', array( $this, 'render' ), 10, 5 );
		add_filter( 'cmb2_sanitize_time_range', array( $this, 'sanitize' ), 10, 2 );
	}

	/**
	 * Activate the plugin
	 * @since  0.1.0
	 */
	function _activate() {}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 * @since  0.1.0
	 */
	function _deactivate() {}

	/**
	 * Init hooks
	 * @since  0.1.0
	 * @return null
	 */
	public function init() {
		load_plugin_textdomain( 'wds-cmb2-time-range-field', false, dirname( $this->basename ) . '/languages/' );
	}

	/**
	 * Renders the date range field in CMB2.
	 *
	 * @param object $field         The CMB2 Field Object.
	 * @param mixed  $escaped_value The value after being escaped, by default, with sanitize_text_field.
	 */
	function render( $field, $escaped_value, $field_object_id, $field_object_type, $field_type ) {
		wp_enqueue_style( 'jquery-timepicker', $this->url . '/assets/jquery-timepicker/jquery.timepicker.css', array(), '0.4.0' );
		wp_register_script( 'datepair', $this->url . '/assets/jquery-timepicker/Datepair.js', array('jquery'), '0.4.0' );
		wp_register_script( 'jquery-datepair', $this->url . '/assets/jquery-timepicker/jquery.datepair.js', array('jquery'), '0.4.0' );
		wp_register_script( 'jquery-timepicker', $this->url . '/assets/jquery-timepicker/jquery.timepicker.js', array( 'jquery', 'datepair', 'jquery-datepair'), '0.4.0' );
		wp_enqueue_script( 'cmb2-timerange-picker', $this->url . '/assets/cmb2-timerange-picker.js', array( 'jquery-timepicker' ), self::VERSION, true );
		// CMB2_Types::parse_args allows arbitrary attributes to be added
		printf( '<p style="font-size: 0;" id="date' . $field_type->_id() . '" class="cmb2-element">' );
		$a = $field_type->parse_args( array(), 'input', array(
			'type'  => 'text',
			'class' => 'time start',
			'name'  => $field_type->_name() . '[start]',
			'id'    => $field_type->_id() . '[start]',
			'desc'  => $field_type->_desc( true ),
			'data-timerange' => json_encode( array(
				'id' => '#' . $field_type->_id() . '[start]'
			) ),
		) );
		printf( '<input%s value=%s />', $field_type->concat_attrs( $a, array( 'desc' ) ), json_encode( $escaped_value['start'] ) );


		$b = $field_type->parse_args( array(), 'input', array(
			'type'  => 'text',
			'class' => 'time end',
			'name'  => $field_type->_name() . '[end]',
			'id'    => $field_type->_id() . '[end]',
			'desc'  => $field_type->_desc( true ),
			'data-timerange' => json_encode( array(
				'id' => '#' . $field_type->_id() . '[end]'
			) ),
		) );

		printf( '<input%s value=%s />', $field_type->concat_attrs( $b, array( 'desc' ) ), json_encode( $escaped_value['end'] ) );
		printf( '</p>' );

		$c = $field_type->parse_args( array(), 'input', array(
			'type'  => 'checkbox',
			'class' => 'closed',
			'name'  => $field_type->_name() . '[closed]',
			'id'    => $field_type->_id() . '[closed]',
			'desc'  => $field_type->_desc( true )
		) );
		if ($escaped_value['closed'] === "on") {
			printf( '<br/><div class="cmb-th"><label for="' . $field_type->_id() . '[closed]">Closed?</label></div><input%s checked=checked />', $field_type->concat_attrs( $c, array( 'desc' ) ) );
		}
		else {
			printf( '<br/><div class="cmb-th"><label for="' . $field_type->_id() . '[closed]">Closed?</label></div><input%s />', $field_type->concat_attrs( $c, array( 'desc' ) ) );
		}
	}

	/**
	 * Convert the json array made by jquery plugin to a regular array to save to db.
	 *
	 * @param mixed $override_value A null value as a placeholder to return the modified value.
	 * @param mixed $value The non-sanitized value.
	 *
	 * @return array|mixed An array of the dates.
	 */
	function sanitize( $override_value, $value ) {

		$value = json_decode( $value, true );
		if ( is_array( $value ) ) {
			$value = array_map( 'sanitize_text_field', $value );
		} else {
			sanitize_text_field( $value );
		}
		return $value;

	}
}

/**
 * Grab the WDS_CMB2_Date_Range_Field object and return it.
 * Wrapper for WDS_CMB2_Date_Range_Field::get_instance()
 */
function wds_cmb2_time_range_field() {
	return WDS_CMB2_Time_Range_Field::get_instance();
}

// Kick it off
add_action( 'plugins_loaded', 'wds_cmb2_time_range_field' );
