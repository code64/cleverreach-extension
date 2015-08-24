<?php

namespace CleverreachExtension\Tests;

use CleverreachExtension\Viewpublic;

/**
 * Contains all public-specific tests.
 *
 * @since      0.3.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/tests
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
class SetupTest extends \WP_UnitTestCase {

	/**
	 * Sample test.
	 *
	 * @since 0.3.0
	 * @group setup
	 */
	function testSample() {
		$this->assertTrue( true );
	}

	/**
	 * Test if plugin is active.
	 *
	 * @since 0.3.0
	 * @group setup
	 */
	function testPluginActive() {
		$this->assertTrue( is_plugin_active( 'cleverreach-extension/cleverreach-extension.php' ) );
	}

}