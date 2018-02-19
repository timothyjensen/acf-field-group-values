<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Acf_Field_Group_Values
 */

if ( ! defined( 'TEST_DATA_DIR' ) ) {
	define( 'TEST_DATA_DIR', __DIR__ . '/test-data' );
}

function get_test_data( $type ) {
	$test_data = include TEST_DATA_DIR . '/test_data.php';

	return $test_data[ $type ];
}

function is_system_test() {
	$phpunit_argv = $GLOBALS['argv'];

	if ( array_search( '--testsuite=system', $phpunit_argv, true ) ) {
		return true;
	}

	$phpunit_key = array_search( '--testsuite', $phpunit_argv, true );

	return $phpunit_key && 'system' === $phpunit_argv[ $phpunit_key + 1 ];
}

if ( is_system_test() ) {
	$_tests_dir = getenv( 'WP_TESTS_DIR' );
	if ( ! $_tests_dir ) {
		$_tests_dir = '/tmp/wordpress-tests-lib';
	}

	// Start up the WP testing environment.
	require $_tests_dir . '/includes/bootstrap.php';
}

// Load Composer autoloader.
if ( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __DIR__ ) . '/vendor/autoload.php';
}
