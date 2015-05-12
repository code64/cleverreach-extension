<?php

namespace CleverreachExtension\Viewadmin;

use CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

class Cre_Admin {

	private $plugin_name;
	private $plugin_slug;
	private $plugin_version;

	public function __construct( $plugin_name, $plugin_slug, $plugin_version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;
		$this->plugin_version = $plugin_version;

	}

	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . esc_html__( 'Settings', 'cleverreachextension' ) . '</a>'
			),
			$links
		);

	}

	public function add_options_page() {

		add_options_page(
			$this->plugin_name,
			$this->plugin_name,
			'manage_options', // TODO: Check required user caps
			$this->plugin_slug,
			array( $this, 'render_options_page' )
		);

	}

	public function get_option( $option ) {

		$option_group = get_option( 'cleverreach_extension' );
		$option       = $option_group[ $option ];

		return $option;

	}

	public function render_options_page() {

		echo '<div class="wrap cleverreach-extension-options-page">';
		echo '<h2>' . esc_html__( $this->plugin_name ) . '</h2>';

		echo $this->render_promotion_notice();

		echo '<form method="post" action="options.php">';

		settings_fields( 'cleverreach_extension_group' );
		do_settings_sections( $this->plugin_slug );

		submit_button();

		echo '</form>';

		echo $this->render_shortcode_preview();

		echo '</div>'; // end of .wrap

	}

	// Register via Settings API
	// @see https://codex.wordpress.org/Settings_API
	public function register_settings() {

		register_setting(
			'cleverreach_extension_group',
			'cleverreach_extension',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'cleverreach_extension_setting',
			esc_html__( 'CleverReach Settings', 'cleverreachextension' ),
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

	}

	public function sanitize( $input ) {

		$new_input = array();
		if ( isset( $input['api_key'] ) ) {
			$new_input['api_key'] = esc_attr( $input['api_key'] ); // TODO: trim() input also $api->test_api_key()
		}

		if ( isset( $input['list_id'] ) ) {
			$new_input['list_id'] = esc_attr( $input['list_id'] ); // TODO: trim() input also $api->test_api_key()
		}

		if ( isset( $input['form_id'] ) ) {
			$new_input['form_id'] = esc_attr( $input['form_id'] ); // TODO: trim() input also $api->test_api_key() and absint
		}

		return $new_input;

	}

	public function render_api_key_field() {

		$api_key = $this->get_option( 'api_key' );
		printf(
			'<input type="text" id="api_key" name="cleverreach_extension[api_key]" value="%s" />',
			// isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : ''
			isset( $api_key ) ? esc_attr( $api_key ) : ''
		);

	}

	public function render_list_field() {

		$client = new Api\Cleverreach();

		if ( $client->has_valid_api_key() ) {

			$group = new Api\Cleverreach_Group_Adapter( $client );
			$list  = $group->get_list();
			$list_id = $this->get_option( 'list_id' );

			echo '<select id="list_id" name="cleverreach_extension[list_id]">';

			foreach ( $list->data as $list_item ) {
				$selected = ( $list_id == $list_item->id ) ? 'selected="selected" ' : '';
				echo '<option ' . esc_attr( $selected ) . 'value="' . esc_attr( $list_item->id ) . '" />' . esc_attr( $list_item->name ) . '</option>';
			}

			echo '</select>';

		}

	}

	public function render_form_field() {

		$client = new Api\Cleverreach();

		if ( $client->has_valid_api_key() ) {

			$form = new Api\Cleverreach_Form_Adapter( $client );
			$list = $form->get_list( $this->get_option( 'list_id' ) );
			$form_id = $this->get_option( 'form_id' );

			echo '<select id="form_id" name="cleverreach_extension[form_id]">';

			foreach ( $list->data as $list_item ) {
				$selected = ( $form_id == $list_item->id ) ? 'selected="selected" ' : '';
				echo '<option ' . esc_attr( $selected ) . 'value="' . esc_attr( $list_item->id ) . '" />' . esc_attr( $list_item->name ) . '</option>';
			}

			echo '</select>';

		}

	}

	public function render_section_info() {

		echo '<p>' . esc_html__( 'Please enter your CleverReach API Key.', 'cleverreachextension' ) . '<br />';
		echo esc_html__( 'CleverReach API Keys can be created within Account » Extras » API', 'cleverreachextension' ) . '</p>';

	}

	public function render_admin_notices() {

		if ( 'settings_page_' . $this->plugin_slug == get_current_screen()->id ) {

			$client = new Api\Cleverreach();
			// Check if there is an api_kay
			if ( $client->has_option( 'api_key' ) ) {
				// Check if api_key is valid
				if ( ! $client->has_valid_api_key() ) {
					echo '<div class="error"><p>' . esc_html__( 'Your API key is invalid.', 'cleverreachextension' ) . '</p></div>';
				} else {
					// Check if there is a list_id
					if ( ! $client->has_option( 'list_id' ) ) {
						echo '<div class="error"><p>' . esc_html__( 'Please select a list.', 'cleverreachextension' ) . '</p></div>';
					} else {
						// Check if there is a from_id
						if ( ! $client->has_option( 'form_id' ) ) {
							echo '<div class="error"><p>' . esc_html__( 'Please select a form.', 'cleverreachextension' ) . '</p></div>';
						} else {

						}
					}
				}
			}
		}

	}

	public function render_promotion_notice() {

		$client = new Api\Cleverreach();
		$result = '';
		if ( ! $client->has_option( 'api_key' ) ) {
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

	public function render_shortcode_preview() {

		$result = '<h3>' . esc_html__( 'Your Shortcode', 'cleverreachextension' ) . '</h3>';
		$result .= '<p>';
		$result .= esc_html__( 'You can use the shortcode below everywhere on your page.', 'cleverreachextension' ) . '<br />';
		$result .= esc_html__( 'Check the wiki on how to customize your form.', 'cleverreachextension' );
		$result .= '</p>';

		$result .= '<code>[cleverreach_extension form_id="' . $this->get_option( 'form_id' ) . '"]</code>';

		$result .= '</p>';

		return $result;

	}

}