<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

class Cleverreach_Group_Adapter implements Group_Adapter {

	private $cleverreach;

	public function __construct( Cleverreach $cleverreach ) {

		$this->cleverreach = $cleverreach;

	}

	/**
	 * Return list of available groups.
	 *
	 * @return string
	 */
	public function get_list() {

		try {
			$result = $this->cleverreach->api_get( 'groupGetList' );
		} catch ( \Exception $e ) {
			$result = $e->getMessage();
		}

		return $result;

	}

}