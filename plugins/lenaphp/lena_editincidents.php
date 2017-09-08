<?php
include ("../db.inc.php");
include ("../functions.php");
$data = $_REQUEST;
echo '<pre>';
//var_dump($data);
$comment = mysql_real_escape_string($data["editcomment"]);
$executor = $data["editexecutor"];
$active = (int) $data["editactive"];
$source = serialize($data["editsource"]);
$service = serialize($data["editservice"]);
$incidents = serialize($data["editincidents"]);
$readers = serialize($data["editreaders"]);
$rank = (int) $data["editrank"];
$id = $data["editid"];

$query="UPDATE lena_incidents SET Comment='$comment', ExecutorID='$executor', Active='$active', SourceID='$source', ServiceID='$service', IncidentID='$incidents', ReadersID='$readers', Rank='$rank' WHERE ID='$id'";
$res = usql($query);
?>
