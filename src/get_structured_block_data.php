<?php
/**
 * Function Get Structured Block Data
 *
 * @package     TimJensen\ACF\Field_Group_Values
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 3.0+
 * @since       3.3.0
 */

declare( strict_types = 1 );

if ( ! function_exists( 'get_structured_block_data' ) ) :

	/**
	 * Instantiates the Field_Group_Values class and returns the results.
	 *
	 * @link    https://www.timjensen.us/acf-get-field-alternative/
	 *
	 * @param array $block_data     Block data passed by ACF to the block template or callback function.
	 * @param array $config         Required. ACF field group JSON reformatted as an array.
	 * @param array $clone_fields   Optional. ACF field group JSON arrays for all cloned fields/groups.
	 * @param bool  $include_labels Whether to include labels in the results.
	 * @return array
	 */
	function get_structured_block_data( $block_data, array $config, array $clone_fields = [], $include_labels = false ): array {
		if ( empty( $config['fields'] ) ) {
			$corrected_config['fields'] = $config;
			$config                     = $corrected_config;
			trigger_error( 'As of version 2.0.0 the $config argument should include the field group key in addition to the array of fields. Pass $config instead of $config[\'fields\'].', E_USER_WARNING );
		}

		if ( ! isset( $block_data['data'] ) || ! is_array( $block_data['data'] ) ) {
			$block_data['data'] = $block_data;
		}

		$field_group_values = new \TimJensen\ACF\Field_Group_Values( $block_data, $config, $clone_fields, $include_labels );

		return $field_group_values->get_results();
	}

endif;
