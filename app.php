<?php

namespace Preseto\WPCSService;

require __DIR__ . '/vendor/autoload.php';

$input = 'assetzip';

if ( ! empty( $_FILES[ $input ] ) ) {
	$asset = new Asset(
		$_FILES[ $input ],
		__DIR__ . '/uploads'
	);

	try {
		// Setup the phpcs validator.
		$validator = new Validator(
			__DIR__ . '/vendor/bin/phpcs',
			$asset->unarchive()
		);

		// Run the coding standard checks.
		$phpcs_report = $validator->run( __DIR__ . '/phpcs-envato.xml' );

		// Format the report.
		$report = new Report( $phpcs_report );

		$data = $report->formatted();

		print_r($data);die;
	} catch (\Exception $e) {
		echo $e->getMessage();
	}
}
