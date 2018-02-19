<?php
/**
 * Function Get All Custom Field Meta
 *
 * @package     TimJensen\ACF\Field_Group_Values
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 3.0+
 * @since       2.2.1
 */

declare( strict_types = 1 );

if ( ! function_exists( 'get_all_custom_field_meta' ) ) :

	/**
	 * Instantiates the Field_Group_Values class and returns the results.
	 *
	 * @link    https://www.timjensen.us/acf-get-field-alternative/
	 *
	 * @param int|string $post_id      Required. Post ID, 'option', or 'term_{id}'.
	 * @param array      $config       Required. ACF field group JSON reformatted as an array.
	 * @param array      $clone_fields Optional. ACF field group JSON arrays for all cloned fields/groups.
	 * @return array
	 */
	function get_all_custom_field_meta( $post_id, array $config, array $clone_fields = [] ): array {
		if ( empty( $config['fields'] ) ) {
			$corrected_config['fields'] = $config;
			$config                     = $corrected_config;
			trigger_error( 'As of version 2.0.0 the $config argument should include the field group key in addition to the array of fields. Pass $config instead of $config[\'fields\'].', E_USER_WARNING );
		}

		$field_group_values = new \TimJensen\ACF\Field_Group_Values( $post_id, $config, $clone_fields );

		return $field_group_values->get_results();
	}

endif;
