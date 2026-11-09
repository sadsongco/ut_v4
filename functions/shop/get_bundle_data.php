<?php

require_once(__DIR__ . "/../functions.php");
require_once(base_path("functions/shop/check_bundle_stock.php"));

function getBundleData($db, $bundle_id)
{
    $query = "SELECT bundle_id, price FROM Bundles WHERE bundle_id = ? AND date <= NOW() AND active = 1";
    $bundle = $db->query($query, [$bundle_id])->fetch();
    if (!$bundle) return false;
    $bundle['stock'] = checkBundleStock($db, $bundle_id);
    if ($bundle['stock'] == 0) return false;
    $params = [$bundle_id];
    $query = "SELECT
    Bundle_items.item_id,
    Bundle_items.item_option_id,
    Items.item_id, Items.name, Items.price, Items.image, Items.featured, Items.active,
    Items.stock + (SELECT
        IFNULL(SUM(Item_options.option_stock), 0)
        FROM Item_options
        WHERE Item_options.item_id = Items.item_id
    ) AS stock
    FROM Bundle_items
    JOIN Items ON Bundle_items.item_id = Items.item_id AND Items.active=1
    WHERE Bundle_items.bundle_id = ?
    ";
    $bundle['items'] = $db->query($query, $params)->fetchAll();
    $bundle['raw_price'] = 0;
    foreach ($bundle['items'] as &$item) {
        if ($item['active'] == 0) continue;
        // if ($item['stock'] < $bundle['stock']) $bundle['stock'] = $item['stock'];
        // $bundle['raw_price'] += $item['price'];
        if ($item['item_option_id']) {
            $query = "SELECT
                item_option_id,
                option_name,
                option_price,
                option_stock
            FROM Item_options
            WHERE item_id = ?
            AND item_option_id = ?";
            $item_options = $db->query($query, [$item['item_id'], $item['item_option_id']])->fetchAll();
            if (isset($item_options[0]['option_price'])) $item_options[0]['option_price'] = number_format($item_options[0]['option_price'], 2); 
            if (sizeof($item_options) == 1) $item['option'] = $item_options[0];
            else $item['option'] = sizeof($item_options) > 0 ? ['options'=>$item_options] : false;
            $bundle['raw_price'] += $item_options[0]['option_price'];
        }
        else {
            $query = "SELECT
                item_option_id,
                option_name,
                option_price,
                option_stock
            FROM Item_options
            WHERE item_id = ?
            AND option_stock > 0";
            $item_options = $db->query($query, [$item['item_id']])->fetchAll();
            $item['option'] = false;
            if (sizeof($item_options) > 0) {
                $item['option'] = ['options'=>$item_options];
                $item['select_option'] = true;
            }
            // THIS IS NOT QUITE RIGHT YET
            $bundle['raw_price'] += $item_options[0]['option_price'];
        }

        if (isset($item['image']) && $item['image'])
            $item['image_path'] = "/serve/" . SHOP_ASSET_PATH . "images/" . str_replace(".", "/", $item['image']);

    }
    $bundle['saving'] = number_format($bundle['raw_price'] - $bundle['price'], 2);
    $bundle['disp_price'] = number_format($bundle['price'], 2);
    $bundle['is_bundle'] = true;
    return $bundle;
}