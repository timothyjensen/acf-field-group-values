<?php
/**
 * Class Term_Meta
 *
 * @package Acf_Field_Group_Values
 */

/**
 * Term Meta test case.
 */
class Term_Meta extends Base {

	/**
	 * Term ID.
	 *
	 * @var int
	 */
	public $term_id = 0;

	public function setUp() {
		parent::setUp();

		$this->term_id = $this->factory->term->create();
		$this->post_id = "term_{$this->term_id}";

		array_walk( $this->test_meta, function ( $value, $key ) {
			update_term_meta( $this->term_id, $key, $value );
		} );
	}

	/**
	 * Confirm values returned by get_all_custom_field_meta() are equal to values
	 * returned by get_post_meta().
	 */
	public function test_get_all_custom_field_meta() {
		$get_all_custom_field_meta = get_all_custom_field_meta( $this->post_id, $this->config );

		$test_keys = include __DIR__ . '/acf-json/test_keys.php';

		array_walk( $test_keys, function ( $test_key ) use ( $get_all_custom_field_meta ) {
			$this->assertEquals(
				$this->get_value_by_key( $test_key, $get_all_custom_field_meta ),
				get_term_meta( $this->term_id, $test_key, true )
			);
		} );
	}
}
