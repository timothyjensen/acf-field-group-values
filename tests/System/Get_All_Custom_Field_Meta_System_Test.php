<?php
/**
 * Class Get_All_Custom_Field_Meta_System_Test
 *
 * @package     TimJensen\ACF\Tests
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 2.0+
 * @link        https://www.timjensen.us
 * @since       2.1.2
 */

namespace TimJensen\ACF\Tests;

use TimJensen\ACF\Field_Group_Values;

/**
 * Class Get_All_Custom_Field_Meta_System_Test
 *
 * @package TimJensen\ACF\Tests
 */
class Get_All_Custom_Field_Meta_System_Test extends TestCase {

	/**
	 * ID of the test post.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * ID of the test term.
	 *
	 * @var int
	 */
	public $term_id;

	/**
	 * Test setup
	 */
	public function setUp() {
		parent::setUp();

		$wp_tests_lib  = new \WP_UnitTestCase();
		$this->post_id = $wp_tests_lib->factory->post->create();
		$this->term_id = $wp_tests_lib->factory->term->create();

		$data_type_callbacks = [
			'post_meta' => 'update_post_meta',
			'term_meta' => 'update_term_meta',
			'options'   => 'update_option',
		];

		array_walk( $data_type_callbacks, function ( $callback, $data_type ) {
			$data = \get_test_data( $data_type );

			foreach ( $data as $meta_key => $meta_value ) {

				switch ( $data_type ) :
					case 'options':
						$callback_args = [
							"options_{$meta_key}",
							$meta_value,
						];
						break;
					case 'post_meta':
						$callback_args = [
							$this->post_id,
							$meta_key,
							$meta_value,
						];
						break;
					case 'term_meta':
						$callback_args = [
							$this->term_id,
							$meta_key,
							$meta_value,
						];
				endswitch;

				call_user_func_array( $callback, $callback_args );
			}
		} );
	}

	/**
	 * Test that the old config format (pre 2.0) throws a PHP warning but still returns correct values.
	 */
	public function test_old_config_format_returns_correct_values() {
		$this->expectException( \PHPUnit\Framework\Error\Warning::class );

		$this->assertEquals(
			get_all_custom_field_meta( $this->post_id, $this->config, $this->clone_fields ),
			get_all_custom_field_meta( $this->post_id, $this->config['fields'], $this->clone_fields )
		);
	}

	/**
	 * Test that the convenience function get_all_custom_field_meta returns the same result as calling
	 * the class directly.
	 */
	public function test_function_and_class_return_same_result() {
		$field_group_values = new Field_Group_Values( $this->post_id, $this->config, $this->clone_fields );

		$this->assertEquals(
			$field_group_values->get_results(),
			get_all_custom_field_meta( $this->post_id, $this->config, $this->clone_fields )
		);
	}

	public function test_post_meta_field_group_values() {
		$field_group_values = get_all_custom_field_meta( $this->post_id, $this->config, $this->clone_fields );

		$post_meta_test_data = \get_test_data( 'post_meta' );

		array_walk( $post_meta_test_data, function ( $meta_value, $meta_key ) use ( $field_group_values ) {
			if ( ! $this->should_compare_with_results_array( $meta_value ) ) {
				return;
			}

			$results_value = $this->lookup_value_in_results_array( $field_group_values, $meta_key );
			$this->assertEquals( $meta_value, $results_value );
		} );
	}

	public function test_term_meta_field_group_values() {
		$field_group_values = get_all_custom_field_meta( "term_{$this->term_id}", $this->config, $this->clone_fields );

		$post_meta_test_data = \get_test_data( 'term_meta' );

		array_walk( $post_meta_test_data, function ( $meta_value, $meta_key ) use ( $field_group_values ) {
			if ( ! $this->should_compare_with_results_array( $meta_value ) ) {
				return;
			}

			$results_value = $this->lookup_value_in_results_array( $field_group_values, $meta_key );
			$this->assertEquals( $meta_value, $results_value );
		} );
	}

	public function test_option_field_group_values() {
		$field_group_values = get_all_custom_field_meta( 'option', $this->config, $this->clone_fields );

		$post_meta_test_data = \get_test_data( 'options' );

		array_walk( $post_meta_test_data, function ( $meta_value, $meta_key ) use ( $field_group_values ) {
			if ( ! $this->should_compare_with_results_array( $meta_value ) ) {
				return;
			}

			$results_value = $this->lookup_value_in_results_array( $field_group_values, $meta_key );
			$this->assertEquals( $meta_value, $results_value );
		} );
	}

	protected function should_compare_with_results_array( $meta_value ) {
		return is_string( $meta_value ) && ! empty( $meta_value ) && ! is_numeric( $meta_value );
	}

	protected function lookup_value_in_results_array( $field_group_values, $meta_key ) {
		$array_dimensions = explode( '_', $meta_key );

		$result = $field_group_values;
		foreach ( $array_dimensions as $partial_key ) {
			// Account for clone fields.
			if ( isset( $result[ $partial_key ] ) ) {
				$result = $result[ $partial_key ];
			} else {
				$result = $result['clone'][ $partial_key ];
			}
		}

		return $result;
	}
}
