<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class WC Multistore Export
 */
abstract class WC_Multistore_Export {

	/**
	 * @var mixed|string
	 */
	public $filename;

	/**
	 * @param string $filename
	 */
	public function __construct( $filename = "wc_multistore_export" ) {
		$this->filename = $filename;
	}

	/**
	 *
	 */
	public function initialize() {
		ob_start();
		$this->sendHttpHeaders();
		$this->write( $this->generateHeader() );
	}

	/**
	 * @param $row
	 */
	public function addRow( $row ) {
		$this->write( $this->generateRow( $row ) );
	}

	/**
	 *
	 */
	public function finalize() {
		$this->write( $this->generateFooter() );

		ob_flush();
	}

	/**
	 * @return mixed
	 */
	abstract public function sendHttpHeaders();

	/**
	 * @param $data
	 */
	protected function write( $data ) {
		echo $data;
	}

	/**
	 *
	 */
	protected function generateHeader() {
		// can be overridden by subclass to return any data that goes at the top of the exported file
	}

	/**
	 *
	 */
	protected function generateFooter() {
		// can be overridden by subclass to return any data that goes at the bottom of the exported file
	}

	// In subclasses generateRow will take $row array and return string of it formatted for export type

	/**
	 * @param $row
	 *
	 * @return mixed
	 */
	abstract protected function generateRow( $row );

}