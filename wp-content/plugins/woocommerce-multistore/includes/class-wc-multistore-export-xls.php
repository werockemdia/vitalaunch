<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class Wc Multistore Export Xls
 */
class WC_Multistore_Export_Xls extends WC_Multistore_Export {

	/**
	 * @var string
	 */
	public $encoding = 'UTF-8';

	/**
	 * @var
	 */
	public $spreadsheet;

	/**
	 * @var string
	 */
	public $title = 'Sheet1';

	/**
	 * @var int
	 */
	public $row = 1;

	/**
	 *
	 */
	public function initialize() {
		$this->register_loader();
		$this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

		ob_start();
		$this->sendHttpHeaders();
		$this->write( $this->generateHeader() );
	}

	/**
	 *
	 */
	function sendHttpHeaders() {
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" . $this->encoding);
		header("Content-Disposition: inline; filename=\"" . basename($this->filename) . "\".xls");
	}

	/**
	 * @param $row
	 */
	function generateRow($row) {
		$sheet = $this->spreadsheet->getActiveSheet();

		$i = 0;
		foreach ($row as $cell) {
			if( is_array($cell) ){
				$cell = json_encode($cell);
			}
			$sheet->setCellValueByColumnAndRow($i + 1, $this->row, $cell);
			$i++;
		}

		$this->row++;
	}

	/**
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	function generateFooter() {
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $this->spreadsheet, ucfirst( 'xls' ) );
		$writer->save('php://output');
	}

	/**
	 *
	 */
	private function register_loader() {
		require_once( WOO_MSTORE_PATH . '/dependencies/W8_Loader.php' );

		$root = WOO_MSTORE_PATH . '/dependencies/PhpSpreadsheet';

		if ( $loader = new W8_Loader() ) {
			$loader->register();

			$loader->addPrefix( 'Psr\SimpleCache', WOO_MSTORE_PATH . '/dependencies/simple-cache' );

			$loader->addPrefix( 'PhpOffice\PhpSpreadsheet', WOO_MSTORE_PATH . '/dependencies/PhpSpreadsheet' );

			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $root, RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST,
				RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
			);
			foreach ( $iterator as $path => $dir ) {
				if ( $dir->isDir() ) {
					$prefix = 'PhpOffice\PhpSpreadsheet' . str_replace( DIRECTORY_SEPARATOR, '\\', str_replace( $root, '', $path ) );
					$loader->addPrefix( $prefix, $path );
				}
			}
		}
	}
}