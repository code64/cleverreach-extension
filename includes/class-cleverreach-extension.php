<?php

namespace CleverreachExtension\Core;

use CleverreachExtension\Viewpublic;
use CleverreachExtension\Viewadmin;

/**
 * Core plugin class to load internationalization, admin and public functions.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
class Cleverreach_Extension {

	/**
	 * Loader to maintaining and registering all hooks that power the plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    Cre_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The unique identifier slug of this plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_slug The string used to uniquely identify this plugin.
	 */
	protected $plugin_slug;

	/**
	 * The unique identifier settings field of this plugin.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @var    string $plugin_settings The string used to uniquely identify this plugin in the database.
	 */
	protected $plugin_settings;

	/**
	 * Path to the main plugin file, relative to the plugins directory.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_basename Path without the leading and trailing slashes.
	 */
	protected $plugin_basename;

	/**
	 * The current version of the plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_version The current version of the plugin.
	 */
	protected $plugin_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since 0.1.0
	 * @param string $plugin_name     The name of this plugin.
	 * @param string $plugin_slug     The slug of this plugin.
	 * @param string $plugin_settings The settings name of this plugin.
	 * @param string $plugin_basename The basename of this plugin.
	 * @param string $plugin_version  The current version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug, $plugin_settings, $plugin_basename, $plugin_version ) {

		$this->plugin_name     = $plugin_name;
		$this->plugin_slug     = $plugin_slug;
		$this->plugin_settings = $plugin_settings;
		$this->plugin_basename = $plugin_basename;
		$this->plugin_version  = $plugin_version;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_models();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function load_dependencies() {

		// @TODO: Include via spl_autoload_register()
		$plugin_root = dirname( __FILE__ );
		require_once plugin_dir_path( $plugin_root ) . 'includes/class-cre-loader.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/class-cre-helper.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/class-cre-i18n.php';
		require_once plugin_dir_path( $plugin_root ) . 'admin/class-cre-admin.php';
		require_once plugin_dir_path( $plugin_root ) . 'public/class-cre-public.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/class-cre-models.php';

		// Get the interfaces first
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/interface-group-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/interface-form-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/interface-receiver-adapter.php';

		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach-group-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach-form-adapter.php';
		require_once plugin_dir_path( $plugin_root ) . 'includes/api/class-cleverreach-receiver-adapter.php';

		$this->loader = new Cre_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function set_locale() {

		$plugin_i18n = new Cre_i18n();
		$plugin_i18n->set_domain( 'cleverreachextension' );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Viewadmin\Cre_Admin( $this->get_plugin_name(), $this->get_plugin_slug(), $this->get_version() );

		$this->loader->add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), $plugin_admin, 'add_action_links' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'admin_enqueue_styles' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

		$this->loader->add_action( 'wp_ajax_cre_admin_ajax_controller_interaction', $plugin_admin, 'admin_ajax_controller_interaction' ); // Executes only for users that are logged in.

	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_public_hooks() {

		$plugin_public = new Viewpublic\Cre_Public( $this->get_plugin_name(), $this->get_plugin_slug(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_nopriv_cre_ajax_controller_interaction', $plugin_public, 'ajax_controller_interaction' ); // Executes for users that are *not* logged in.
		$this->loader->add_action( 'wp_ajax_cre_ajax_controller_interaction', $plugin_public, 'ajax_controller_interaction' ); // Executes for users that are logged in.

	}

	/**
	 * Register and and parse shortcodes and plugin integrations.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_models() {

		$plugin_models = new Cre_Models();

		$plugin_models->init_shortcodes();

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since  0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  0.1.0
	 * @return Cre_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the unique name of the plugin.
	 *
	 * @since  0.1.0
	 * @return string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the unique slug of the plugin.
	 *
	 * @since  0.1.0
	 * @return string    The slug of the plugin.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Retrieve the path of the main plugin file, relative to the plugins directory.
	 *
	 * @since  0.1.0
	 * @return string    Path without the leading and trailing slashes.
	 */
	public function get_plugin_basename() {
		return $this->plugin_basename;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  0.1.0
	 * @return string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->plugin_version;
	}

}