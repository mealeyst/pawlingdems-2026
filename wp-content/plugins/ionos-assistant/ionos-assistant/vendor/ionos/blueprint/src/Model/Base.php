<?php
/**
 * Provides the base class for all AS-elements.
 *
 * @package Blueprint
 */

namespace Ionos\Blueprint\Model;

/**
 * Base class. Inherited by any AS2-object.
 */
class Base implements \JsonSerializable {
	protected $type;

	/**
	 * Set classname as type. Base class is heavily inherited.
	 */
	public function __construct() {
		$reflect = new \ReflectionClass( $this );

		$this->type = $reflect->getShortName();
	}

	/**
	 * Serializes protected and private properties as well
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		$vars = get_object_vars( $this );

		if ( in_array( 'AT_SIGNcontext', array_keys( $vars ), true ) ) {
			$vars = array( '@context' => $vars['AT_SIGNcontext'] ) + $vars;
			unset( $vars['AT_SIGNcontext'] );
		}
		return $vars;
	}

	/**
	 * Grant access to protected and private properties
	 *
	 * @param mixed $var The property to get.
	 * @return mixed
	 */
	public function __get( $var ) {
		return $this->$var;
	}
}
