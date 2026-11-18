<?php

function getFeatured($db, $category=null)
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
            WHERE featured = 1
            AND active = 1
            $where
            GROUP BY Items.item_id
            ORDER BY featured DESC
        )T
    WHERE T.stock > 0";
    try {
        $items = $db->query($query, $params)->fetchAll();
    } catch (PDOException $e) {
        throw new Exception($e);
    }
    foreach ($items as &$item) {
        try {
            $item_options = $db->query("SELECT * FROM Item_options WHERE item_id = ? AND option_stock > 0", [$item['item_id']])->fetchAll();
        } catch (PDOException $e) {
            throw new Exception($e);
        }
        if (sizeof($item_options) > 0) {
            $item['option'] = ['options'=>$item_options];
            $item['option']['select_option'] = true;
            if (is_null($item['price']) || $item['price'] == 0) {
                $price = $item['option']['options'][0]['option_price'];
                if (is_null($price) || $price == 0) {
                    unset($item);
                    continue;
                } else ($item['price'] = number_format($price, 2));
            }            
        }
        $item['image_path'] = "/serve/" . SHOP_ASSET_PATH . "images/" . str_replace(".", "/", $item['image']);
    }
    return $items;
}