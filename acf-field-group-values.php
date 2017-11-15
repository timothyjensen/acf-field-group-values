<?php
/**
 * Plugin Name:     ACF Field Group Values
 * Plugin URI:      https://github.com/timothyjensen/acf-field-group-values
 * Description:     Retrieve all post meta and option values for the specified ACF field group.
 * Author:          Tim Jensen
 * Author URI:      https://www.timjensen.us
 * Text Domain:     acf-field-group-values
 * Domain Path:     /languages
 * Version:         2.0.0
 *
 * @package         ACF_Field_Group_Values
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require __DIR__ . '/src/Field_Group_Values.php';

if ( ! function_exists( 'get_all_custom_field_meta' ) ) :

	/**
	 * Instantiates the Field_Group_Values class and returns the results.
	 *
	 * @link    https://www.timjensen.us/acf-get-field-alternative/
	 *
	 * @param int|string $post_id      Required. Post ID, or 'options' when retrieving option values.
	 * @param array      $config       Required. ACF field group JSON reformatted as an array.
	 * @param array      $clone_fields Optional. ACF field group JSON arrays for all cloned fields/groups.
	 * @return array
	 */
	function get_all_custom_field_meta( $post_id, array $config, array $clone_fields = [] ) {
		$field_group_values = new \TimJensen\ACF\Field_Group_Values( $post_id, $config, $clone_fields );

		return $field_group_values->get_all_field_group_values();
	}

endif;
