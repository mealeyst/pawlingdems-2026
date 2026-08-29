<?php
/**
 * Provides the AS-element Blueprint.
 *
 * @package Blueprint
 */

namespace Ionos\Blueprint\Model;

/**
 * Blueprint Main Class
 */
class Blueprint extends Collection {
	protected $AT_SIGNcontext = array( //phpcs:ignore WordPress.NamingConventions
		'https://www.w3.org/ns/activitystreams',
		array(
			'bp'               => 'https://wpblueprints.org/namespace',
			'schema'           => 'https://schema.org/version/latest/schemaorg-current-https.jsonld',
			'WebApplication'   => 'schema:WebApplication',
			'Blueprint'        => 'bp:Blueprint',
			'Install'          => 'bp:Install',
			'applicationSuite' => 'schema:applicationSuite',
		),

	);
	protected $type    = array( 'Collection', 'Blueprint' );
	protected $content = 'WP Blueprint';
	protected $generator;

	/**
	 * Constructor. Needed, because parent would need parameter
	 */
	public function __construct() {
	}

	/**
	 * Set Generator
	 *
	 * @param object $application Application-Object.
	 * @return void
	 */
	public function set_generator( $application ) {
		$this->generator = $application;
	}

}
