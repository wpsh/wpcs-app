<?php

namespace Preseto\WPCSService;

use Chumper\Zipper\Zipper;

class Asset {

	protected $id;

	protected $file;

	protected $dir;

	function __construct( $file, $dir, $id = null ) {
		$this->dir = $dir;
		$this->id = $id;
		$this->file = array_merge(
			$file,
			[
				'name' => null,
				'tmp_name' => null,
			]
		);
	}

	public function id() {
		if ( ! isset( $this->id ) ) {
			$this->id = $this->hash();
		}

		return $this->id;
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
		// Unzip only if not done already.
		if ( ! file_exists( $this->destination() ) ) {
			$zipper = new Zipper();
			$zipper->make( $this->filename() )->extractTo( $this->destination() );
			$zipper->close();
		}

		return $this->destination();
	}

}
