<?php
/**
 * Plugin Name:     ACF Field Group Values
 * Plugin URI:      https://github.com/timothyjensen/acf-field-group-values
 * Description:     Retrieve all post meta, term meta, and option values for the specified ACF field group.
 * Author:          Tim Jensen
 * Author URI:      https://www.timjensen.us
 * Text Domain:     acf-field-group-values
 * Domain Path:     /languages
 * Version:         2.2.0
 *
 * @package         TimJensen\ACF\Field_Group_Values
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {

	add_action( 'plugins_loaded', 'get_all_custom_field_meta_init_deactivation' );
	/**
	 * Initialize deactivation functions.
	 *
	 * @since 2.2.0
	 */
	function get_all_custom_field_meta_init_deactivation() {
		if ( current_user_can( 'activate_plugins' ) ) {
			add_action( 'admin_init', 'get_all_custom_field_meta_deactivate_plugin' );
			add_action( 'admin_notices', 'get_all_custom_field_meta_deactivation_notice' );
		}
	}

	/**
	 * Deactivate the plugin.
	 */
	function get_all_custom_field_meta_deactivate_plugin() {
		deactivate_plugins( __FILE__ );
	}

	/**
	 * Show deactivation admin notice.
	 */
	function get_all_custom_field_meta_deactivation_notice() {
		$notice = sprintf(
			// Translators: 1: Required PHP version, 2: Current PHP version.
			esc_html__( 'ACF Field Group Values requires PHP %1$s or higher to run. This site uses %2$s, so the plugin has been <strong>deactivated</strong>.', 'acf-field-group-values' ),
			'7.0',
			PHP_VERSION
		);

		?>
		<div class="notice notice-error"><p><?php echo $notice; // WPCS: XSS ok ?></p></div>
		<?php
	}

	return false;
}

require __DIR__ . '/src/Field_Group_Values.php';

if ( ! function_exists( 'get_all_custom_field_meta' ) ) :

	/**
	 * Instantiates the Field_Group_Values class and returns the results.
	 *
	 * @link    https://www.timjensen.us/acf-get-field-alternative/
	 *
	 * @param int|string $post_id      Required. Post ID, 'option', or 'term_{id}'.
	 * @param array      $config       Required. ACF field group JSON reformatted as an array.
	 * @param array      $clone_fields Optional. ACF field group JSON arrays for all cloned fields/groups.
	 * @return array
	 */
	function get_all_custom_field_meta( $post_id, array $config, array $clone_fields = [] ): array {
		if ( empty( $config['fields'] ) ) {
			trigger_error( 'As of version 2.0.0 the $config argument should include the field group key in addition to the array of fields. Pass $config instead of $config[\'fields\'].', E_USER_WARNING );
			$corrected_config['fields'] = $config;
			$config                     = $corrected_config;
		}

		$field_group_values = new \TimJensen\ACF\Field_Group_Values( $post_id, $config, $clone_fields );

		return $field_group_values->get_results();
	}

endif;
