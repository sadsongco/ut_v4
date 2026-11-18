<?php

if(session_status() === PHP_SESSION_NONE) session_start();

// exit($this->renderer->render('shop/tmp_index', ["stylesheets"=>["shop"]]));

use Database\Database;
$db = new Database('orders');

include_once(__DIR__ . "/../../functions/functions.php");
if (ENV !== 'dev') exit($this->renderer->render('shop/offline', ["stylesheets"=>["shop"]]));
include_once(base_path("functions/shop/get_featured.php"));
include_once(base_path("functions/shop/get_items.php"));
include_once(base_path("functions/shop/get_bundles.php"));
include_once(base_path("functions/shop/get_categories.php"));

try {
    $featured = getFeatured($db);
    $items = getItems($db);
    $bundles = getBundles($db);
    $categories = getCategories($db);
} catch (Exception $e) {
    error_log($e);
    exit("Sorry, there has been a fundamental error.");
}

foreach ($items as &$item) {
    if (is_null($item['price']) || $item['price'] == 0) {
        $price = $item['option']['options'][0]['option_price'];
        if (is_null($price) || $price == 0) unset($item);
        else ($item['price'] = number_format($price, 2));
    }
}

$env = false;
if (ENV === 'dev') $env = 'dev';

echo $this->renderer->render('shop/index', [
    "featured"=>$featured,
    "items"=>$items,
    "bundles"=>$bundles,
    "categories"=>$categories,
    "stylesheets"=>["shop"],
    "session_timeout"=>isset($_GET['session_timeout']),
    "technical_error"=>isset($_GET['technical_error']),
    "env"=>$env,
    "v"=>$v,
    "debug"=>print_r($items, true)
]);