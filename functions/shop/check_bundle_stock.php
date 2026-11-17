<?php

function checkBundleStock($db, $bundle_id) {
    // check stock of specified item options
    $query = "SELECT
        MIN(Item_options.option_stock) as stock
        FROM Bundle_items
        JOIN Item_options ON Bundle_items.item_option_id = Item_options.item_option_id
        WHERE Bundle_items.bundle_id = ?";
    $params = [$bundle_id];
    p_2($query);
    p_2($params);
    try {
        $specified_item_options_stock = $db->query($query, $params)->fetch();
        echo "SPECIFIED ITEM OPTIONS STOCK: ";
        p_2($specified_item_options_stock['stock']);
    } catch (Exception $e) {
        error_log($e);
        exit("Error checking bundle stock");
    }
    // check stock of non-specified item options
    $query = "SELECT
        MAX(Item_options.option_stock) as stock
        FROM Bundle_items
        JOIN Item_options ON Bundle_items.item_id = Item_options.item_id
        WHERE Bundle_items.bundle_id = ?
        AND Bundle_items.item_option_id IS NULL";
    $params = [$bundle_id];
        p_2($query);
    p_2($params);
    try {
        $non_specified_item_options_stock = $db->query($query, $params)->fetch();
        echo "NON-SPECIFIED ITEM OPTIONS STOCK: ";
        p_2($non_specified_item_options_stock['stock']);

    } catch (Exception $e) {
        error_log($e);
        exit("Error checking bundle stock");
    }
    // check stock of non-option items
    $query = "SELECT
        MIN(Items.stock) AS stock
    FROM Bundle_items
    JOIN Items on Bundle_items.item_id = Items.item_id
    WHERE Bundle_items.bundle_id = ?
    AND Bundle_items.item_option_id IS NULL";
    $params = [$bundle_id];
        p_2($query);
    p_2($params);
    try {
        $non_option_items_stock = $db->query($query, $params)->fetch();
        echo "NON OPTION ITEMS STOCK: ";
        p_2($non_option_items_stock);

    } catch (Exception $e) {
        error_log($e);
        exit("Error checking bundle stock");
    }
    if ((sizeof($non_option_items_stock) === 0 || $non_option_items_stock['stock'] === NULL) && min($specified_item_options_stock['stock'], $non_specified_item_options_stock['stock']) > 0) {
        echo "No non option items returned, the smaller of specified item options stock and non specified item options stock is greater than 0";
        return min($specified_item_options_stock['stock'], $non_specified_item_options_stock['stock']);
    }
    if (sizeof($non_option_items_stock) > 0 && $non_option_items_stock['stock'] > 0) {
        echo "Non option items stock is more than zero";
        return min($specified_item_options_stock['stock'], $non_specified_item_options_stock['stock'], $non_option_items_stock['stock']);
    }
    echo "There is no stock of this bundle";
    return 0;
}