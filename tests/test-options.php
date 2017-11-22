<?php
/**
 * Class Options
 *
 * @package Acf_Field_Group_Values
 */

/**
 * PostMeta test case.
 */
class Options extends Base {

	public function setUp() {
		parent::setUp();

		$this->post_id = 'option';

		array_walk( $this->test_meta, function( $value, $key ) {
			update_option( "options_{$key}", $value );
		} );
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
				get_option( "options_{$test_key}" ),
				$this->get_value_by_key( $test_key, $get_all_custom_field_meta )
			);
		} );
	}
}
