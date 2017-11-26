<?php
/**
 * Field_Group_Values Class
 *
 * @author      Tim Jensen <tim@timjensen.us>
 * @license     GNU General Public License 2.0+
 * @link        https://www.timjensen.us
 * @since       1.3.0
 * @package     TimJensen\ACF\Field_Group_Values
 */

declare( strict_types=1 );

namespace TimJensen\ACF;

if ( ! class_exists( 'TimJensen\ACF\Field_Group_Values' ) ) :

	/**
	 * Class Field_Group_Values
	 */
	class Field_Group_Values {

		/**
		 * Field group configuration array for the current level of recursion.
		 *
		 * @var array
		 */
		public $config;

		/**
		 * Field group configuration array containing fields/groups to clone.
		 *
		 * @since 2.0.0
		 *
		 * @var array
		 */
		protected $clone_fields;

		/**
		 * Post ID or 'option'.
		 *
		 * @var int|string
		 */
		protected $post_id;

		/**
		 * Stores all the custom field values.
		 *
		 * @var array
		 */
		protected $results = [];

		/**
		 * Field_Group_Values constructor.
		 *
		 * @param int|string $post_id      Post ID, 'options', or 'term_{id}'.
		 * @param array      $config       Field group configuration array.
		 * @param array      $clone_fields Field group configuration arrays for cloned fields/groups.
		 */
		public function __construct( $post_id, array $config, $clone_fields = [] ) {
			$this->post_id      = $post_id;
			$this->config       = $config['fields'];
			$this->clone_fields = array_merge( [ $config ], $clone_fields );
			$this->get_all_field_group_values( $this->config );
		}

		/**
		 * Builds the multidimensional array that contains all the custom field values.
		 *
		 * @return array
		 */
		public function get_all_field_group_values( $config ): array {

			$this->reset_results();

			foreach ( $config as $field ) {

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

				} elseif ( $this->is_clone_field( $field ) ) {

					$this->get_clone_field_values( $field );

				} elseif ( $this->is_group_field( $field ) ) {

					$this->get_group_field_values( $field, $field_key, $field_value );

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
		protected function reset_results() {
			$this->results = [];
		}

		/**
		 * Determines whether the specified field has a valid structure.
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		protected function has_valid_field_structure( array $field ): bool {
			return ! empty( $field['name'] );
		}

		/**
		 * Builds the field key that is used for looking up the custom field value.
		 *
		 * @param array $field ACF field configuration.
		 * @return string
		 */
		protected function get_field_key( array $field ): string {
			$field_key = $field['name'];

			if ( isset( $field['meta_key_prefix'] ) ) {
				return $field['meta_key_prefix'] . $field_key;
			}

			return $field_key;
		}

		/**
		 * Retrieve the value for the specified field, either from the options table or post meta table.
		 *
		 * @param string $field_key Custom field key.
		 * @return mixed
		 */
		protected function get_field_value( string $field_key ) {
			if ( 'option' === $this->post_id ) {
				return get_option( "options_{$field_key}" );
			} elseif ( is_string( $this->post_id ) && 'term_' === substr( $this->post_id, 0, 5 ) ) {
				$term_id = (int) substr( $this->post_id, 5 );

				return get_term_meta( $term_id, $field_key, true );
			}

			return get_post_meta( $this->post_id, $field_key, true );
		}

		/**
		 * Returns true if $field represents a flexible content field.
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		protected function is_flexible_content_field( array $field ): bool {
			return isset( $field['type'] ) && 'flexible_content' === $field['type'];
		}

		/**
		 * Returns true if $field represents a clone field.
		 *
		 * @since 1.4.0
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		protected function is_clone_field( array $field ): bool {
			return isset( $field['type'] ) && 'clone' === $field['type'];
		}

		/**
		 * Returns true if $field represents a group field.
		 *
		 * @since 1.4.0
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		protected function is_group_field( array $field ): bool {
			return isset( $field['type'] ) && 'group' === $field['type'];
		}

		/**
		 * Returns true if $field represents a repeater field.
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		protected function is_repeater_field( array $field ): bool {
			return isset( $field['type'] ) && 'repeater' === $field['type'];
		}

		/**
		 * Returns true if $field represents a field group.
		 *
		 * @since 2.0.0
		 *
		 * @param array $field ACF field configuration.
		 * @return bool
		 */
		protected function is_field_group( array $field ): bool {
			return isset( $field['fields'] );
		}

		/**
		 * Returns an array of ACF flexible content layout types.
		 *
		 * @param array $field ACF field configuration.
		 * @return array
		 */
		protected function get_flexible_content_layout_types( array $field ): array {

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
		 * @param string $parent_meta_key   Field key.
		 * @param array  $field_value Array of layout types for each flexible content row.
		 * @return void
		 */
		protected function get_flexible_content_field_values( array $field, string $parent_meta_key, array $field_value
		) {
			$results = $this->results;

			$layout_types = $this->get_flexible_content_layout_types( $field );

			// Loop through the chosen layouts.
			foreach ( $field_value as $index => $current_layout_type ) {

				// Check if the layout has been deleted from the ACF group.
				if ( empty( $layout_types[ $current_layout_type ]['sub_fields'] ) ) {
					continue;
				}

				$config = $layout_types[ $current_layout_type ]['sub_fields'];

				$config = $this->set_meta_key_prefix( 'flexible_content', $config, $parent_meta_key, $index );

				$results[ $field['name'] ][] = array_merge(
					[
						'acf_fc_layout' => $current_layout_type,
					],
					$this->get_all_field_group_values( $config )
				);
			}

			$this->results = $results;
		}

		/**
		 * Returns the custom field values for clone fields.
		 *
		 * @since 1.4.0
		 *
		 * @param array $field ACF field configuration.
		 * @return void
		 */
		protected function get_clone_field_values( array $field ) {

			$results = $this->results;

			$config = [];
			foreach ( $field['clone'] as $clone_field_key ) {

				$clone_field_config = $this->get_clone_field_config( $clone_field_key, $this->clone_fields );

				// A `false` value means the corresponding field was deleted from the field group.
				if ( false === $clone_field_config ) {
					continue;
				}

				$config = array_merge( $config, $clone_field_config );
			}

			$config = $this->set_meta_key_prefix( 'clone', $config );

			$results[ $field['name'] ] = $this->get_all_field_group_values( $config );

			$this->results = $results;
		}

		/**
		 * Sets the value for the meta key prefix and adds it to the field configuration array.
		 *
		 * @since 2.1.2
		 *
		 * @param string $field_type      Field type: 'clone', 'repeater', 'flexible_content', or 'group'.
		 * @param array  $config          Field configuration.
		 * @param string $parent_meta_key Meta key of the parent field.
		 * @param int    $index           Loop index.
		 * @return array
		 */
		protected function set_meta_key_prefix( string $field_type, array $config, string $parent_meta_key = '', int
		$index
		= 0 ): array {
			switch ( $field_type ) {

				case 'clone':
					foreach ( $config as &$field_config ) {

						// Build the field key prefix including ACF's option for prefixing, if set.
						$prefix = empty( $field['meta_key_prefix'] ) ? '' : $field['meta_key_prefix'];
						$prefix = empty( $field['prefix_name'] ) ? $prefix : "{$field['name']}_{$prefix}";

						$field_config['meta_key_prefix'] = $prefix;
					}

					break;

				case 'repeater':
					foreach ( $config as &$field_config ) {
						$field_config['meta_key_prefix'] = "{$parent_meta_key}_{$index}_";
					}

					break;

				case 'flexible_content':
					foreach ( $config as &$field_config ) {
						$field_config['meta_key_prefix'] = "{$parent_meta_key}_{$index}_";
					}

					break;

				case 'group':
					foreach ( $config as &$field_config ) {
						$field_config['meta_key_prefix'] = "{$parent_meta_key}_";
					}

					break;
			}

			return $config;
		}

		/**
		 * Recursively search for the appropriate clone configuration array.
		 *
		 * @since 1.4.0
		 *
		 * @param string $clone_field_key Field key to search for.
		 * @param array  $clone_fields    ACF fields configuration.
		 * @return bool|array
		 */
		protected function get_clone_field_config( string $clone_field_key, array $clone_fields ) {

			foreach ( $clone_fields as $field ) {

				if ( $field['key'] === $clone_field_key ) {

					if ( $this->is_field_group( $field ) ) {
						return $field['fields'];
					}

					return [ $field ];

				} elseif ( $this->is_field_group( $field ) ) {

					$result = $this->get_clone_field_config( $clone_field_key, $field['fields'] );

					if ( $result ) {
						return $result;
					}
				} elseif ( $this->is_repeater_field( $field ) || $this->is_group_field( $field ) ) {

					$result = $this->get_clone_field_config( $clone_field_key, $field['sub_fields'] );

					if ( $result ) {
						return $result;
					}
				} elseif ( $this->is_flexible_content_field( $field ) ) {

					$result = $this->get_clone_field_config( $clone_field_key, $field['layouts'] );

					if ( $result ) {
						return $result;
					}
				}
			}

			return false;
		}

		/**
		 * Returns the custom field values for group fields.
		 *
		 * @since 1.4.0
		 *
		 * @param array  $field       ACF field configuration.
		 * @param string $parent_meta_key   Field key.
		 * @param string $field_value Field value.
		 * @return void
		 */
		protected function get_group_field_values( array $field, string $parent_meta_key, string $field_value ) {
			$results = $this->results;

			$field['sub_fields'] = $this->set_meta_key_prefix( 'group', $field['sub_fields'], $parent_meta_key );

			$results[ $field['name'] ] = $this->get_all_field_group_values( $field['sub_fields'] );

			$this->results = $results;
		}

		/**
		 * Returns the custom field values for repeater fields.
		 *
		 * @param array  $field       ACF field configuration.
		 * @param string $parent_meta_key   Field key.
		 * @param string $field_value Field value.
		 * @return void
		 */
		protected function get_repeater_field_values( array $field, string $parent_meta_key, string $field_value ) {
			$results = $this->results;

			for ( $i = 0; $i < $field_value; $i ++ ) {
				$field['sub_fields'] = $this->set_meta_key_prefix( 'repeater', $field['sub_fields'], $parent_meta_key, $i );

				$results[ $field['name'] ][] = $this->get_all_field_group_values( $field['sub_fields'] );
			}

			$this->results = $results;
		}

		/**
		 * Store the field value to the results property.
		 *
		 * @param array        $field       ACF field configuration.
		 * @param string|array $field_value Field value.
		 * @return void
		 */
		protected function store_field_value( array $field, $field_value ) {
			$this->results[ $field['name'] ] = $field_value;
		}

		/**
		 * Returns a multidimensional array containing all the custom field values.
		 *
		 * @return array
		 */
		public function get_results(): array {
			return $this->results;
		}
	}

endif;
