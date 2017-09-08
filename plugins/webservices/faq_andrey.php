<?php
header('Content-type: application/json; charset=utf-8');

include "includes1.php";

$id = ( isset($_GET['id']) )? $_GET['id'] : 2026;
$id = ($id) ? $id : 2026;
$fetch_themes = ( isset($_GET['fetch_themes']) ) ? $_GET['fetch_themes'] : false;
$lang = ( isset($_GET['lang']) )? $_GET['lang'] : "ru";
//echo mb_convert_encoding(json_encode(get_notes($id,$lang,true, $fetch_themes)), 'UTF-8, 'Windows-1251');
echo  json_encode(get_notes($id,$lang,true, $fetch_themes));
//var_dump(strpos("\n", $qwerty));
//var_dump(get_notes($id,$lang,true, $fetch_themes));
?>
