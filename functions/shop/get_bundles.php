<?php

require_once(base_path("functions/shop/check_bundle_stock.php"));

function getBundles($db, $category=null)
{
    $where = "";
    $params = [];
    if ($category) {
        $where = "
        JOIN `Bundle_items` ON Bundle_items.bundle_id = Bundles.bundle_id
        JOIN `Items` ON Bundle_items.item_id = Items.item_id
        AND Items.category = ?";
        $params = [$category];
    }
    $query = "SELECT
        Bundles.bundle_id,
        Bundles.price
    FROM `Bundles`
    $where
    WHERE `active` = 1
    GROUP BY Bundles.bundle_id";
    try {
        $bundles = $db->query($query, $params)->fetchAll();
    } catch (Exception $e) {
        error_log($e->getMessage());
        exit("Error getting bundles");
    }
    $return_bundles = [];
    foreach ($bundles as $bundle) {
        try {
            if (checkBundleStock($db, $bundle['bundle_id']) > 0) {
                $return_bundles[] = $bundle;
            }
        } catch (Exception $e) {
            error_log($e);
            exit("Error checking bundle stock");
        }
    }
    return $return_bundles;
}
