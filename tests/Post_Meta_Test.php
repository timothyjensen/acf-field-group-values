<?php
/**
 * Class Post_Meta_Test
 *
 * @package Acf_Field_Group_Values
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 2.0+
 * @link        https://www.timjensen.us
 * @since       2.1.2
 */

namespace TimJensen\ACF\Tests;

/**
 * Class Post_Meta_Test
 */
class Post_Meta_Test extends TestCase {

	public function setUp() {
		parent::setUp();
	}

	/**
	 * Confirm values returned by get_all_custom_field_meta() are equal to values
	 * returned by get_post_meta().
	 */
	public function test_get_all_custom_field_meta() {
		$get_all_custom_field_meta = get_all_custom_field_meta( $this->post_id, $this->config );

		$test_keys = include __DIR__ . '/test-data/test_keys.php';

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
	public function test_get_all_custom_field_meta_clone_fields() {
		$clone_config = json_decode( file_get_contents( __DIR__ . '/test-data/test_clone_group.json' ), true );

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
}
