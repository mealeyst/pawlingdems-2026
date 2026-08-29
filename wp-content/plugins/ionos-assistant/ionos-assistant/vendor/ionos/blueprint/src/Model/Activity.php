<?php
/**
 * Provides the AS-element Activity.
 *
 * @package Blueprint
 */

namespace Ionos\Blueprint\Model;

/**
 * AS2-object Activity.
 */
class Activity extends Base {
	protected $type;
	protected $summary;
	protected $object;

	/**
	 * Sets type and object
	 *
	 * @param string $type AS2-type.
	 * @param object $object Object, on which activity takes place.
	 */
	public function __construct( $type, $object ) {
		$this->type    = $type;
		$this->summary = sprintf(
			'%s %s: %s',
			join( ', ', (array) $this->type ),
			join( ', ', (array) $object->applicationCategory ),
			$object->abstract
		);
		$this->object  = $object;
	}

}
