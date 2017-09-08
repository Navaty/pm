<?php
header('Content-type: application/json; charset=utf-8');

include "includes2.php";
$CacheName = "themes.php".md5(serialize($_GET));
$cache = file_get_contents2($CacheName);

if ($cache != false) {
        echo json_encode(unserialize($cache));
} else {
$id = ( isset($_GET['id']) )? $_GET['id'] : 3791;
$id = ($id) ? $id : 3791;
$lang = ( isset($_GET['lang']) )? $_GET['lang'] : "ru";

$data=FOS($id,$lang);
if($id==3791){
	$data['fields']=getFields(3791,4);
	$data['fields']['action']="http://pm.citrt.net/plugins/webservices/task.php";
}
$dir = "/fengoffice/cache";
$filePath = $dir. "/".$CacheName;
file_put_contents($filePath,serialize($data));
echo json_encode($data);

}
//file_put_contents("logs/themes.post.log", date("D M j G:i:s ").serialize($output)."/n", FILE_APPEND);
?>
