<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

class Cleverreach_Receiver_Adapter implements Receiver_Adapter {

	private $cleverreach;

	public function __construct( Cleverreach $cleverreach ) {

		$this->cleverreach = $cleverreach;

	}

	/**
	 * Adds a new single receiver.
	 *
	 * @param $user
	 *
	 * @return string
	 */
	public function add( $user ) {

		try {
			$result = $this->cleverreach->api_post( 'receiverAdd', $user );
		} catch ( \Exception $e ) {
			$result = $e->getMessage();
		}

		return $result;

	}

}