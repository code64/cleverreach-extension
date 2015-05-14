<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

/**
 * Group interface according to CleverReach Api.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes/api
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
interface Group_Adapter {

	public function get_list();

}