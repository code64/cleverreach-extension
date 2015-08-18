<?php
/**
 * Visual Composer Integration
 * Hooks into `cleverreach_extension` shortcode
 *
 * @since 0.2.0
 *
 * Optimized for Visual Composer v4.6.2
 * @link http://vc.wpbakery.com/
 * @docs https://wpbakery.atlassian.net/wiki/display/VC/Visual+Composer+Pagebuilder+for+WordPress
 */

use CleverreachExtension\Core;
use CleverreachExtension\Core\Api;

$values = array();
$client = new Api\Cleverreach();
$helper = new Core\Cre_Helper();

if ( $client->has_valid_api_key() && $helper->has_option( 'list_id' ) ) {
	$form = new Api\Cleverreach_Form_Adapter( $client );
	$forms = $helper->parse_list( $form->get_list( $helper->get_option( 'list_id' ) ), 'form_id' );

	// Prepare dropdown list in terms of Visual Composer.
	foreach ( $forms as $form ) {
		$values[ $form['name'] ] = $form['id'];
	}

	// Append `Custom` at the very end of the list.
	$values[ esc_html__( 'Custom', 'cleverreachextension' ) ] = 'custom';
}

vc_map(
	array(
		'name'              => esc_html__( 'CleverReach Form', 'cleverreachextension' ),
		'base'              => 'cleverreach_extension',
		'class'             => 'cleverreach_extension',
		'icon'              => '',
		'category'          => esc_html__( 'CleverReach', 'cleverreachextension' ),
		'admin_enqueue_js'  => array(),
		'admin_enqueue_css' => array(),
		'params'            => array(
			array(
				'type'        => 'dropdown',
				'holder'      => 'div',
				'class'       => 'cre_form_id',
				'heading'     => esc_html__( 'Form', 'cleverreachextension' ),
				'param_name'  => 'form_id',
				'value'       => $values,
				'std'         => $helper->get_option( 'form_id' ), // Use default value from options.
				'description' => esc_html__( 'Check the wiki on how to customize your form.', 'cleverreachextension' ),
			),
		)
	)
);