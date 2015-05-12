<?php

namespace CleverreachExtension\Core\Api;

defined( 'ABSPATH' ) or die();

interface Form_Adapter {

	public function get_list( $group_id );

	public function get_code( $form_id );

	public function get_embedded_code( $form_id );

	public function send_activation_mail( $form_id, $email, $data );

}