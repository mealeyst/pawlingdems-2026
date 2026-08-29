<?php
/**
 * Provides the Schema-org-element WebApplication.
 *
 * @package Blueprint
 */

namespace Ionos\Blueprint\Model;

/**
 * AS2-object application.
 */
class WebApplication extends Base {
	protected $type;
	protected $name;
	protected $abstract;
	protected $version;
	protected $applicationCategory;
	protected $applicationSuite = 'WordPress';


	/**
	 * Defines an application and set the type.
	 *
	 * @param object $data Application-Object.
	 * @param string $type AS-Type.
	 */
	public function __construct( $data, $type = 'Application' ) {
		if ( ! is_array( $data ) ) {
			return null;
		}
		parent::__construct();

		$this->applicationCategory = $type;
		$this->name                = $data['name'];
		$this->version             = $data['version'];

		if ( isset( $data['title'] ) ) {
			$this->abstract = $data['title'];
		}

	}
}
