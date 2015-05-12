<?php
/**
 * Visual Composer Plugin
 * @link http://vc.wpbakery.com/
 * @docs https://wpbakery.atlassian.net/wiki/display/VC/Visual+Composer+Pagebuilder+for+WordPress
 */

// TODO: Move to class/adapter?

vc_map(
	array(
		'name'              => 'CleverReach Form',
		'base'              => 'cleverreach_interface',
		'class'             => 'cleverreach_interface',
		'icon'              => '',
		'category'          => 'Custom',
		'admin_enqueue_js'  => array(),
		'admin_enqueue_css' => array(),
		'params'            => array(
			/*
			array(
				'type'        => 'textfield',
				'holder'      => 'div',
				'class'       => '',
				'heading'     => __( 'Headline', 'js_composer' ),
				'param_name'  => 'cr_headline',
				'value'       => '',
				'description' => '',
			),
			array(
				'type'        => 'textfield',
				'holder'      => 'div',
				'class'       => '',
				'heading'     => __( 'Form URL', 'js_composer' ),
				'param_name'  => 'cr_url',
				'value'       => '',
				'description' => '',
			),
			array(
				'type'        => 'textfield',
				'holder'      => 'div',
				'class'       => '',
				'heading'     => __( 'Submit', 'js_composer' ),
				'param_name'  => 'cr_submit',
				'value'       => '',
				'description' => '',
			),
			array(
				'type'        => 'textfield',
				'holder'      => 'div',
				'class'       => '',
				'heading'     => __( 'Form', 'js_composer' ),
				'param_name'  => 'form',
				'value'       => '',
				'description' => '',
			),
			*/
		)
	)
);