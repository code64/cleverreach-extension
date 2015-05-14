<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

/**
 * Receiver interface according to CleverReach Api.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes/api
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
interface Receiver_Adapter {

	public function add( $user );

}