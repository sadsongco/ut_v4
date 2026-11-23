<?php

require_once(__DIR__ . "/../../functions/functions.php");
global $auth;

if (!$auth->isLoggedIn()) header("Location: /members");

use Database\Database;
$db = new Database('orders');

$query = "SELECT * FROM Items WHERE item_id = ?";
$params = [SINGLE_SHOP_ID];

try {
    $item = $db->query($query, $params)->fetch();
} catch (PDOException $e) {
    error_log($e);
    exit("There has been a fundamental error. Sorry.");
}

if ($item['image'] == "") unset($item['image']);
else $item['image_path'] = "/serve/" . SHOP_ASSET_PATH . "images/" . str_replace(".", "/", $item['image']);

$item['description'] = nl2br($item['description']);

if (isset($item['release_date']) && $item['release_date'] > date("Y-m-d")) {
    $item['preorder'] = true;
    $item['release_date_disp'] = date("d M Y", strtotime($item['release_date']));
}


echo $this->renderer->render('shop/item', [
    "item"=>$item,
    "item_view"=>true,
    "stylesheets"=>["shop", "accordion"],
    "scripts"=>["accordion"],
]);