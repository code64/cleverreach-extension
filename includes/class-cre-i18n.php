<?php

namespace CleverreachExtension\Core;

defined( 'ABSPATH' ) or die();

class Cre_i18n {

	private $domain;

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}