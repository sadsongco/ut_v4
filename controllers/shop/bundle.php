<?php

if(session_status() === PHP_SESSION_NONE) session_start();

include_once(__DIR__ . "/../../functions/functions.php");
include_once(base_path("functions/shop/get_bundle_data.php"));

use Database\Database;
$db = new Database('orders');

$env = false;
if (ENV === 'dev') $env = 'dev';

$bundle_id = $paths[2];

$bundle = getBundleData($db, (int)$bundle_id);

echo $this->renderer->render('shop/bundle', [
    "bundle"=>$bundle,
    "stylesheets"=>["shop"],
    "env"=>$env
]);
