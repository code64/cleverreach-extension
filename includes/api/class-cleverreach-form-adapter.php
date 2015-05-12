<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

class Cleverreach_Form_Adapter implements Form_Adapter {

	private $cleverreach;

	public function __construct(Cleverreach $cleverreach) {

		$this->cleverreach = $cleverreach;

	}

	/**
	 * Returns a list of available forms for the given group
	 *
	 * @param $group_id
	 *
	 * @return string
	 */
	public function get_list( $group_id ) {

		try {
			$result = $this->cleverreach->api_get( 'formsGetList', $group_id );
		} catch ( \Exception $e ) {
			$result = $e->getMessage();
		}

		return $result;

	}

	/**
	 * Returns the HTML code for the given embedded form.
	 *
	 * @param $form_id
	 *
	 * @return string
	 */
	public function get_code( $form_id ) {

		try {
			$result = $this->cleverreach->api_get( 'formsGetCode', $form_id );
		} catch ( \Exception $e ) {
			$result = $e->getMessage();
		}

		return $result;

	}

	/**
	 * Returns the embedded form HTML code, CSS styles and javascript for the given.
	 *
	 * @param $form_id
	 *
	 * @return string
	 */
	public function get_embedded_code( $form_id ) {

		try {
			$result = $this->cleverreach->api_get( 'formsGetEmbeddedCode', $form_id );
		} catch ( \Exception $e ) {
			$result = $e->getMessage();
		}

		return $result;

	}

	/**
	 * Will send the activation mail to the given email.
	 *
	 * @param $form_id
	 * @param $email
	 * @param $data
	 *
	 * @return string
	 */
	public function send_activation_mail( $form_id, $email, $data ) {

		try {
			$result = $this->cleverreach->api_send_mail( 'formsSendActivationMail', $form_id, $email, $data );
		} catch ( \Exception $e ) {
			$result = $e->getMessage();
		}

		return $result;

	}

}