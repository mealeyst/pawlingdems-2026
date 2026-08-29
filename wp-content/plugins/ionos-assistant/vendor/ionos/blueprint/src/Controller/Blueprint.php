<?php
/**
 * Provides the AS-element Blueprint.
 *
 * @package Blueprint
 */

namespace Ionos\Blueprint\Controller;

use Opis\JsonSchema\Validator;
use Opis\JsonSchema\Errors\ErrorFormatter;

/**
 * Blueprint Main Class
 */
class Blueprint {
	protected $data;
	protected $debug = false;

	/**
	 * Constructor. Initializes blueprint data object.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = array() ) {
		$this->data = new \Ionos\Blueprint\Model\Blueprint();

		if ( isset( $args['debug'] ) && (bool) $args['debug'] === true ) {
			$this->debug = true;
		}
	}

	/**
	 * Returns php value with blueprint info.
	 *
	 * @return object
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Returns the json-string with the blueprint.
	 * Return false if failed.
	 *
	 * @return string
	 */
	public function encode() {
		return json_encode( $this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	/**
	 * Reads a given file
	 *
	 * @param string $file Path to json-file.
	 * @return object|bool Returns bueprint-object or false. If failed, use get_error()
	 */
	public function decode_file( $file ) {
		if ( ! is_readable( $file ) ) {
			$this->set_error( sprintf( 'File %s not found', $file ) );
			return false;
		}

		return $this->decode( file_get_contents( $file ) );
	}

	/**
	 * Reads json from a string
	 *
	 * @param string $json json-String with blueprint info.
	 * @return object|bool Returns bueprint-object or false. If failed, use get_error()
	 */
	public function decode( $json ) {
		$json = json_decode( $json );

		if ( ! $this->is_valid( $json ) ) {
			$this->set_error( 'json-file not valid.' );
			return false;
		}

		return $json;
	}

	/**
	 * Checks json again blueprint schema
	 *
	 * @param object $json Blueprint data as php value.
	 * @return boolean
	 */
	private function is_valid( $json ) {
		$validator = new Validator();
		$validator->resolver()->registerFile(
			'https://wpblueprints.org',
			__DIR__ . '/../../schema/blueprint.json'
		);

		$result = $validator->validate( $json, 'https://wpblueprints.org' );

		if ( true === $this->debug && ! empty( $result->error() ) ) {
			print_r( ( new ErrorFormatter() )->format( $result->error() ) ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}

		return $result->isValid();
	}

	/**
	 * Returns error message.
	 *
	 * @return string
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Sets error message.
	 *
	 * @param string $message The error message.
	 */
	private function set_error( $message ) {
		$this->error = $message;
	}

}
