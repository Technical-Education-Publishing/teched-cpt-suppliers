<?php
/**
 * Provides helper functions.
 *
 * @since	  1.0.0
 *
 * @package	TechEd_CPT_Suppliers
 * @subpackage TechEd_CPT_Suppliers/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since		1.0.0
 *
 * @return		TechEd_CPT_Suppliers
 */
function TECHEDCPTSUPPLIERS() {
	return TechEd_CPT_Suppliers::instance();
}

/**
 * Returns a list of States with the shorthand as the Key
 *
 * @since	1.0.0
 * @return  array  States
 */
function teched_suppliers_get_state_list() {

	return array(
		'AL' => __( 'Alabama', 'teched-cpt-suppliers' ),
		'AK' => __( 'Alaska', 'teched-cpt-suppliers' ),
		'AZ' => __( 'Arizona', 'teched-cpt-suppliers' ),
		'AR' => __( 'Arkansas', 'teched-cpt-suppliers' ),
		'CA' => __( 'California', 'teched-cpt-suppliers' ),
		'CO' => __( 'Colorado', 'teched-cpt-suppliers' ),
		'CT' => __( 'Connecticut', 'teched-cpt-suppliers' ),
		'DE' => __( 'Delaware', 'teched-cpt-suppliers' ),
		'FL' => __( 'Florida', 'teched-cpt-suppliers' ),
		'GA' => __( 'Georgia', 'teched-cpt-suppliers' ),
		'HI' => __( 'Hawaii', 'teched-cpt-suppliers' ),
		'ID' => __( 'Idaho', 'teched-cpt-suppliers' ),
		'IL' => __( 'Illinois', 'teched-cpt-suppliers' ),
		'IN' => __( 'Indiana', 'teched-cpt-suppliers' ),
		'IA' => __( 'Iowa', 'teched-cpt-suppliers' ),
		'KS' => __( 'Kansas', 'teched-cpt-suppliers' ),
		'KY' => __( 'Kentucky', 'teched-cpt-suppliers' ),
		'LA' => __( 'Louisiana', 'teched-cpt-suppliers' ),
		'ME' => __( 'Maine', 'teched-cpt-suppliers' ),
		'MD' => __( 'Maryland', 'teched-cpt-suppliers' ),
		'MA' => __( 'Massachusetts', 'teched-cpt-suppliers' ),
		'MI' => __( 'Michigan', 'teched-cpt-suppliers' ),
		'MN' => __( 'Minnesota', 'teched-cpt-suppliers' ),
		'MS' => __( 'Mississippi', 'teched-cpt-suppliers' ),
		'MO' => __( 'Missouri', 'teched-cpt-suppliers' ),
		'MT' => __( 'Montana', 'teched-cpt-suppliers' ),
		'NE' => __( 'Nebraska', 'teched-cpt-suppliers' ),
		'NV' => __( 'Nevada', 'teched-cpt-suppliers' ),
		'NH' => __( 'New Hampshire', 'teched-cpt-suppliers' ),
		'NJ' => __( 'New Jersey', 'teched-cpt-suppliers' ),
		'NM' => __( 'New Mexico', 'teched-cpt-suppliers' ),
		'NY' => __( 'New York', 'teched-cpt-suppliers' ),
		'NC' => __( 'North Carolina', 'teched-cpt-suppliers' ),
		'ND' => __( 'North Dakota', 'teched-cpt-suppliers' ),
		'OH' => __( 'Ohio', 'teched-cpt-suppliers' ),
		'OK' => __( 'Oklahoma', 'teched-cpt-suppliers' ),
		'OR' => __( 'Oregon', 'teched-cpt-suppliers' ),
		'PA' => __( 'Pennsylvania', 'teched-cpt-suppliers' ),
		'RI' => __( 'Rhode Island', 'teched-cpt-suppliers' ),
		'SC' => __( 'South Carolina', 'teched-cpt-suppliers' ),
		'SD' => __( 'South Dakota', 'teched-cpt-suppliers' ),
		'TN' => __( 'Tennessee', 'teched-cpt-suppliers' ),
		'TX' => __( 'Texas', 'teched-cpt-suppliers' ),
		'UT' => __( 'Utah', 'teched-cpt-suppliers' ),
		'VT' => __( 'Vermont', 'teched-cpt-suppliers' ),
		'VA' => __( 'Virginia', 'teched-cpt-suppliers' ),
		'WA' => __( 'Washington', 'teched-cpt-suppliers' ),
		'DC' => __( 'Washington D.C.', 'teched-cpt-suppliers' ),
		'WV' => __( 'West Virginia', 'teched-cpt-suppliers' ),
		'WI' => __( 'Wisconsin', 'teched-cpt-suppliers' ),
		'WY' => __( 'Wyoming', 'teched-cpt-suppliers' ),
		'AA' => __( 'Armed Forces Americas', 'teched-cpt-suppliers' ),
		'AE' => __( 'Armed Forces Europe', 'teched-cpt-suppliers' ),
		'AP' => __( 'Armed Forces Pacific', 'teched-cpt-suppliers' ),
		'AS' => __( 'American Samoa', 'teched-cpt-suppliers' ),
		'VI' => __( 'Virgin Islands', 'teched-cpt-suppliers' ),
		'PR' => __( 'Puerto Rico', 'teched-cpt-suppliers' ),
		'PW' => __( 'Palau', 'teched-cpt-suppliers' ),
		'ON' => __( 'Ontario', 'teched-cpt-suppliers' ),
	);

}

