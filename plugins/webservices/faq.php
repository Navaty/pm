<?php
header('Content-type: application/json; charset=utf-8');

include "includes2.php";
$CacheName = "faq.php".md5(serialize($_GET));
$cache = file_get_contents2($CacheName);

if ($cache != false) {
        echo json_encode(unserialize($cache));
} else {
$id = ( isset($_GET['id']) )? $_GET['id'] : 2026;
$id = ($id) ? $id : 2026;
$fetch_themes = ( isset($_GET['fetch_themes']) ) ? $_GET['fetch_themes'] : false;
$lang = ( isset($_GET['lang']) )? $_GET['lang'] : "ru";
$data = get_notes($id,$lang,true, $fetch_themes);

$dir = "/fengoffice/cache";
$filePath = $dir. "/".$CacheName;
file_put_contents($filePath,serialize($data));
echo json_encode($data);

}
?>
