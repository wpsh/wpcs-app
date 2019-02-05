<?php

namespace Preseto\WPCSService;

class Report {

	protected $formatted;

	function __construct( $report ) {
		$this->report = $report;
	}

	protected function format() {
		$report = $this->report;

		foreach ( $report->files as $file_path => &$file_report ) {
			$lines = file( $file_path );

			foreach ( $file_report->messages as &$message ) {
				$message->snippet = implode( "\n", array_splice( $lines, max( 0, $message->line - 1 ), 3 ) );
			}
		}

		return $report;
	}

	public function formatted() {
		if ( ! isset( $this->formatted ) ) {
			$this->formatted = $this->format();
		}

		return $this->formatted;
	}

}
