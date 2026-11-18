<?php

if(session_status() === PHP_SESSION_NONE) session_start();

include_once(__DIR__ . "/../../functions/functions.php");
include_once(base_path("functions/shop/get_categories.php"));
include_once(base_path("functions/shop/get_cart_contents.php"));
include_once(base_path("functions/shop/calculate_cart_subtotal.php"));
include_once(base_path("functions/shop/get_associated_bundles.php"));

use Database\Database;
$db = new Database('orders');

$env = false;
if (ENV === 'dev') $env = 'dev';

$categories = getCategories($db);

if (!isset($_SESSION['bundles']) && (!isset($_SESSION['items']) || sizeof($_SESSION['items']) == 0)) {
    echo $this->renderer->render('shop/cart', ["empty"=>true, "categories"=>$categories, "stylesheets"=>["shop"]]);
    exit();
}

$cart_contents = getCartContents($db);
$assoc_bundle_ids = [];
foreach ($cart_contents['items'] AS $item) {
    try {
        $item['option_id'] = isset($item['option_id']) ? $item['option_id'] : false;
        $assoc_bundle_ids = array_merge($assoc_bundle_ids, getAssociatedBundles($db, $item['item_id'], $item['option_id']));
    } catch (Exception $e) {
        error_log($e);
        exit("Sorry, there has been a fundamental error.");
    }
}
$subtotal = calculateCartSubtotal($cart_contents);

echo $this->renderer->render('shop/cart', [
    "cart_contents"=>$cart_contents, 
    "categories"=>$categories,
    "subtotal"=>$subtotal,
    "assoc_bundles"=>sizeof($assoc_bundle_ids) > 0,
    "cart"=>true,
    "bundles"=>$assoc_bundle_ids,
    "stylesheets"=>["shop"],
    "env"=>$env
]);
