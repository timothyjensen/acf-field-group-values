<?php
/**
 * Field_Group_Values Class
 *
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 2.0+
 * @link        https://www.timjensen.us
 * @since       1.3.0
 */

namespace TimJensen\ACF;

if ( ! class_exists( 'TimJensen\ACF\Field_Group_Values' ) ) :

	/**
	 * Class Field_Group_Values
	 */
	class Field_Group_Values {

		/**
		 * Field group configuration array.
		 *
		 * @var array
		 */
		private $config;

		/**
		 * Post ID or 'option'.
		 *
		 * @var int|string
		 */
		private $post_id;

		/**
		 * Stores all the custom field values.
		 *
		 * @var array
		 */
		private $results = [];

		/**
		 * Field_Group_Values constructor.
		 *
		 * @param mixed $post_id Post ID, or 'options' when retrieving option values.
		 * @param array $config  Field group configuration array.
		 */
		public function __construct( $post_id, array $config ) {
			$this->post_id = $post_id;
			$this->config  = $config;
		}

		/**
		 * Builds the multidimensional array that contains all the custom field values.
		 *
		 * @return array
		 */
		public function get_all_field_group_values() {

			$this->reset_results();

			foreach ( $this->config as $field ) {

				if ( ! $this->has_valid_field_structure( $field ) ) {
					continue;
				}

				$field_key   = $this->get_field_key( $field );
				$field_value = $this->get_field_value( $field_key );

				if ( $this->is_flexible_content_field( $field ) ) {

					if ( empty( $field_value ) ) {
						continue;
					}

					$this->get_flexible_content_field_values( $field, $field_key, $field_value );

				} elseif ( $this->is_repeater_field( $field ) ) {

					if ( empty( $field_value ) ) {
						continue;
					}

					$this->get_repeater_field_values( $field, $field_key, $field_value );

				} else {

					$this->store_field_value( $field, $field_value );

				}
			}

			return $this->results;
		}

		/**
		 * Resets the results property so that the final array is formatted correctly.
		 *
		 * @return void
		 */
		private function reset_results() {
			$this->results = [];
		}

		/**
		 * Determines whether the specified field has a valid structure.
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		private function has_valid_field_structure( array $field ) {
			return ! empty( $field['name'] );
		}

		/**
		 * Builds the field key that is used for looking up the custom field value.
		 *
		 * @param array $field ACF field configuration.
		 * @return string
		 */
		private function get_field_key( array $field ) {
			$field_key = $field['name'];

			if ( isset( $field['field_key_prefix'] ) ) {
				return $field['field_key_prefix'] . $field_key;
			}

			return $field_key;
		}

		/**
		 * Retrieve the value for the specified field, either from the options table or post meta table.
		 *
		 * @param string $field_key Custom field key.
		 * @return mixed
		 */
		private function get_field_value( string $field_key ) {
			if ( 'option' === $this->post_id ) {
				return get_option( "options_{$field_key}" );
			}

			return get_post_meta( $this->post_id, $field_key, true );
		}

		/**
		 * Determines whether the specified field is of the flexible content type.
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		private function is_flexible_content_field( array $field ) {
			return isset( $field['layouts'] );
		}

		/**
		 * Determines whether the specified field is of the repeater type.
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		private function is_repeater_field( array $field ) {
			return isset( $field['sub_fields'] );
		}

		/**
		 * Returns an array of ACF flexible content layout types.
		 *
		 * @param array $field ACF field configuration.
		 * @return array
		 */
		private function get_flexible_content_layout_types( array $field ) {

			$layout_types = [];
			foreach ( $field['layouts'] as $layout ) {
				$layout_types[ $layout['name'] ] = $layout;
			}

			return $layout_types;
		}

		/**
		 * Returns the values for repeater fields.
		 *
		 * @param array  $field       ACF field configuration.
		 * @param string $field_key   Field key.
		 * @param array  $field_value Array of layout types for each flexible content row.
		 * @return void
		 */
		private function get_flexible_content_field_values( array $field, string $field_key, array $field_value ) {

			/** @TODO find a way to write to the results property without destroying the formatting. * */
			$results = $this->results;

			$layout_types = $this->get_flexible_content_layout_types( $field );

			foreach ( $field_value as $index => $current_layout_type ) {

				$this->config = $layout_types[ $current_layout_type ]['sub_fields'];

				if ( empty( $this->config ) ) {
					continue;
				}

				foreach ( $this->config as &$field_config ) {
					$field_config['field_key_prefix'] = $field_key . "_{$index}_";
				}

				$results[ $field['name'] ][] = array_merge(
					[
						'acf_fc_layout' => $current_layout_type,
					],
					$this->get_all_field_group_values()
				);
			}

			$this->results = $results;
		}

		/**
		 * Returns the custom field values for repeater fields.
		 *
		 * @param array  $field       ACF field configuration.
		 * @param string $field_key   Field key.
		 * @param string $field_value Field value.
		 * @return void
		 */
		private function get_repeater_field_values( array $field, string $field_key, string $field_value ) {

			/** @TODO find a way to write to the results property without destroying the formatting. * */
			$results = $this->results;

			for ( $i = 0; $i < $field_value; $i ++ ) {
				$this->config = $field['sub_fields'];

				if ( empty( $this->config ) ) {
					continue;
				}

				foreach ( $this->config as &$field_config ) {
					$field_config['field_key_prefix'] = $field_key . "_{$i}_";
				}

				$results[ $field['name'] ][] = $this->get_all_field_group_values();
			}

			$this->results = $results;
		}

		/**
		 * Store the field value to the results property.
		 *
		 * @param array  $field       ACF field configuration.
		 * @param string $field_value Field value.
		 * @return void
		 */
		private function store_field_value( array $field, string $field_value ) {
			$this->results[ $field['name'] ] = $field_value;
		}

		/**
		 * Returns a multidimensional array containing all the custom field values.
		 *
		 * @return array
		 */
		public function get_results() {
			return $this->results;
		}
	}

endif;
