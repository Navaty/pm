<?php
include_once "statusage.php"; //by almaz - usage control
error_reporting(E_ALL);
include ("functions.php");
//phpinfo();
/*
Отправляем номер задачи в Монго
*/
$oktellsession = "4c2d30eb-8064-49a9-bb6f-f1a60d8a12e9";
$TASKID = '5687';
$url = 'http://85.233.79.237/esb/oktell/index.php';
$data = array('command' => 'taskid', 'idsession' => $oktellsession, 'taskid' => $TASKID);
//$res = post_with_curl($data, $url);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
 
$res = curl_exec($ch);

var_dump($data); 
//var_dump($res);

?>
