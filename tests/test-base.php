<?php
/**
 * Class SampleTest
 *
 * @package Acf_Field_Group_Values
 */

/**
 * Sample test case.
 */
class Base extends WP_UnitTestCase {

	/**
	 * ACF field group config.
	 *
	 * @var array
	 */
	public $config = [];

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $post_id = 0;

	/**
	 * Test meta data.
	 *
	 * @var array
	 */
	public $test_meta = [];

	/**
	 * Test meta key.
	 *
	 * @var string
	 */
	public $meta_key = '';

	public function setUp() {
		$this->config = json_decode( file_get_contents( __DIR__ . '/acf-json/test_field_group.json' ), true );

		$this->test_meta = include __DIR__ . '/acf-json/test_data.php';
	}

	public function test_is_valid_config() {
		$this->assertTrue( is_array( $this->config ) );
		$this->assertTrue( isset( $this->config['fields'] ) );
	}

	/**
	 * Returns the value for the specified key from the array of custom field values.
	 *
	 * @param string $lookup_key
	 * @param array $all_values
	 * @return mixed
	 */
	protected function get_value_by_key( $lookup_key, $all_values ) {
		$value = $all_values;

		$key_fragments = explode( '_', $lookup_key );
		foreach ( $key_fragments as $key ) {
			$value = $value[ $key ];
		}

		return $value;
	}
}
