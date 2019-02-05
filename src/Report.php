<?php

namespace Preseto\WPCSService;

class Source {

	protected $dir;

	public function __construct( $dir ) {
		$this->dir = rtrim( $dir, '/' );
	}

	public function id() {
		return basename( $this->dir );
	}

	public function exists() {
		return file_exists( $this->dir );
	}

}
