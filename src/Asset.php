<?php

namespace Preseto\WPCSService;

use Chumper\Zipper\Zipper;

class Asset {

	protected $id;

	protected $file;

	protected $to;

	function __construct( $file, $to, $id = null ) {
		$this->to = $to;
		$this->file = $file;
		$this->id = $id;
	}

	public function id() {
		if ( ! isset( $this->id ) ) {
			$this->id = $this->hash();
		}

		return $this->id;
	}

	protected function hash() {
		return md5_file( $this->file );
	}

	public function destination() {
		return sprintf(
			'%s/%s',
			rtrim( $this->to, '/' ),
			$this->id()
		);
	}

	public function unarchive() {
		// Unzip only if not done already.
		if ( ! file_exists( $this->destination() ) ) {
			$zipper = new Zipper();
			$zipper->make( $this->file )->extractTo( $this->destination() );
			$zipper->close();
		}

		return $this->destination();
	}

}
