<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

/**
 * Class to connect to CleverReach using the CleverReach Api.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes/api
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
class Cleverreach {

	/**
	 * Define connection via SOAP client and Api Key.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		$this->client  = new \SoapClient( 'http://api.cleverreach.com/soap/interface_v5.1.php?wsdl' );
		$this->api_key = $this->get_option( 'api_key' );

		return $this;

	}

	/**
	 * Get option value from database.
	 *
	 * @since 0.1.0
	 *
	 * @param $option
	 *
	 * @return string
	 */
	public function get_option( $option ) {

		$option_group = get_option( 'cleverreach_extension' );
		if ( isset( $option_group[ $option ] ) ) {
			$option = $option_group[ $option ];
		} else {
			$option = '';
		}

		return $option;

	}

	/**
	 * Checks if `$option` is valid.
	 *
	 * @since 0.1.0
	 *
	 * @param string $option
	 *
	 * @return bool
	 */
	public function has_option( $option ) {

		$result = $this->get_option( $option );
		$status = false;

		if ( isset( $result ) && ! empty( $result ) ) {
			$status = true;
		}

		return $status;

	}

	/**
	 * Checks if Api Key is valid.
	 *
	 * @since 0.1.0
	 * @return bool
	 */
	public function has_valid_api_key() {

		$result = $this->client->clientGetDetails( $this->api_key );
		$status = false;

		if ( 'SUCCESS' == $result->status ) {
			$status = true;
		}

		return $status;

	}

	/**
	 * Retrieve data via CleverReach Api.
	 *
	 * @since 0.1.0
	 *
	 * @param string $method
	 * @param string $param
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function api_get( $method = 'clientGetDetails', $param = '' ) {

		$result = $this->client->$method( $this->api_key, $param );

		if ( 'SUCCESS' != $result->status ) {
			throw new \Exception( esc_html__( 'Your API key is invalid.', 'cleverreachextension' ) );
		}

		return $result;

	}

	/**
	 * Post data via CleverReach Api.
	 *
	 * @since 0.1.0
	 *
	 * @param       $method
	 * @param array $param
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function api_post( $method, $param = array() ) {

		$result = $this->client->$method( $this->api_key, $this->get_option( 'list_id' ), $param );

		if ( 'SUCCESS' != $result->status ) {
			throw new \Exception( esc_html__( $result->message ) );
		}

		return $result;

	}

	/**
	 * Send mail via CleverReach Api.
	 *
	 * @param $method
	 * @param $form_id
	 * @param $email
	 * @param $data
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function api_send_mail( $method, $form_id, $email, $data ) {

		$result = $this->client->$method( $this->api_key, $form_id, $email, $data );

		if ( 'SUCCESS' != $result->status ) {
			throw new \Exception( esc_html__( $result->message ) );
		}

		return $result;

	}

}