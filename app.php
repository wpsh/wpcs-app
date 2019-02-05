<?php

namespace Preseto\WPCSService;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

$config = [
	'uploads_dir' => __DIR__ . '/uploads',
	'mustache' => new \Mustache_Engine( [
		'loader' => new \Mustache_Loader_FilesystemLoader( __DIR__ . '/views' ),
	] ),
];

function process_asset( $asset ) {
	$report_file = sprintf( '%s/%s.json', __DIR__ . '/reports', $asset->id() );

	if ( file_exists( $report_file ) ) {
		$phpcs_report = json_decode( file_get_contents( $report_file ) );
	} else {
		// Setup the phpcs validator.
		$validator = new Validator(
			__DIR__ . '/vendor/bin/phpcs',
			$asset->destination()
		);

		// Run the coding standard checks.
		$phpcs_report = $validator->run( __DIR__ . '/phpcs-envato.xml' );

		file_put_contents( $report_file, json_encode( $phpcs_report ) );
	}

	// Format the report.
	return new Formatter( $phpcs_report, $asset->destination() );
}

$app = new \Slim\App;

$app->get( '/report/{id:[a-z0-9]+}', function ( Request $request, Response $response, array $args ) use ( $config ) {
	$asset = new Asset(
		[],
		$config['uploads_dir'],
		$args['id']
	);

	$template = $config['mustache']->loadTemplate( 'report' );

	try {
		$output = $template->render( [
			'hash' => $asset->id(),
			'report' => process_asset( $asset )->formatted(),
		] );
	} catch (\Exception $e) {
		$output = $template->render( [
			'error' => $e->getMessage(),
		] );
	}

	$response->getBody()->write( $output );

	return $response;
});

$app->post( '/submit', function ( Request $request, Response $response, array $args ) use ( $config ) {
	$uploadedFiles = $request->getUploadedFiles();

	$template = $config['mustache']->loadTemplate( 'report' );

	if ( ! empty( $uploadedFiles['assetzip'] ) ) {
		try {
			$asset = new Asset( $uploadedFiles['assetzip']->file, $config['uploads_dir'] );
			$asset->unarchive();

			return $response->withRedirect( 'report/' . $asset->id(), 302 );
		} catch (\Exception $e) {
			$output = $template->render( [
				'error' => $e->getMessage(),
			] );
		}
	} else {
		$output = $template->render( [
			'error' => 'Missing upload file.',
		] );
	}

	$response->getBody()->write( $output );

	return $response;
});

$app->get( '/', function ( Request $request, Response $response, array $args ) use ( $config ) {
	$template = $config['mustache']->loadTemplate( 'index' );
	$response->getBody()->write( $template->render() );

	return $response;
});

$app->run();
