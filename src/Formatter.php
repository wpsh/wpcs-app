<?php

namespace Preseto\WPCSService;

class Formatter {

	protected $formatted;

	protected $dir;

	function __construct( $report, $dir ) {
		$this->report = $report;
		$this->dir = $dir;
	}

	protected function format() {
		$report = $this->report;
		$files = [];

		foreach ( $report->files as $file_path => $file_report ) {
			$lines = file( $file_path );
			$file_report->file = str_replace( $this->dir, '', $file_path );

			foreach ( $file_report->messages as &$message ) {
				$message->snippet = implode( "\n", array_splice( $lines, max( 0, $message->line - 1 ), 3 ) );
			}

			$files[] = $file_report;
		}

		$report->files = $files;

		return $report;
	}

	public function formatted() {
		if ( ! isset( $this->formatted ) ) {
			$this->formatted = $this->format();
		}

		return $this->formatted;
	}

}
