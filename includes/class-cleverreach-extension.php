<?php

namespace CleverreachExtension\Core;

use CleverreachExtension\Viewpublic;
use CleverreachExtension\Viewadmin;

defined( 'ABSPATH' ) or die();

class Cleverreach_Extension {

	protected $loader;
	protected $plugin_name;
	protected $plugin_slug;
	protected $plugin_basename;
	protected $plugin_version;

	public function __construct( $plugin_name, $plugin_slug, $plugin_basename, $plugin_version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;
		$this->plugin_basename = $plugin_basename;
		$this->plugin_version = $plugin_version;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_models();

	}

	private function load_dependencies() {

		$plugin_root = dirname( __FILE__ );
		require_once plugin_dir_path( $plugin_root ) . 'includes/class-cre-loader.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/class-cre-i18n.php';
		require_once plugin_dir_path( $plugin_root ) . 'admin/class-cre-admin.php';
		require_once plugin_dir_path( $plugin_root ) . 'public/class-cre-public.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/class-cre-models.php';

		// Get the interface first

		require_once plugin_dir_path( $plugin_root ) . 'includes/api/interface-group-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/interface-form-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/interface-receiver-adapter.php';

		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach-group-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach-form-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach-receiver-adapter.php';

		$this->loader = new Cre_Loader();

	}

	private function set_locale() {

		$plugin_i18n = new Cre_i18n();
		$plugin_i18n->set_domain( 'cleverreachextension' );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new Viewadmin\Cre_Admin( $this->get_plugin_name(), $this->get_plugin_slug(), $this->get_version() );

		$this->loader->add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), $plugin_admin, 'add_action_links' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'render_admin_notices' );

	}

	private function define_public_hooks() {

		$plugin_public = new Viewpublic\Cre_Public( $this->get_plugin_name(), $this->get_plugin_slug(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_nopriv_cre_ajax_controller_interaction', $plugin_public, 'ajax_controller_interaction' );
		$this->loader->add_action( 'wp_ajax_cre_ajax_controller_interaction', $plugin_public, 'ajax_controller_interaction' );

	}

	public function define_models() {

		$plugin_models = new Cre_Models();

		$plugin_models->init_shortcodes();

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	public function get_plugin_basename() {
		return $this->plugin_basename;
	}

	public function get_version() {
		return $this->plugin_version;
	}

	public function get_loader() {
		return $this->loader;
	}

}
