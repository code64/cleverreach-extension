<?php

namespace CleverreachExtension\Core\Api;

/**
 * Form interface according to CleverReach Api.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes/api
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
interface Form_Adapter {

	public function get_list( $group_id );

	public function get_code( $form_id );

	public function get_embedded_code( $form_id );

	public function send_activation_mail( $form_id, $email, $data );

}