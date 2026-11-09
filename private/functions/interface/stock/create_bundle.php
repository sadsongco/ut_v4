<?php

require(__DIR__ . "/../../../../functions/functions.php");

use Database\Database;
$db = new Database('orders');

if (sizeof($_POST['bundle-item']) <= 1) {
    exit("Can't create a bundle with only one item");
}

$db->beginTransaction();

$query = "INSERT INTO Bundles VALUES (null, NOW(), ?, 1)";
try {
    $db->query($query, [$_POST['price']]);
    $bundle_id = $db->lastInsertId();
} catch (PDOException $e) {
    error_log($e);
    $db->rollback();
    exit("Error adding new bundle");
}

foreach ($_POST['bundle-item'] as $key=>$bundle_item) {
    try {
        $bundle_item_option = NULL;
        if (isset($_POST['bundle-item-option'][$key]) && $_POST['bundle-item-option'][$key] != "" && $_POST['bundle-item-option'][$key] != "*") {
            $bundle_item_option = $_POST['bundle-item-option'][$key];
        }
        $query = "INSERT INTO Bundle_items (bundle_id, item_id, item_option_id) VALUES (?, ?, ?)";
        $db->query($query, [$bundle_id, $bundle_item, $bundle_item_option]);
    } catch (PDOException $e) {
        error_log($e);
        $db->rollback();
        exit("Error adding bundle items");
    }
}

$db->commit();

echo "Bundle Created";