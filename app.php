<?php

namespace Preseto\WPCSService;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

$mustache = new \Mustache_Engine( [
	'loader' => new \Mustache_Loader_FilesystemLoader( __DIR__ . '/views' ),
] );

$config = [
	'standards' => [
		'envato' => __DIR__ . '/phpcs-envato.xml',
	],
	'uploads_dir' => __DIR__ . '/uploads',
	'mustache' => $mustache,
];

function process_asset( $asset ) {
	// Setup the phpcs validator.
	$validator = new Validator(
		__DIR__ . '/vendor/bin/phpcs',
		$asset->unarchive()
	);

	// Run the coding standard checks.
	$phpcs_report = $validator->run( $config['standards']['envato'] );

	// Format the report.
	return new Formatter( $phpcs_report, $config['uploads_dir'] . '/' . $asset->id() . '/' );
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
	$input = 'assetzip';
	$name = $args['id'];
	$response->getBody()->write("Hello, $name");

	$asset = new Asset(
		$_FILES[ $input ],
		$uploads_dir
	);

	return $response;
});

$app->run();
