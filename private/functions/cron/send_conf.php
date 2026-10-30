<?php

require_once("../../../functions/functions.php");
require_once(base_path("../secure/env/config.php"));
require_once(base_path('classes/Database.php'));
require_once(base_path("functions/email/send_confirmation_email.php"));
include_once(base_path('../secure/secure_id/secure_id_ut.php'));
$log_path = base_path("../web_assets/ut/data/conf_list.txt");

use Database\Database;
$db = new Database('mailing_list');

$ids = file($log_path);
if (sizeof($ids) == 0) exit ("Resending of confirmation email failed: No emails to resend");
$id = (int)array_pop($ids);

try {
    $query = "SELECT * FROM ut_mailing_list WHERE email_id = ?";
    $row = $db->query($query, [$id])->fetch();
    if (!$row || sizeof($row) == 0) throw new PDOException('Email not found in database');
} catch (PDOException $e) {
    error_log($e);
    exit ("Resending of confirmation email failed: " . $e->getMessage());
}
      
try {
    $response = sendConfirmationEmail($row, "conf_email_resend");
    if (!$response['success']) throw new Exception($response['status']);
} catch (Exception $e) {
    error_log($e);
    exit ("Resending of confirmation email failed: " . $e->getMessage());
}

file_put_contents($log_path, $ids);
