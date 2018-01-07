<?php
/**
 * Reports API - Endpoint View object
 *
 * @package     EDD
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2018, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */
namespace EDD\Admin\Reports\Data;

use EDD\Utils;

/**
 * Represents a data endpoint for the Reports API.
 *
 * @since 3.0
 */
class Endpoint {

	/**
	 * Endpoint ID.
	 *
	 * @since 3.0
	 * @var   string
	 */
	private $endpoint_id;

	/**
	 * Endpoint label.
	 *
	 * @since 3.0
	 * @var   string
	 */
	private $label;

	/**
	 * Endpoint type.
	 *
	 * @since 3.0
	 * @var   string
	 */
	private $type;

	/**
	 * Represents filters available to the endpoint.
	 *
	 * @since 3.0
	 */
	public $filters = array();

	/**
	 * Represents the callback used to retrieve data based on the set view type.
	 *
	 * @since 3.0
	 * @var   callable
	 */
	private $data_callback;

	/**
	 * Represents the display callback based on the set view type.
	 *
	 * @since 3.0
	 * @var   callable
	 */
	private $display_callback;

	/**
	 * Represents the display arguments (passed to the display callback) for the (view) type.
	 *
	 * @since 3.0
	 * @var   array
	 */
	private $display_args = array();

	/**
	 * Constructs the endpoint object.
	 *
	 * @since 3.0
	 *
	 * @see set_display_props()
	 *
	 * @param array  $endpoint Endpoint record from the registry.
	 * @param string $type     Endpoint view type. Determines which view attribute to
	 *                         retrieve from the corresponding endpoint registry entry.
	 */
	public function __construct( $endpoint, $type ) {
		$this->set_type( $type );

		if ( ! empty( $endpoint['id'] ) ) {
			$this->endpoint_id = $endpoint['id'];
		} else {
			// TODO: Decide on error handling.
		}

		if ( ! empty( $endpoint['label'] ) ) {
			$this->label = $endpoint['label'];
		} else {
			// TODO: Decide on error handling.
		}

		$this->set_display_props( $endpoint );
	}

	/**
	 * Retrieves the endpoint ID.
	 *
	 * @since 3.0
	 *
	 * @return string Endpoint ID.
	 */
	public function get_id() {
		return $this->endpoint_id;
	}

	/**
	 * Retrieves the global label for the current endpoint.
	 *
	 * @since 3.0
	 *
	 * @return string Endpoint string.
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Retrieves the endpoint (view) type.
	 *
	 * @since 3.0
	 *
	 * @return string Endpoint type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Retrieves the data for the endpoint (view) type.
	 *
	 * @since 3.0
	 *
	 * @return mixed Endpoint data.
	 */
	public function get_data() {
		if ( is_callable( $this->data_callback ) ) {
			$data = call_user_func( $this->data_callback );
		} else {
			$data = '';
		}

		/**
		 * Filters data for the current endpoint.
		 *
		 * @since 3.0
		 *
		 * @param mixed|string $data Endpoint data.
		 * @param \EDD\Admin\Reports\Data\Endpoint Endpoint object.
		 */
		return apply_filters( 'edd_reports_endpoint_data', $data, $this );
	}

	/**
	 * Retrieves the display callback for the endpoint (view) type.
	 *
	 * @since 3.0
	 *
 	 * @return mixed|void
	 */
	public function get_display_callback() {
		/**
		 * Filters the display callback for the current endpoint.
		 *
		 * @since 3.0
		 *
		 * @param callable $display_callback Display callback.
		 * @param \EDD\Admin\Reports\Data\Endpoint Endpoint object.
		 */
		return apply_filters( 'edd_reports_endpoint_display_callback', $this->display_callback, $this );
	}

	/**
	 * Retrieves the display arguments for the (view) type.
	 *
	 * @since 3.0
	 *
	 * @return array Display arguments.
	 */
	public function get_display_args() {
		return $this->display_args;
	}

	/**
	 * Sets the endpoint type.
	 *
	 * @since 3.0
	 *
	 * @param string $type Endpoint type.
	 */
	private function set_type( $type ) {
		$types = edd_get_reports_endpoint_views();

		if ( in_array( $type, $types, true ) ) {
			$this->type = $type;
		}
	}

	/**
	 * Sets display-related properties for the Endpoint.
	 *
	 * @since 3.0
	 *
	 * @param array $endpoint Endpoint record from the registry.
	 */
	protected function set_display_props( $endpoint ) {

		if ( ! empty( $endpoint[ $this->type ] ) ) {

			$view_atts = $endpoint[ $this->type ];

			if ( isset( $view_atts['display_args'] ) ) {
				$this->display_args = $view_atts['display_args'];
			} else {
				// TODO: Decide on error handling.
			}

			if ( isset( $view_atts['display_callback'] ) ) {
				$this->display_callback = $view_atts['display_callback'];
			} else {
				// TODO: Decide on error handling.
			}

			if ( isset( $view_atts['data_callback'] ) ) {
				$this->data_callback = $view_atts['data_callback'];
			} else {
				// TODO: Decide on error handling.
			}

		} else {
			// TODO: Decide on error handling.
		}
	}

	/**
	 * Displays the endpoint based on the (view) type.
	 *
	 * @since 3.0
	 *
	 * @return void
	 */
	public function display() {
		$callback = $this->get_display_callback();

		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, array(
				'data' => $this->get_data(),
				'args' => $this->get_display_args(),
			) );
		}
	}

}
