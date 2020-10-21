<?php
/**
 * Plugin Name:     ACF Field Group Values
 * Plugin URI:      https://github.com/timothyjensen/acf-field-group-values
 * Description:     Retrieve all post meta, term meta, and option values for the specified ACF field group.
 * Author:          Tim Jensen
 * Author URI:      https://www.timjensen.us
 * Text Domain:     acf-field-group-values
 * Domain Path:     /languages
 * Version:         3.3.0
 *
 * @package         TimJensen\ACF\Field_Group_Values
 * @license         GPL-3.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
	/**
	 * Initialize deactivation functions.
	 *
	 * @since 2.2.0
	 */
	add_action( 'plugins_loaded', function () {
		if ( current_user_can( 'activate_plugins' ) ) {
			add_action( 'admin_init', 'get_all_custom_field_meta_deactivate_plugin' );
			add_action( 'admin_notices', 'get_all_custom_field_meta_deactivation_notice' );
		}
	} );

	if ( ! defined( 'get_all_custom_field_meta_deactivate_plugin' ) ) {
		/**
		 * Deactivate the plugin.
		 */
		function get_all_custom_field_meta_deactivate_plugin() {
			deactivate_plugins( __FILE__ );
		}
	}

	if ( ! defined( 'get_all_custom_field_meta_deactivation_notice' ) ) {
		/**
		 * Show deactivation admin notice.
		 */
		function get_all_custom_field_meta_deactivation_notice() {
			$notice = sprintf(
				// Translators: 1: Required PHP version, 2: Current PHP version.
				__( 'ACF Field Group Values requires PHP %1$s or higher to run. This site uses %2$s, so the plugin has been <strong>deactivated</strong>.', 'acf-field-group-values' ),
				'7.0',
				PHP_VERSION
			);

			?>
			<div class="notice notice-error"><p><?php echo wp_kses_post( $notice ); ?></p></div>
			<?php
		}
	}

	return false;
}

require __DIR__ . '/src/Field_Group_Values.php';
require __DIR__ . '/src/get_all_custom_field_meta.php';
require __DIR__ . '/src/get_structured_block_data.php';
