<?php
/**
 * Provides the AS-element Collection.
 *
 * @package Blueprint
 */

namespace Ionos\Blueprint\Model;

/**
 * AS-Type Collection
 */
class Collection extends Base {

	protected $type;
	protected $content;
	protected $totalItems = 0; //phpcs:ignore WordPress.NamingConventions
	protected $items      = array();


	/**
	 * Construct AS-2-Object
	 *
	 * @param string $content Content-field.
	 */
	public function __construct( $content ) {
		parent::__construct();
		$this->content = $content;
	}

	/**
	 * Add items
	 *
	 * @param mixed $item Item in collection.
	 * @return void
	 */
	public function add_item( $item ) {
		if ( empty( $item ) ) {
			return;
		}

		$this->items[] = $item;
		$this->totalItems++; //phpcs:ignore WordPress.NamingConventions
	}
}
