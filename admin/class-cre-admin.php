<?php

namespace CleverreachExtension\Viewadmin;

use CleverreachExtension\Core;
use CleverreachExtension\Core\Api;

/**
 * Contains all admin-specific functionality of the plugin.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/admin
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
class Cre_Admin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique identifier slug of this plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_slug The string used to uniquely identify this plugin.
	 */
	private $plugin_slug;

	/**
	 * The current version of the plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_version The current version of the plugin.
	 */
	private $plugin_version;

	/**
	 * Define the admin-specific functionality of the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name    The name of this plugin.
	 * @param string $plugin_slug    The slug of this plugin.
	 * @param string $plugin_version The current version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_slug    = $plugin_slug;
		$this->plugin_version = $plugin_version;

		$helper        = new Core\Cre_Helper();
		$this->api_key = $helper->get_option( 'api_key' );
		$this->list_id = $helper->get_option( 'list_id' );
		$this->form_id = $helper->get_option( 'form_id' );
		$this->source  = $helper->get_option( 'source' );

	}

	/**
	 * Register the admin styles for this plugin.
	 *
	 * @since 0.2.0
	 *
	 * @param $hook
	 */
	public function admin_enqueue_styles( $hook ) {

		// Enqueue scripts for plugin options page only.
		if ( 'settings_page_' . $this->plugin_slug != $hook ) {
			return;
		}

		wp_register_style(
			$this->plugin_name . '_admin',
			plugin_dir_url( __FILE__ ) . 'css/cleverreach-extension-admin.css',
			array(),
			$this->plugin_version,
			'all'
		);

		wp_enqueue_style( $this->plugin_name . '_admin' );

	}

	/**
	 * Register the localized admin scripts for this plugin.
	 *
	 * @since 0.2.0
	 *
	 * @param $hook
	 */
	public function admin_enqueue_scripts( $hook ) {

		// Enqueue scripts for plugin options page only.
		if ( 'settings_page_' . $this->plugin_slug != $hook ) {
			return;
		}

		wp_register_script(
			$this->plugin_name . '_admin',
			plugin_dir_url( __FILE__ ) . 'js/cleverreach-extension-admin.js',
			array( 'jquery' ),
			$this->plugin_version,
			true
		);

		wp_localize_script(
			$this->plugin_name . '_admin', 'cre_admin',
			array(
				'ajaxurl'            => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'              => wp_create_nonce( $this->plugin_name . '_admin_ajax_interaction_nonce' ),
				'selector'           => esc_attr( '.' ), // Selector supports only classes, yet.
				'container_selector' => sanitize_html_class( 'cre-admin-form-container' ),
				'response_selector'  => sanitize_html_class( 'cre-js-response' ),
				'key_selector'       => sanitize_html_class( 'cre-admin-input-key' ),
				'list_selector'      => sanitize_html_class( 'cre-admin-select-list' ),
				'form_selector'      => sanitize_html_class( 'cre-admin-select-form' ),
				'source_selector'    => sanitize_html_class( 'cre-admin-input-source' ),
				'list_empty'         => esc_html__( 'Please select a list', 'cleverreachextension' ),
				'form_empty'         => esc_html__( 'Please select a form', 'cleverreachextension' ),
				'shortcode_selector' => sanitize_html_class( 'cre-admin-shortcode' )
			)
		);

		wp_enqueue_script( $this->plugin_name . '_admin' );

	}

	/**
	 * Save data and return API response as JSON.
	 *
	 * @since 0.2.0
	 *
	 * @return array Return JSON `$result` with API response.
	 */
	public function admin_ajax_controller_interaction() {

		check_ajax_referer( $this->plugin_name . '_admin_ajax_interaction_nonce', 'nonce' );

		if ( $_POST['cr_admin_form'] && current_user_can( 'manage_options' ) ) { // Requires $_POST from admin user.

			// Create or update database entry.
			update_option( 'cleverreach_extension', $this->sanitize( $_POST['cr_admin_form'] ) );

			$result  = array();
			$client = new Api\Cleverreach();
			$helper = new Core\Cre_Helper();

			// Add status and list options to result.
			if ( $client->has_valid_api_key() ) {
				$result['status'] = 'success';

				$group = new Api\Cleverreach_Group_Adapter( $client );
				$result['list_options'] = $helper->parse_list( $group->get_list(), 'list_id' );
			} else {
				$result['status'] = 'error';
			}

			// Add form options to result.
			if ( $client->has_valid_api_key() && $helper->has_option( 'list_id' ) ) {
				$form = new Api\Cleverreach_Form_Adapter( $client );
				$result['form_options'] = $helper->parse_list( $form->get_list( $helper->get_option( 'list_id' ) ), 'form_id' );
			}

			// Add shortcode value to result.
			if ( $client->has_valid_api_key() && $helper->has_option( 'form_id' ) ) {
				$result['shortcode_id'] = $helper->get_option( 'form_id' );
			}

			// Finally return JSON result.
			$result = json_encode( $result );
			echo $result;
			die();
		}

	}

	/**
	 * Add custom action links to plugins page.
	 *
	 * @since 0.1.0
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . esc_html__( 'Settings', 'cleverreachextension' ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Add plugin options page.
	 *
	 * @since 0.1.0
	 */
	public function add_options_page() {

		add_options_page(
			$this->plugin_name,
			$this->plugin_name,
			'manage_options', // Requires admin user.
			$this->plugin_slug,
			array( $this, 'render_options_page' )
		);

	}

	/**
	 * Render options page.
	 *
	 * @since 0.1.0
	 */
	public function render_options_page() {

		echo '<div class="wrap cleverreach-extension-options-page">';
		echo '<h1>' . esc_html( $this->plugin_name ) . '</h1>';

		echo wp_kses(
			$this->render_promotion_notice(),
			array(
				'p'   => array(),
				'a'   => array(
					'href' => array()
				),
				'img' => array(
					'src' => array()
				)
			)
		);

		echo '<div class="cre-admin-form-container">';
		echo '<form method="post" action="options.php">';

		settings_fields( 'cleverreach_extension_group' );
		do_settings_sections( $this->plugin_slug );

		submit_button();

		echo '</form>';
		echo '</div>'; // end of .cre-admin-form-container

		echo wp_kses(
			$this->render_shortcode_preview(),
			array(
				'h3'   => array(),
				'p'    => array(),
				'span' => array(
					'class' => array()
				),
				'br'   => array(),
				'code' => array()
			)
		);

		echo '</div>'; // end of .wrap

	}

	/**
	 * Register settings via WordPress Settings Api
	 *
	 * @since 0.1.0
	 * @see   https://codex.wordpress.org/Settings_API
	 */
	public function register_settings() {

		register_setting(
			'cleverreach_extension_group',
			'cleverreach_extension',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'cleverreach_extension_setting',
			esc_html__( 'Settings', 'cleverreachextension' ),
			array( $this, 'render_section_info' ),
			$this->plugin_slug
		);

		add_settings_field(
			'api_key',
			esc_html__( 'API Key', 'cleverreachextension' ),
			array( $this, 'render_api_key_field' ),
			$this->plugin_slug,
			'cleverreach_extension_setting'
		);

		add_settings_field(
			'list_id',
			esc_html__( 'List', 'cleverreachextension' ),
			array( $this, 'render_list_field' ),
			$this->plugin_slug,
			'cleverreach_extension_setting'
		);

		add_settings_field(
			'form_id',
			esc_html__( 'Form', 'cleverreachextension' ),
			array( $this, 'render_form_field' ),
			$this->plugin_slug,
			'cleverreach_extension_setting'
		);

		add_settings_field(
			'source',
			esc_html__( 'Source', 'cleverreachextension' ),
			array( $this, 'render_source_field' ),
			$this->plugin_slug,
			'cleverreach_extension_setting'
		);

	}

	/**
	 * Prepare input to be saved in database.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function sanitize( $input ) {

		$new_input = array();

		if ( isset( $input['api_key'] ) ) {
			$new_input['api_key'] = sanitize_key( trim( $input['api_key'] ) );
		}

		if ( isset( $input['list_id'] ) ) {
			$new_input['list_id'] = sanitize_key( absint( trim( $input['list_id'] ) ) );
		}

		if ( isset( $input['form_id'] ) ) {
			$new_input['form_id'] = sanitize_key( absint( trim( $input['form_id'] ) ) );
		}

		if ( isset( $input['source'] ) ) {
			$new_input['source'] = sanitize_text_field( trim( $input['source'] ) );
		}

		return $new_input;

	}

	/**
	 * Render Api Key input field and description.
	 *
	 * @since 0.1.0
	 */
	public function render_api_key_field() {

		echo '<div class="cre-input-container">';

		$client  = new Api\Cleverreach();
		printf(
			'<input type="text" class="cre-admin-input-key" size="45" name="cleverreach_extension[api_key]" value="%s" /><div class="dashicons-before cre-info-message %s cre-js-response"></div>',
			isset( $this->api_key ) ? esc_attr( $this->api_key ) : '',
			$client->has_valid_api_key() ? 'confirmed' : 'invalid'
		);

		echo '</div>'; // end of .input-container

		echo '<p class="description">' . esc_html__( 'CleverReach API Keys can be created within Account » Extras » API', 'cleverreachextension' ) . '</p>';

	}

	/**
	 * Render list input field and description.
	 *
	 * @since 0.1.0
	 */
	public function render_list_field() {

		$html = '<select class="cre-admin-select-list" name="cleverreach_extension[list_id]">';

		$client = new Api\Cleverreach();
		$helper = new Core\Cre_Helper();
		if ( $client->has_valid_api_key() ) {

			$group = new Api\Cleverreach_Group_Adapter( $client );
			$html .= $helper->parse_list_html(
				$this->list_id,
				$group->get_list(),
				'list_id',
				esc_html__( 'Please select a list', 'cleverreachextension' )
			);

		}

		$html .= '</select>';

		echo wp_kses( $html, $helper->allowed_html_select() );

	}

	/**
	 * Render form input field and description.
	 *
	 * @since 0.1.0
	 */
	public function render_form_field() {

		$html = '<select class="cre-admin-select-form" name="cleverreach_extension[form_id]">';

		$client = new Api\Cleverreach();
		$helper = new Core\Cre_Helper();
		if ( $client->has_valid_api_key() && $helper->has_option( 'list_id' ) ) {

			$form = new Api\Cleverreach_Form_Adapter( $client );
			$html .= $helper->parse_list_html(
				$this->form_id,
				$form->get_list( $this->list_id ),
				'form_id',
				esc_html__( 'Please select a form', 'cleverreachextension' )
			);

		}

		$html .= '</select>';

		echo wp_kses( $html, $helper->allowed_html_select() );

	}

	/**
	 * Render source input field and description.
	 *
	 * @since 0.1.0
	 */
	public function render_source_field() {

		$source = $this->source;
		printf(
			'<input type="text" class="cre-admin-input-source" size="45" name="cleverreach_extension[source]" value="%s" />',
			isset( $source ) ? esc_attr( $source ) : ''
		);

		echo '<p class="description">' . esc_html__( '(optional)', 'cleverreachextension' ) . '</p>';

	}

	/**
	 * Render section into message.
	 *
	 * @since 0.1.0
	 */
	public function render_section_info() {

		echo '<p>' . esc_html__( 'Use the fields below to connect WordPress and CleverReach.', 'cleverreachextension' ) . '</p>';

	}

	/**
	 * Render promotion section.
	 * For users without api key only.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function render_promotion_notice() {

		$result = '';

		$helper = new Core\Cre_Helper();

		if ( ! $helper->has_option( 'api_key' ) ) {

			$result .= '<p>';
			$result .= esc_html__( 'Still need a CleverReach account?', 'cleverreachextension' ) . ' ';
			$result .= '<a href="' . esc_url( 'http://www.cleverreach.com/frontend/account.php?rk=85097mbwkysub"' ) . '">';
			$result .= esc_html__( 'Sign up for free!', 'cleverreachextension' );
			$result .= '</a>';
			$result .= '</p>';

			$result .= '<p><a href="' . esc_url( 'http://www.cleverreach.com/frontend/account.php?rk=85097mbwkysub"' ) . '">';
			$result .= '<img src="http://s3-eu-west-1.amazonaws.com/cloud-files.crsend.com/img/affiliate/en/468_60.png" />';
			$result .= '</a></p>';

		}

		return $result;

	}

	/**
	 * Render shortcode preview section.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function render_shortcode_preview() {

		$result = '<h3>' . esc_html__( 'Your Shortcode', 'cleverreachextension' ) . '</h3>';

		$result .= '<p>';
		$result .= esc_html__( 'You can use the shortcode below everywhere on your page.', 'cleverreachextension' ) . '<br />';
		$result .= esc_html__( 'Check the wiki on how to customize your form.', 'cleverreachextension' );
		$result .= '</p>';

		$result .= '<p>';
		$shortcode_id = $this->form_id ? $this->form_id : '';
		$result .= '<code>[cleverreach_extension form_id="<span class="cre-admin-shortcode">' . $shortcode_id . '</span>"]</code>';
		$result .= '</p>';

		return $result;

	}

}