<?php
header('Content-type: application/json; charset=utf-8');

include "includes_andrey.php";

$id = ( isset($_GET['id']) )? $_GET['id'] : 2026;
$id = ($id) ? $id : 2026;
$lang = ( isset($_GET['lang']) )? $_GET['lang'] : "ru";

$data=FOS($id,$lang);
if($id==2026){
	$data['fields']=getFields(2026,4);
	$data['fields']['action']="http://pm.citrt.net/plugins/webservices/service.php";
}
echo json_encode($data);

?>
