<?php

namespace CleverreachExtension\Viewpublic;

use CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

class Cre_Public {

	private $plugin_name;
	private $plugin_slug;
	private $version;

	public function __construct( $plugin_name, $plugin_slug, $version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;
		$this->version     = $version;

	}

	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cleverreach-extension-public.js', array( 'jquery' ), $this->version, true );

		wp_localize_script( $this->plugin_name, 'cre', array(
			'ajaxurl'            => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'              => wp_create_nonce( $this->plugin_name . '_ajax_interaction_nonce' ),
			'loading'            => sanitize_text_field( apply_filters( 'cleverreach_extension_loading_msg', esc_html__( 'Saving...', 'cleverreachextension' ) ) ),
			'success'            => sanitize_text_field( apply_filters( 'cleverreach_extension_success_msg', esc_html__( 'Please check your email to confirm your subscription.', 'cleverreachextension' ) ) ),
			'error'              => sanitize_text_field( apply_filters( 'cleverreach_extension_error_msg', esc_html__( 'Sorry, there was a problem saving your data. Please try later or contact the administrator.', 'cleverreachextension' ) ) ),
			'selector'           => esc_attr( '.' ), // Selector supports only classes, yet
			'container_selector' => sanitize_html_class( apply_filters( 'cleverreach_extension_container_selector', 'cr_form-container' ) ),
			'loading_selector'   => sanitize_html_class( apply_filters( 'cleverreach_extension_loading_selector', 'cr_loading' ) ),
			'success_selector'   => sanitize_html_class( apply_filters( 'cleverreach_extension_success_selector', 'cr_success' ) ),
			'response_selector'  => sanitize_html_class( apply_filters( 'cleverreach_extension_response_selector', 'cr_response' ) ),
			'error_selector'     => sanitize_html_class( apply_filters( 'cleverreach_extension_error_selector', 'cr_error' ) )
		) );

		wp_enqueue_script( $this->plugin_name );

	}

	public function ajax_controller_interaction() {
		check_ajax_referer( $this->plugin_name . '_ajax_interaction_nonce', 'nonce' );

		$result = $post_attr = array();

		// Parse serialized ajax post data as `$post` (array)
		parse_str( $_POST['cr_form'], $post );

		if ( is_email( $post['email'] ) ) :

			// Prepare receiver adapter
			$client   = new Api\Cleverreach();
			$receiver = new Api\Cleverreach_Receiver_Adapter( $client );

			// Populate `$post_attr` (array) according to CleverReach API defaults
			foreach ( $post as $key => $value ) {

				if ( 'email' != $key ) { // Skip 'email' as this is not needed as separate attribute
					// Attribute `$key` may only contain lowercase a-z and 0-9. Everything else will be converted to "_".
					array_push( $post_attr, array( 'key' => sanitize_html_class( $key ), 'value' => sanitize_text_field( $value ) ) );
				}

			}

			// Populate `$user` (array) according to CleverReach API defaults
			$user = array(
				'email'      => sanitize_email( $post['email'] ),
				'registered' => time(),
				// 'activated' => time(), // Force double opt-in
				'source'     => get_bloginfo( 'name' ), // TODO: Add fitler and esc
				'attributes' => $post_attr
			);
			$receiver_added = $receiver->add( $user );

			// Test returned data
			if ( is_object( $receiver_added ) && 'SUCCESS' == $receiver_added->status ) {

				$result['type'] = 'success';

				// Prepare form adapter
				$form = new Api\Cleverreach_Form_Adapter( $client );

				// Send activation mail
				$user_data = array(
					'user_ip'    => '127.0.0.1', // Fake IP
					'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:14.0) Gecko/20100101 Firefox/14.0.1', // Also fake user agent
					'referer'    => esc_url( home_url() ),
				);

				$form->send_activation_mail( $client->get_option( 'form_id' ), sanitize_email( $post['email'] ), $user_data );

			} else {

				$result['type'] = 'error';

				if ( 'duplicate data' == $receiver_added ) {
					$result['status'] = sanitize_text_field( apply_filters( 'cleverreach_extension_error_msg_duplicate', esc_html__( 'It seems like you\'re already subscribed to our list.', 'cleverreachextension' ) ) );
				} else {
					$result['status'] = sanitize_text_field( apply_filters( 'cleverreach_extension_error_msg_common', esc_html__( 'Sorry, there seems to be a problem with your data.', 'cleverreachextension' ) ) );
				}

			} // end of is_object() && 'SUCCESS'

		else :

			$result['type']   = 'error';
			$result['status'] = sanitize_text_field( apply_filters( 'cleverreach_extension_error_msg_invalid_email', esc_html__( 'Sorry, there seems to be a problem with your email address.', 'cleverreachextension' ) ) );

		endif; // end of is_email()

		// Finally return JSON result
		$result = json_encode( $result );
		echo $result;
		die();

	}

}