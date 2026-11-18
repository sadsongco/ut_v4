<?php

function getAssociatedBundles($db, $item_id, $item_option_id=false) {
    $query = "SELECT DISTINCT bundle_id FROM Bundle_items WHERE item_id = ?";
    $query .= $item_option_id ? " AND item_option_id = ?" : " AND item_option_id IS NULL";
    $params = $item_option_id ? [$item_id, $item_option_id] : [$item_id];
    try {
        return $db->query($query, $params)->fetchAll();
    } catch (PDOException $e) {
        throw new Exception($e);
    }
}