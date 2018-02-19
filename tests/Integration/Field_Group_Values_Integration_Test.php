<?php
/**
 * Class Field_Group_Values_Integration_Test
 *
 * @package     TimJensen\ACF\Tests
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 2.0+
 * @link        https://www.timjensen.us
 * @since       2.3.0
 */

namespace TimJensen\ACF\Tests;

use Brain\Monkey;
use TimJensen\ACF\Field_Group_Values;

/**
 * Class Field_Group_Values_Integration_Test
 *
 * @package TimJensen\ACF\Tests
 */
class Field_Group_Values_Integration_Test extends TestCase {

	/**
	 * Holds an instance of Field_Group_Values.
	 *
	 * @var Field_Group_Values
	 */
	protected $instance;

	/**
	 * ACF group field config
	 *
	 * @var array
	 */
	protected $group_field;

	/**
	 * ACF repeater field config
	 *
	 * @var array
	 */
	protected $repeater_field;

	/**
	 * ACF flexible content field config
	 *
	 * @var array
	 */
	protected $flexcontent_field;

	/**
	 * ACF clone field config
	 *
	 * @var array
	 */
	protected $clone_field;

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

		$this->instance = new Field_Group_Values(
			$this->post_id,
			$this->config,
			$this->clone_fields
		);

		$this->group_field       = $this->config['fields'][0];
		$this->repeater_field    = $this->config['fields'][1];
		$this->flexcontent_field = $this->config['fields'][2];
		$this->clone_field       = $this->config['fields'][3];
	}

	/**
	 * Test tear down.
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Tests get_all_field_group_values().
	 */
	public function test_get_all_field_group_values() {
		$this->instance->get_all_field_group_values( $this->instance->config );

		$expected_keys = [
			'group',
			'repeater',
			'flexcontent',
			'clone',
		];

		array_walk( $expected_keys, function ( $expected_key ) {
			$this->assertArrayHasKey( $expected_key, $this->instance->results );
		} );
	}

	/**
	 * Tests get_flexible_content_field_values().
	 */
	public function test_get_flexible_content_field_values() {
		$flexcontent_field_name = $this->flexcontent_field['name'];

		$this->assertArrayNotHasKey( $flexcontent_field_name, $this->instance->results );

		$test_data = \get_test_data( 'post_meta' );

		$this->get_protected_method_result( [
			$this->flexcontent_field,
			'',
			$test_data[ $flexcontent_field_name ],
		] );

		$this->assertArrayHasKey( $flexcontent_field_name, $this->instance->results );
	}

	/**
	 * Tests get_clone_field_values().
	 */
	public function test_get_clone_field_values() {
		$clone_field_name = $this->clone_field['name'];

		$this->assertArrayNotHasKey( $clone_field_name, $this->instance->results );

		$test_data = \get_test_data( 'post_meta' );

		$this->get_protected_method_result( [
			$this->clone_field,
			'',
			$test_data[ $clone_field_name ],
		] );

		$this->assertArrayHasKey( $clone_field_name, $this->instance->results );
	}

	/**
	 * Tests get_group_field_values().
	 */
	public function test_get_group_field_values() {
		$this->assertArrayNotHasKey( $this->group_field['name'], $this->instance->results );

		$this->get_protected_method_result( [
			$this->group_field,
			'',
			'',
		] );

		$this->assertArrayHasKey( $this->group_field['name'], $this->instance->results );
	}

	/**
	 * Tests get_repeater_field_values().
	 */
	public function test_get_repeater_field_values() {
		$repeater_field_name = $this->repeater_field['name'];

		$this->assertArrayNotHasKey( $repeater_field_name, $this->instance->results );

		$test_data = \get_test_data( 'post_meta' );

		$this->get_protected_method_result( [
			$this->repeater_field,
			'',
			$test_data[ $repeater_field_name ],
		] );

		$this->assertArrayHasKey( $repeater_field_name, $this->instance->results );
	}

	/**
	 * Tests get_results().
	 */
	public function test_get_results() {
		$this->assertEmpty( $this->instance->results );

		$results = $this->instance->get_results();

		$this->assertSame(
			$results,
			$this->instance->results
		);

		$this->assertSame(
			$this->instance->get_all_field_group_values( $this->instance->config ),
			$this->instance->results
		);
	}
}
