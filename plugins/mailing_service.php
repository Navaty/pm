<?php
include_once "statusage.php"; //by almaz - usage control
include_once "db.inc.php";
include_once "functions.php";

$to = $_REQUEST['toemail'];
$from = $_REQUEST["fromemail"];
$subject = $_REQUEST['subject'];
$body = $_REQUEST['body'];
echo opengoo_insert_queued_email_without_feng($to, $from, $subject, $body);
//$ToEmail,$FromEmail,$Subject,$Body
