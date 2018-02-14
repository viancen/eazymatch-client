<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo '<pre>';
include( '../vendor/autoload.php' );

$key    = 'xxxxx';
$secret = 'xxxxx';

$tt = new \EazymatchClient(
	$key,
	$secret,
	'emol'
);

if ( ! empty( $tt ) ) {
	$test = $tt->call( 'session/login', ['test','123'] );
}

//session token is now set. in production = store this in local storage for future requests
$data = $tt->get( 'users' );

if ( ! empty( $data ) ) {
	print_r( $data );
}
