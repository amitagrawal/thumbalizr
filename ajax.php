<?php
require_once 'thumbalizrrequest.php';

header('Content-Type: application/json;charset=utf-8');

$url = (isset($_GET['url'])) ? $_GET['url'] : null;
$config = array('api_key' => 'your-api-key');
$thumbalizr = new thumbalizrRequest($config);

if ($url) {
    $thumbalizr->request($url);
    $response = array(
        'status' => strtolower($thumbalizr->headers['Status']),
        'image_path' => $thumbalizr->local_cache_file
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Missing url parameter.'
    );
}

echo json_encode($response);
?>