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
class PublicTest extends \WP_UnitTestCase {

	private $plugin;
	private $plugin_name = 'CleverReach Extension';
	private $plugin_slug = 'cleverreach-extension';
	private $plugin_version = '0.0.0';

	public function setUp() {
		$this->plugin = new Viewpublic\Cre_Public( $this->plugin_name, $this->plugin_slug, $this->plugin_version );
	}

	public function tearDown() {
		$this->plugin = null;
	}

	/**
	 * Test if frontend scripts are enqueued.
	 *
	 * @since 0.3.0
	 * @group public
	 */
	function testScriptsEnqueue() {
		$this->plugin->enqueue_scripts();
		$this->assertTrue( wp_script_is( $this->plugin_name ) );
	}

}