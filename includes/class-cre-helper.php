<?php

namespace CleverreachExtension\Core;

/**
 * Helper for plugin settings and interactions.
 *
 * @since      0.2.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
class Cre_Helper {

	/**
	 * Get option value from database.
	 *
	 * @since 0.2.0
	 *
	 * @param $option
	 *
	 * @return string
	 */
	public function get_option( $option ) {

		$option_group = get_option( 'cleverreach_extension' );
		if ( isset( $option_group[ $option ] ) ) {
			$option = $option_group[ $option ];
		} else {
			$option = '';
		}

		return $option;

	}

	/**
	 * Checks if `$option` is valid.
	 *
	 * @since 0.2.0
	 *
	 * @param string $option
	 *
	 * @return bool
	 */
	public function has_option( $option ) {

		$result = $this->get_option( $option );
		$status = false;

		if ( isset( $result ) && ! empty( $result ) ) {
			$status = true;
		}

		return $status;

	}

	/**
	 * Parse list options with meta data.
	 *
	 * @since  0.2.0
	 *
	 * @param $list
	 * @param $option
	 *
	 * @access protected
	 *
	 * @return array
	 */
	public function parse_list( $list, $option ) {

		$options = array();
		$list_id = $this->get_option( $option );

		foreach ( $list->data as $list_item ) {
			$selected  = ( $list_id == $list_item->id ) ? true : false;
			$options[] = array(
				'id'       => esc_attr( $list_item->id ),
				'name'     => esc_attr( $list_item->name ),
				'selected' => $selected,
			);
		}

		return $options;

	}

	/**
	 * Parse list with meta data as html options to use within `select`.
	 *
	 * @since  0.2.0
	 *
	 * @param $id
	 * @param $list
	 * @param $option
	 * @param $empty String
	 *
	 * @return string
	 */
	public function parse_list_html( $id, $list, $option, $empty ) {

		$html = '<option value="">' . $empty . '</option>';

		$options = $this->parse_list( $list, $option );
		foreach ( $options as $option ) {
			$selected = ( $id == $option['id'] ) ? 'selected ' : '';
			$html .= '<option ' . esc_attr( $selected ) . 'value="' . esc_attr( $option['id'] ) . '" />' . esc_attr( $option['name'] ) . '</option>';
		}

		return $html;

	}

	/**
	 * Allowed tags for `wp_kses()` select
	 *
	 * @since  0.2.0
	 * @see https://codex.wordpress.org/Function_Reference/wp_kses
	 *
	 * @return array
	 */
	public function allowed_html_select() {

		return array(
			'select' => array(
				'class' => array(),
				'name'  => array()
			),
			'option' => array(
				'value'    => array(),
				'selected' => array()
			)
		);

	}

}