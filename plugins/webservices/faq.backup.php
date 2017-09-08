<?php
header('Content-type: application/json; charset=utf-8');

include "includes.php";

$id = ( isset($_GET['id']) )? $_GET['id'] : 2026;
$id = ($id) ? $id : 2026;
$lang = ( isset($_GET['lang']) )? $_GET['lang'] : "ru";
echo json_encode(get_notes($id,$lang,true));
?>