/**
 * Quick access to plugin field helpers.
 *
 * @since 1.0.0
 *
 * @return RBM_FieldHelpers
 */
function teched_suppliers_fieldhelpers() {
	return TECHEDCPTSUPPLIERS()->field_helpers;
}

/**
 * Initializes a field group for automatic saving.
 *
 * @since 1.0.0
 *
 * @param $group
 */
function teched_suppliers_init_field_group( $group ) {
	teched_suppliers_fieldhelpers()->fields->save->initialize_fields( $group );
}

/**
 * Gets a meta field helpers field.
 *
 * @since 1.0.0
 *
 * @param string $name Field name.
 * @param string|int $post_ID Optional post ID.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function teched_suppliers_get_field( $name, $post_ID = false, $default = '', $args = array() ) {
    $value = teched_suppliers_fieldhelpers()->fields->get_meta_field( $name, $post_ID, $args );
    return $value !== false ? $value : $default;
}

/**
 * Gets a option field helpers field.
 *
 * @since 1.0.0
 *
 * @param string $name Field name.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function teched_suppliers_get_option_field( $name, $default = '', $args = array() ) {
	$value = teched_suppliers_fieldhelpers()->fields->get_option_field( $name, $args );
	return $value !== false ? $value : $default;
}

/**
 * Outputs a text field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_text( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_text( $args['name'], $args );
}

/**
 * Outputs a password field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_password( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_password( $args['name'], $args );
}

/**
 * Outputs a textarea field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_textarea( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_textarea( $args['name'], $args );
}

/**
 * Outputs a checkbox field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_checkbox( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_checkbox( $args['name'], $args );
}

/**
 * Outputs a toggle field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_toggle( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_toggle( $args['name'], $args );
}

/**
 * Outputs a radio field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_radio( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_radio( $args['name'], $args );
}

/**
 * Outputs a select field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_select( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_select( $args['name'], $args );
}

/**
 * Outputs a number field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_number( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_number( $args['name'], $args );
}

/**
 * Outputs an image field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_media( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_media( $args['name'], $args );
}

/**
 * Outputs a datepicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_datepicker( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_datepicker( $args['name'], $args );
}

/**
 * Outputs a timepicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_timepicker( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_timepicker( $args['name'], $args );
}

/**
 * Outputs a datetimepicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_datetimepicker( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_datetimepicker( $args['name'], $args );
}

/**
 * Outputs a colorpicker field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_colorpicker( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_colorpicker( $args['name'], $args );
}

/**
 * Outputs a list field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_list( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_list( $args['name'], $args );
}

/**
 * Outputs a hidden field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_hidden( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_hidden( $args['name'], $args );
}

/**
 * Outputs a table field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_table( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_table( $args['name'], $args );
}

/**
 * Outputs a HTML field.
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_do_field_html( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_html( $args['name'], $args );
}

/**
 * Outputs a repeater field.
 *
 * @since 1.0.0
 *
 * @param mixed $values
 */
function teched_suppliers_do_field_repeater( $args = array() ) {
	teched_suppliers_fieldhelpers()->fields->do_field_repeater( $args['name'], $args );
}

/**
 * Outputs a String if a Callback Function does not exist for an Options Page Field
 *
 * @since 1.0.0
 *
 * @param array $args
 */
function teched_suppliers_missing_callback( $args ) {
	
	printf( 
		_x( 'A callback function called "teched_suppliers_do_field_%s" does not exist.', '%s is the Field Type', 'teched-cpt-suppliers' ),
		$args['type']
	);
		
}

if ( ! function_exists( 'teched_media_file_exists' ) ) {

	/**
	 * Checks if a Media File exists in the database and returns the Attachment ID
	 *
	 * @param   string  $filename  File Name
	 *
	 * @since	1.0.0
	 * @return  integer            Attachment ID
	 */
	function teched_media_file_exists( $filename ){

		global $wpdb;
		$query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%$filename'";

		return ( $wpdb->get_var( $query ) ) ? $wpdb->get_var( $query ) : false;

	}

}