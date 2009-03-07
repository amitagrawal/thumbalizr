<?
define('_THUMBALIZR',1);
require('thumbalizrrequest.php');

// See README.textile for further reference.
$config = array(
	// Change the value to your api-key.
	'api_key' => 'api_key',
);
$image = new thumbalizrRequest($config);
$image->request('http://www.thumbalizr.com/');

if($image->headers['Status'] == 'OK' || $image->headers['Status'] == 'LOCAL') {
	$image->output();
} else {
	print_r($image->headers);
}
?>