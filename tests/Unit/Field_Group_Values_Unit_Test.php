<?php
/**
 * Class Field_Group_Values_Unit_Test
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
 * Class Field_Group_Values_Unit_Test
 *
 * @package TimJensen\ACF\Tests
 */
class Field_Group_Values_Unit_Test extends TestCase {

	/**
	 * Holds an instance of \TimJensen\ACF\Field_Group_Values.
	 *
	 * @var \TimJensen\ACF\Field_Group_Values
	 */
	protected $instance;

	/**
	 * ACF Field config
	 *
	 * @var array
	 */
	protected $field;

	/**
	 * Test setup.
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
		$this->field    = $this->config['fields'][0];

		$this->instance->get_results();
	}

	/**
	 * Test tear down.
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Tests reset_results().
	 */
	public function test_reset_results() {
		$results_array = $this->instance->results;
		$this->assertFalse( empty( $results_array ) );

		$this->get_protected_method_result();

		$results_array = $this->instance->results;
		$this->assertTrue( empty( $results_array ) );
	}

	/**
	 * Tests has_valid_field_structure().
	 */
	public function test_has_valid_field_structure() {
		$field_is_valid   = $this->get_protected_method_result( [ $this->field ] );
		$group_is_invalid = $this->get_protected_method_result( [ $this->config ] );

		$this->assertTrue( $field_is_valid );
		$this->assertFalse( $group_is_invalid );
	}

	/**
	 * Test get_field_key().
	 */
	public function test_get_field_key() {
		$subfields = $this->get_protected_method_result(
			[
				$this->field['type'],
				$this->field['sub_fields'],
				$this->field['name'],
			],
			'set_meta_key_prefix'
		);

		$field_key = $this->get_protected_method_result( [ $subfields[0] ] );

		$this->assertEquals(
			'group_text',
			$field_key
		);
	}

	/**
	 * Test get_field_value().
	 */
	public function test_get_field_value() {
		// Post meta.
		$field_value = $this->get_protected_method_result( [ 'group_text' ] );
		$this->assertEquals( 'POST META: Group text field', $field_value );

		// Option.
		$this->instance = new Field_Group_Values( 'option', $this->config );
		$field_value    = $this->get_protected_method_result( [ 'group_text' ] );
		$this->assertEquals( 'OPTION: Group text field', $field_value );

		// Option, alternate key.
		$this->instance = new Field_Group_Values( 'options', $this->config );
		$field_value    = $this->get_protected_method_result( [ 'group_text' ] );
		$this->assertEquals( 'OPTION: Group text field', $field_value );

		// Term meta.
		$this->instance = new Field_Group_Values( 'term_2', $this->config );
		$field_value    = $this->get_protected_method_result( [ 'group_text' ] );
		$this->assertEquals( 'TERM META: Group text field', $field_value );
	}

	/**
	 * Test get_flexible_content_layout_types().
	 */
	public function test_get_flexible_content_layout_types() {
		$layout_types = $this->get_protected_method_result( [ $this->config['fields'][2] ] );

		$this->assertEquals(
			array_column( $this->config['fields'][2]['layouts'], 'name' ),
			array_keys( $layout_types )
		);
	}

	/**
	 * Test set_meta_key_prefix().
	 */
	public function test_set_meta_key_prefix() {
		$field_config = [ $this->field ];

		$test_sets = [
			[
				'group',
				$this->get_field_config_by_type( 'group' )['sub_fields'],
				'parent_key',
			],
			[
				'repeater',
				$this->get_field_config_by_type( 'repeater' )['sub_fields'],
				'parent_key',
				1,
			],
			[
				'flexible_content',
				$this->get_field_config_by_type( 'flexible_content' )['layouts'],
				'parent_key',
				1,
			],
			[
				'clone',
				$this->get_field_config_by_type( 'clone' ),
				'',
			],
		];

		foreach ( $test_sets as $test_set ) {
			$config = $this->get_protected_method_result( $test_set );

			array_walk( $config, function ( $field_config ) use ( $test_set ) {
				$expected_key = empty( $test_set[2] ) ? '' : $test_set[2] . '_';
				$expected_key = isset( $test_set[3] ) ? $expected_key . $test_set[3] . '_' : $expected_key;

				$this->assertEquals( $expected_key, $field_config['meta_key_prefix'] );
			} );
		}
	}

	public function test_get_clone_field_config() {
		// From test_field_group.json.
		$clone_field_keys = [ 'group_5a14771d289be', 'field_5a1479742d333' ];

		$expected_config_1  = $this->instance->clone_fields[1]['fields'];
		$clone_field_config = $this->get_protected_method_result( [
			'group_5a14771d289be',
			$this->instance->clone_fields,
		] );
		$this->assertEquals( $expected_config_1, $clone_field_config );

		$expected_config_2  = $this->instance->clone_fields[1]['fields'][1]['sub_fields'];
		$clone_field_config = $this->get_protected_method_result( [
			'field_5a1479742d333',
			$this->instance->clone_fields,
		] );
		$this->assertEquals( $expected_config_2, $clone_field_config );
	}
}
