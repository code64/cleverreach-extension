<?php
/**
 * CleverReach Extension PHPUnit Test Suite
 */
echo "\n";
echo "\033[0;32m CleverReach Extension Test Suite \033[0m" . "\n";
echo "\033[0;32m Version: 0.2.0 \033[0m" . "\n";
echo "\n";

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../cleverreach-extension.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';