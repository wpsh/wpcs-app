<?php

namespace Preseto\WPCSService;

use Chumper\Zipper\Zipper;

class Asset {

	protected $file;

	protected $dir;

	function __construct( $file, $dir ) {
		$this->file = $file;
		$this->dir = $dir;
	}

	public function id() {
		return $this->hash();
	}

	public function basename() {
		return basename( $this->file['name'] );
	}

	public function filename() {
		return $this->file['tmp_name'];
	}

	protected function hash() {
		return md5_file( $this->file['tmp_name'] );
	}

	public function extension() {
		return strtolower( pathinfo( $this->basename(), PATHINFO_EXTENSION ) );
	}

	public function valid() {
		return ( 'zip' === $this->extension() );
	}

	public function destination() {
		return sprintf(
			'%s/%s',
			rtrim( $this->dir, '/' ),
			$this->id()
		);
	}

	public function unarchive() {
		if ( ! $this->valid() ) {
			throw new \Exception( 'Please supply a zip archive.' );
		}

		// Unzip only if not done already.
		if ( ! file_exists( $this->destination() ) ) {
			$zipper = new Zipper();
			$zipper->make( $this->filename() )->extractTo( $this->destination() );
			$zipper->close();
		}

		return $this->destination();
	}

}
