<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class WC Multistore Export CSV
 */
class WC_Multistore_Export_Csv extends WC_Multistore_Export {
	/**
	 * Sends http headers
	 */
	function sendHttpHeaders() {
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->filename . '.csv' );
	}

	/**
	 * @param $row
	 * Generates row
	 *
	 * @return string
	 */
	function generateRow( $row ) {
		foreach ( $row as $key => $value ) {
			$row[ $key ] = '"' . str_replace( ',', '', $this->maybe_jsonify( $this->maybe_format_date( $value ) ) ) . '"';
		}

		return implode( ",", $row ) . "\n";
	}

	/**
	 * @param $value
	 * Encodes array to json
	 *
	 * @return false|mixed|string
	 */
	private function maybe_jsonify( $value ) {
		if ( is_array( $value ) ) {
			$value = json_encode( $value );
		}

		return $value;
	}

	/**
	 * @param $date
	 * Formats date
	 *
	 * @return mixed|string
	 */
	private function maybe_format_date( $date ) {
		if ( is_array( $date ) && isset( $date['date'] ) ) {
			$date = strtotime( $date['date'] );
			$date = date( 'Y-m-d H:i:s', $date );
		}
		if ( $date instanceof WC_DateTime ) {
			$date = $date->__toString();
			$date = strtotime( $date );
			$date = date( 'Y-m-d H:i:s', $date );
		}

		return $date;
	}
}