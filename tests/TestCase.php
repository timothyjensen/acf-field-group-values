<?php
/**
 * Class TestCase
 *
 * @package Acf_Field_Group_Values
 */

namespace TimJensen\ACF\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Class TestCase
 *
 * @package TimJensen\ACF\Tests
 */
abstract class TestCase extends PHPUnitTestCase {

	/**
	 * ACF field group config.
	 *
	 * @var array
	 */
	public $config = [];

	/**
	 * ACF clone fields config.
	 *
	 * @var array
	 */
	public $clone_fields = [];

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
		$this->config = json_decode( file_get_contents( TEST_DATA_DIR . '/test_field_group.json' ), true );

		$clone_field_1      = json_decode( file_get_contents( TEST_DATA_DIR . '/test_clone_group.json' ), true );
		$this->clone_fields = [ $clone_field_1 ];

		$this->test_meta = include TEST_DATA_DIR . '/test_data.php';
	}

	public function test_is_valid_config() {
		$this->assertTrue( is_array( $this->config ) );
		$this->assertTrue( isset( $this->config['fields'] ) );
	}

	/**
	 * Returns the value for the specified key from the array of custom field values.
	 *
	 * @param string $lookup_key
	 * @param array  $all_values
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

	/**
	 * Get test field config by field type.
	 *
	 * @param string $type Field type.
	 * @return array
	 */
	protected function get_field_config_by_type( $type ) {

		foreach ( $this->config['fields'] as $field ) {

			if ( $type === $field['type'] ) {

				if ( 'clone' === $type ) {
					$field = $this->get_clone_field_config( $field );
				}

				return $field;
			}
		}

		return [];
	}

	/**
	 * Get test clone field config.
	 *
	 * @param array $clone_field Clone field.
	 * @return array
	 */
	protected function get_clone_field_config( array $clone_field ) {

		$config = [];
		foreach ( $clone_field['clone'] as $clone_field_key ) {
			$clone_fields       = $this->instance->clone_fields;
			$clone_field_config = self::call_protected_method(
				$this->instance,
				'get_clone_field_config',
				[ $clone_field_key, $clone_fields ]
			);

			$config = array_merge( $config, $clone_field_config );
		}

		return $config;
	}

	/**
	 * Call protected methods as public.
	 *
	 * @param \TimJensen\ACF\Field_Group_Values $instance    Instance of \TimJensen\ACF\Field_Group_Values
	 * @param string                            $method_name Name of protected method.
	 * @param array                             $args        Arguments to pass to the method.
	 * @return mixed
	 */
	public static function call_protected_method( $instance, $method_name, array $args ) {
		$class  = new \ReflectionClass( $instance );
		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( $instance, $args );
	}

	/**
	 * Calls the protected method that has the name of the calling function, less the string 'test_'.
	 * For example, the test method `test_has_valid_field_structure()` will call `has_valid_field_structure()`.
	 *
	 * @param array  $args Array of arguments to pass to \TimJensen\ACF\Field_Group_Values.
	 * @param string $function
	 * @return mixed
	 */
	protected function get_protected_method_result( array $args = [ null ], $function = '' ) {
		// If no function is specified, get the name of the calling function and strip out 'test_'.
		$function = $function ? $function : str_replace( 'test_', '', debug_backtrace()[1]['function'] );

		return self::call_protected_method(
			$this->instance,
			$function,
			$args
		);
	}
}
