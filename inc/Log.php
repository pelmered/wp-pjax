<?php

/**
 * Class WP_PJAX_Log
 *
 * @author Peter Elmered
 */
class WP_PJAX_Log {

	/**
	 * Log file handle
	 *
	 * @var resource|null
	 */
	private $fh = null;

	public function write( $msg ) {
		if ( ! $this->fh ) {
			$this->set_file( 'error' );
		}

		return fwrite( $this->fh, $this->format_log( $msg ) );
	}

	public function set_file( $filename ) {
		$this->fh = fopen( WP_PJAX_PLUGIN_PATH . 'logs' . DIRECTORY_SEPARATOR . $filename, 'a' );
	}

	private function format_log( $msg ) {
		return '[' . date( 'Y-m-d h:i:s' ) . '] ' . $msg . "\n\n";
	}
}
