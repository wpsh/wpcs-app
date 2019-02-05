<?php

namespace Preseto\WPCSService;

class Validator {

	function __construct( $bin, $dir ) {
		$this->bin = $bin;
		$this->dir = $dir;
	}

	public function run( $standard ) {
		$command = sprintf(
			'php %s --standard=%s --report=json %s',
			escapeshellarg( $this->bin ),
			escapeshellarg( $standard ),
			escapeshellarg( $this->dir )
		);

		$output = trim( shell_exec( $command ) ); // TODO Is there a better way to do this?

		return json_decode( $output );
	}

}
