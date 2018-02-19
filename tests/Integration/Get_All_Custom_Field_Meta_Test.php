<?php
/**
 * Class Get_All_Custom_Field_Meta_Test
 *
 * @package     TimJensen\ACF\Tests
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 2.0+
 * @link        https://www.timjensen.us
 * @since       2.1.2
 */

namespace TimJensen\ACF\Tests;

use Brain\Monkey;
use TimJensen\ACF\Field_Group_Values;

/**
 * Class Get_All_Custom_Field_Meta_Test
 *
 * @package TimJensen\ACF\Tests
 */
class Get_All_Custom_Field_Meta_Test extends TestCase {

	/**
	 * Holds an instance of \TimJensen\ACF\Field_Group_Values.
	 *
	 * @var \TimJensen\ACF\Field_Group_Values
	 */
	protected $instance;

	/**
	 * Test setup
	 */
	public function setUp() {
		Monkey\setUp();
		parent::setUp();

		Monkey\Functions\stubs( [
			'get_post_meta' => function ( $post_id = null, $key, $single = false ) {
				$test_data = \get_test_data( 'post_meta' );

				return empty( $test_data[ $key ] ) ? '' : $test_data[ $key ];
			},
			'get_option'    => function ( $key ) {
				$test_data = \get_test_data( 'options' );

				$key = str_replace( 'options_', '', $key );

				return empty( $test_data[ $key ] ) ? '' : $test_data[ $key ];
			},
			'get_term_meta' => function ( $post_id = null, $key, $single = false ) {
				$test_data = \get_test_data( 'term_meta' );

				return empty( $test_data[ $key ] ) ? '' : $test_data[ $key ];
			},
		] );

		$this->instance = new Field_Group_Values( $this->post_id, $this->config, $this->clone_fields );
	}

	/**
	 * Test tear down.
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
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
		$this->assertEquals(
			$this->instance->get_results(),
			get_all_custom_field_meta( $this->post_id, $this->config, $this->clone_fields )
		);
	}

	/**
	 * Confirm values returned by get_all_custom_field_meta() are equal to values
	 * returned by get_post_meta().
	 */
	public function test_options_get_all_custom_field_meta() {
		$get_all_custom_field_meta = get_all_custom_field_meta( 'option', $this->config );

		$test_keys = include TEST_DATA_DIR . '/test_keys.php';

		array_walk( $test_keys, function( $test_key ) use ( $get_all_custom_field_meta ) {
			$this->assertEquals(
				get_option( "options_{$test_key}" ),
				$this->get_value_by_key( $test_key, $get_all_custom_field_meta )
			);
		} );
	}

	/**
	 * Confirm values returned by get_all_custom_field_meta() are equal to values
	 * returned by get_post_meta().
	 */
	public function test_post_meta_get_all_custom_field_meta() {
		$get_all_custom_field_meta = get_all_custom_field_meta( $this->post_id, $this->config );

		$test_keys = include TEST_DATA_DIR . '/test_keys.php';

		array_walk( $test_keys, function( $test_key ) use ( $get_all_custom_field_meta ) {

			$this->assertEquals(
				get_post_meta( $this->post_id, $test_key, true ),
				$this->get_value_by_key( $test_key, $get_all_custom_field_meta )
			);

		} );
	}

	/**
	 * Confirm clone field values returned by get_all_custom_field_meta() are equal to values
	 * returned by get_post_meta().
	 */
	public function test_post_meta_clone_fields_get_all_custom_field_meta() {
		$clone_config = json_decode( file_get_contents( TEST_DATA_DIR . '/test_clone_group.json' ), true );

		$get_all_custom_field_meta = get_all_custom_field_meta( $this->post_id, $this->config, [ $clone_config ] );

		$clone_keys = [
			'clone-text',
			'clone-repeater_0_subfield',
			'clone-repeater_1_subfield',
		];

		array_walk( $clone_keys, function( $clone_key ) use ( $get_all_custom_field_meta ) {

			$this->assertEquals(
				get_post_meta( $this->post_id, $clone_key, true ),
				$this->get_value_by_key( "clone_$clone_key", $get_all_custom_field_meta )
			);

		} );
	}

	/**
	 * Confirm values returned by get_all_custom_field_meta() are equal to values
	 * returned by get_post_meta().
	 */
	public function test_term_meta_get_all_custom_field_meta() {
		$get_all_custom_field_meta = get_all_custom_field_meta( 'term_0', $this->config );

		$test_keys = include TEST_DATA_DIR . '/test_keys.php';

		array_walk( $test_keys, function ( $test_key ) use ( $get_all_custom_field_meta ) {
			$this->assertEquals(
				get_term_meta( 0, $test_key, true ),
				$this->get_value_by_key( $test_key, $get_all_custom_field_meta )
			);
		} );
	}
}
