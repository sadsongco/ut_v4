<?php

function getItems($db, $category=null)
{
    $where = "";
    $params = [];
    if ($category) {
        $where = "AND Items.category = ?";
        $params = [$category];
    }
    $query = "SELECT * FROM (
        SELECT
            Items.item_id, Items.name, Items.price, Items.image, Items.featured,
            IFNULL(Items.stock, 0) + (SELECT IFNULL(SUM(Item_options.option_stock), 0) FROM Item_options WHERE Item_options.item_id = Items.item_id) AS stock
            FROM Items
            WHERE active = 1
            $where
            GROUP BY Items.item_id
            ORDER BY featured DESC
        ) AS T
    WHERE T.stock > 0";
    try {
        $items = $db->query($query, $params)->fetchAll();
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }
    foreach ($items as &$item) {
        try {
            $query = "SELECT * FROM Item_options WHERE item_id = ? AND option_stock > 0";
            $params = [$item['item_id']];
            $item_options = $db->query($query, $params)->fetchAll();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        if (sizeof($item_options) > 0) {
            $item['option'] = ['options'=>$item_options];
            $item['option']['select_option'] = true;
        }
        if (isset($item['image']) && $item['image'])
            $item['image_path'] = "/serve/" . SHOP_ASSET_PATH . "images/" . str_replace(".", "/", $item['image']);
    }
    return $items;
}