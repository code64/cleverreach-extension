<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

class Cleverreach {

	public function __construct() {

		$this->client  = new \SoapClient( 'http://api.cleverreach.com/soap/interface_v5.1.php?wsdl' );
		$this->api_key = $this->get_option( 'api_key' );

		return $this;

	}

	public function get_option( $option ) {

		$option_group = get_option( 'cleverreach_interface' );
		$option       = $option_group[ $option ];

		return $option;

	}

	public function has_option( $option ) {

		$result = $this->get_option( $option );
		$status = false;

		if ( isset( $result ) && ! empty( $result ) ) {
			$status = true;
		}

		return $status;

	}

	public function has_valid_api_key() {

		$result = $this->client->clientGetDetails( $this->api_key );
		$status = false;

		if ( 'SUCCESS' == $result->status ) {
			$status = true;
		}

		return $status;

	}

	public function api_get( $method = 'clientGetDetails', $param = '' ) {

		$result = $this->client->$method( $this->api_key, $param );

		if ( 'SUCCESS' != $result->status ) {
			throw new \Exception( esc_html__( 'Your API key is invalid.', 'cleverreachextension' ) );
		}

		return $result;

	}

	public function api_post( $method, $param = array() ) {

		$result = $this->client->$method( $this->api_key, $this->get_option( 'list_id' ), $param );

		if ( 'SUCCESS' != $result->status ) {
			throw new \Exception( esc_html__( $result->message ) );
		}

		return $result;

	}

	public function api_send_mail( $method, $form_id, $email, $data ) {

		$result = $this->client->$method( $this->api_key, $form_id, $email, $data );

		if ( 'SUCCESS' != $result->status ) {
			throw new \Exception( esc_html__( $result->message ) );
		}

		return $result;

	}

}