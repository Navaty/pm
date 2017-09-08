<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");

$d = opengoo_get_project_role(4768,"подписчик",false,true);
echo json_encode($d);
