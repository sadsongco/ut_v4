<?php

require(__DIR__ . "/../../../../functions/functions.php");
$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(base_path('private/views/stock')),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(base_path('private/views/partials'))
));

use Database\Database;
$db = new Database('orders');

// p_2($_GET);

$query = "SELECT * FROM Item_options WHERE item_id = ?";
try {
    $options = $db->query($query, [$_GET['item_id']])->fetchAll();
}
catch (Exception $e) {
    error_log($e->getMessage());
    exit("ERROR GETTING BUNDLE ITEM OPTIONS");
}
// p_2($options);
echo $m->render("bundle_item_options", [
    "item_id" => $_GET['item_id'],
    "options" => isset($options) && sizeof($options) != 0,
    "item_options" => $options,
    "bundle_page_id" => $_GET['bundle_page_id']
]);