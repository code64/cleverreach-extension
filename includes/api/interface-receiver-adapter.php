<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

interface Receiver_Adapter {

	public function add( $user );

}