<?php

if(session_status() === PHP_SESSION_NONE) session_start();

include_once(__DIR__ . "/../../functions/functions.php");
include_once(base_path("functions/shop/get_categories.php"));
include_once(base_path("functions/shop/get_bundle_data.php"));

use Database\Database;
$db = new Database('orders');

if(isset($paths[4]) && $paths[4] == "bundles") {
    $query = "SELECT DISTINCT bundle_id FROM Bundle_items WHERE item_id = ? AND item_option_id = ?";
    $params = [$paths[2], $paths[3]];
    $assoc_bundle_ids = $db->query($query, $params)->fetchAll();
    exit($this->renderer->render("shop/assoc_bundles", [
        "assoc_bundles"=>sizeof($assoc_bundle_ids) > 0,
        "bundles"=>$assoc_bundle_ids,
        "item_id"=>$paths[2],
        "item_option_id"=>$paths[3]
        ]));
}


$query = "SELECT *
    FROM Items
    WHERE item_id = ?";
try {
    $item = $db->query($query, [$paths[2]])->fetch();
} catch (Exception $e) {
    error_log($e);
    exit("Sorry, there has been a fundamental error.");
}

if ($item['image'] == "") unset($item['image']);
else $item['image_path'] = "/serve/" . SHOP_ASSET_PATH . "images/" . str_replace(".", "/", $item['image']);

$item['description'] = nl2br($item['description']);

if (isset($item['release_date']) && $item['release_date'] > date("Y-m-d")) {
    $item['preorder'] = true;
    $item['release_date_disp'] = date("d M Y", strtotime($item['release_date']));
}

$item_options = $db->query("SELECT * FROM Item_options WHERE item_id = ? AND Item_options.option_stock > 0", [$paths[2]])->fetchAll();

foreach($item_options as &$item_option) {
    if (isset($item_option['option_price'])) $item_option['option_price'] = number_format($item_option['option_price'], 2);
    if ($item_option['option_image'] == "") unset($item_option['option_image']);
    else $item_option['image_path'] = "/serve/" . SHOP_ASSET_PATH . "images/" . str_replace(".", "/", $item_option['option_image']);
}


$item['option'] = sizeof($item_options) > 0 ? ['options'=>$item_options] : false;

$categories = getCategories($db);

echo $this->renderer->render('shop/item', [
    "item"=>$item,
    "item_view"=>true,
    "categories"=>$categories,
    "stylesheets"=>["shop", "accordion"],
    "scripts"=>["accordion"]
]);