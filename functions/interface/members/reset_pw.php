<?php

include(__DIR__ . "/../../functions.php");
include("includes/content_includes.php");

$target = "login-container";
$manage = false;
if (isset($_GET['target'])) $target = $_GET['target'];
if (isset($_GET['manage'])) $manage = $_GET['manage'];


echo $m->render("members/request_pw_reset", ["base_dir"=>getHost(), "manage"=>$manage, "target"=>$target]);
