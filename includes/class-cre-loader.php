<?php

namespace CleverreachExtension\Core;

defined( 'ABSPATH' ) or die();

/**
 * Register all actions and filters for the plugin.
 *
 * @since      0.1.0
 * @package    Cleverreach_Extension
 * @subpackage Cleverreach_Extension/includes
 * @author     Sven Hofmann <info@hofmannsven.com>
 */
class Cre_Loader {

	/**
	 * The array of actions registered.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array $actions The actions registered to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array $filters The filters registered to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since  0.1.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $hook          The name of the WordPress action that is being registered.
	 * @param  object $component     A reference to the instance of the object on which the action is defined.
	 * @param  string $callback      The name of the function definition on the `$component`.
	 * @param  int    $priority      The priority at which the function should be fired (optional).
	 * @param  int    $accepted_args The number of arguments that should be passed to the `$callback` (optional).
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $hook          The name of the WordPress filter that is being registered.
	 * @param  object $component     A reference to the instance of the object on which the filter is defined.
	 * @param  string $callback      The name of the function definition on the `$component`.
	 * @param  int    $priority      The priority at which the function should be fired (optional).
	 * @param  int    $accepted_args The number of arguments that should be passed to the `$callback` (optional).
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Register actions and hooks into a single collection.
	 *
	 * @since  0.1.0
	 * @access private
	 *
	 * @param  array  $hooks         The collection of hooks that is being registered (actions or filters).
	 * @param  string $hook          The name of the WordPress filter that is being registered.
	 * @param  object $component     A reference to the instance of the object on which the filter is defined.
	 * @param  string $callback      The name of the function definition on the `$component`.
	 * @param  int    $priority      The priority at which the function should be fired (optional).
	 * @param  int    $accepted_args The number of arguments that should be passed to the `$callback` (optional).
	 *
	 * @return array                      The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;

	}

	/**
	 * Run the filters and actions.
	 *
	 * @since 0.1.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array(
				$hook['component'],
				$hook['callback'],
			), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array(
				$hook['component'],
				$hook['callback'],
			), $hook['priority'], $hook['accepted_args'] );
		}

	}

}