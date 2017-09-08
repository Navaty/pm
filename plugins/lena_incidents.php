<?php

include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$data = $_POST;

$comment = $data["comment"];
$executor = $data["executor"];
$active = $data["active"];
$source = $data["source"];
$service = $data["service"];
$incidents = $data["incidents"];
$readers = $data["readers"];
$rank = $data["rank"];

$countresult=1;
$result=array();

for($i=1; $i<count($comment); $i++) {
	$result[$i]["comment"] = $comment[$i];
}
for($i=1; $i<count($rank); $i++) {
        $result[$i]["rank"] = $rank[$i];
}
for($i=1; $i<count($executor); $i++) {
        $result[$i]["executor"] = $executor[$i];
}
for($i=1; $i<count($active); $i++) {
        $result[$i]["active"] = $active[$i];
}
for($i=1; $i<=count($source); $i++) {
        $result[$i]["source"] = $source[$i];
}
for($i=1; $i<=count($service); $i++) {
        $result[$i]["service"] = $service[$i];
}
for($i=1; $i<=count($incidents); $i++) {
        $result[$i]["incidents"] = $incidents[$i];
}
for($i=1; $i<=count($readers); $i++) {
        $result[$i]["readers"] = $readers[$i];
}

foreach($result as $result_item) {
	$rescomment=mysql_real_escape_string($result_item["comment"]);
	$resexecutor=$result_item["executor"];
	$resactive = (int) $result_item["active"];
	$ressource = serialize($result_item["source"]);
	$resservice = serialize($result_item["service"]);
	$resincidents = serialize($result_item["incidents"]);
	$resreaders = serialize($result_item["readers"]);
	$resrank = (int) $result_item["rank"];
	$query="SELECT * FROM lena_incidents WHERE SourceID='$ressource' AND ServiceID='$resservice' AND IncidentID='$resincidents'";
	if(!ssql($query)) {
		$query="INSERT INTO lena_incidents (Rank, Comment, SourceID, ServiceID, IncidentID, ExecutorID, ReadersID, Active) VALUES ('$resrank', '$rescomment', '$ressource', '$resservice', '$resincidents', '$resexecutor', '$resreaders', '$resactive')";
		usql($query);
		$result["status"]=true;
	}
	else {
		$result["status"]=false;
		//return $msg = json_encode($result, JSON_FORCE_OBJECT);
		var_dump(json_encode($result, JSON_FORCE_OBJECT));
	}
}
return $msg = json_encode($result, JSON_FORCE_OBJECT);
?>
