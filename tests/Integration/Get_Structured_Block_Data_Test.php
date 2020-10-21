<?php
/**
 * Class Get_Structured_Block_Data_Test
 *
 * @package     TimJensen\ACF\Tests
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 2.0+
 * @link        https://www.timjensen.us
 * @since       3.3.0
 */

namespace TimJensen\ACF\Tests;

use TimJensen\ACF\Field_Group_Values;

/**
 * Class Get_Structured_Block_Data_Test
 *
 * @package TimJensen\ACF\Tests
 */
class Get_Structured_Block_Data_Test extends TestCase {

	/**
	 * @group Blocks
	 */
	public function test_get_structured_block_data() {
		$get_structured_block_data = get_structured_block_data( $this->test_meta['block_data'], $this->config, $this->clone_fields );

		$test_keys = include TEST_DATA_DIR . '/test_keys.php';

		array_walk( $test_keys, function ( $lookup_value, $meta_key ) use ( $get_structured_block_data ) {
			$this->assertEquals(
				$this->test_meta['block_data'][ $meta_key ],
				$this->get_value_by_key( $lookup_value, $get_structured_block_data )
			);
		} );
	}

	/**
	 * @group Blocks
	 */
	public function test_get_structured_block_data_with_labels() {
		$get_structured_block_data = get_structured_block_data( $this->test_meta['block_data'], $this->config, $this->clone_fields, true );

		$test_keys = include TEST_DATA_DIR . '/test_keys.php';

		array_walk( $test_keys, function ( $lookup_value, $meta_key ) use ( $get_structured_block_data ) {
			$this->assertEquals(
				$this->test_meta['block_data'][ $meta_key ],
				$this->get_value_by_key( $lookup_value, $get_structured_block_data )['value']
			);
		} );

		array_walk( $test_keys, function ( $lookup_value, $meta_key ) use ( $get_structured_block_data ) {
			$this->assertEquals(
				$this->test_meta['labels'][ $meta_key ],
				$this->get_value_by_key( $lookup_value, $get_structured_block_data )['label']
			);
		} );
	}
}
