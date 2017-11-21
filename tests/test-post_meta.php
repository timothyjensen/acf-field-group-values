<?php
/**
 * Class Post_Meta
 *
 * @package Acf_Field_Group_Values
 */

/**
 * PostMeta test case.
 */
class Post_Meta extends Base {

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		array_walk( $this->test_meta, function( $value, $key ) {
			update_post_meta( $this->post_id, $key, $value );
		} );
	}

	/**
	 * Confirm values returned by get_all_custom_field_meta() are equal to values
	 * returned by get_post_meta().
	 */
	public function test_get_all_custom_field_meta() {
		$get_all_custom_field_meta = get_all_custom_field_meta( $this->post_id, $this->config );

		$test_keys = include __DIR__ . '/acf-json/test_keys.php';

		array_walk( $test_keys, function( $test_key ) use ( $get_all_custom_field_meta ) {
			$this->assertEquals(
				$this->get_value_by_key( $test_key, $get_all_custom_field_meta ),
				get_post_meta( $this->post_id, $test_key, true )
			);
		} );
	}
}
