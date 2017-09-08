<?php
header('Content-type: application/json; charset=utf-8');

include "includes2.php";
$CacheName = "fos.php".md5(serialize($_GET));
$cache = file_get_contents2($CacheName);

if ($cache != false) {
	echo json_encode(unserialize($cache));
} else {
$id = ( isset($_GET['id']) )? $_GET['id'] : 2026;
$id = ($id) ? $id : 2026;
$lang = ( isset($_GET['lang']) )? $_GET['lang'] : "ru";

$data=FOS($id,$lang);
if($id==2026){
	$data['fields']=getFields(2026,4);
	$data['fields']['action']="http://pm.citrt.net/plugins/webservices/service.php";
}
$dir = "/fengoffice/cache";
$filePath = $dir. "/".$CacheName;
file_put_contents($filePath,serialize($data));
echo json_encode($data);

}
//file_put_contents("logs/fos.post.log", date("D M j G:i:s ").serialize($output)."/n", FILE_APPEND);
?>
