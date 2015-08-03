<?php

namespace CleverreachExtension\Core\Api;

/**
 * Group adapter for CleverReach Api.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes/api
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
class Cleverreach_Group_Adapter implements Group_Adapter {

	private $cleverreach;

	public function __construct( Cleverreach $cleverreach ) {

		$this->cleverreach = $cleverreach;

	}

	/**
	 * Return list of available groups.
	 *
	 * @since 0.1.0
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