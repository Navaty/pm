<?php
include_once "statusage.php"; //by almaz - usage control
include_once("db.inc.php");
include_once("functions.php");

$time = microtime();
$searchTerm = $_REQUEST['term'];

function get_comments($TaskID) {
  $sql = "SELECT text,created_on,created_by_id FROM og_comments WHERE rel_object_id = '{$TaskID}' AND rel_object_manager = 'ProjectTasks' AND trashed_on = '0000-00-00 00:00:00' ORDER BY ID ";
  $comments = ssql($sql);
  if(is_array($comments)) {
    foreach($comments as $comment) {
      $res[] = $comment;
    }
  }
  return $res;
}
function get_fields($TaskID) {
  $sql = "SELECT * FROM og_object_properties WHERE rel_object_id = '{$TaskID}'\n";
  $fields = ssql($sql);
  if(is_array($fields)) {
    foreach($fields as $field) {
      $newfield[key] = $field[name];
      $newfield[value] = $field[value];
      $res[] = $newfield;
    }
  }
  return $res;
}
$sql1 = "SELECT rel_object_id, EntryTime
FROM og_searchable_objects
WHERE rel_object_manager = 'ProjectTasks'
AND   MATCH (content) AGAINST  ('{$searchTerm}')
GROUP BY rel_object_id
ORDER BY EntryTime DESC
LIMIT 100
";
//AND   content LIKE  ('%{$searchTerm}%')
$res = ssql($sql1);
if(count($res)>0) {
  foreach($res as $objectid) {
    $taskid = (string)$objectid[rel_object_id];
    //    $taskid = (string)$fields[1][rel_object_id];
    $sql = "SELECT title,text FROM og_project_tasks WHERE id = '{$taskid}'";
    $taskinfo = ssql($sql);
    $time = (string)$fields[1][EntryTime];
    $task[taskid] = $taskid;
    $task[EntryTime] = $objectid[EntryTime];
    $task[taskinfo] = $taskinfo[1];
    $task[comments] = get_comments($taskid);
    //    foreach($fields as $field) {
    $task[fields] = get_fields($taskid);//[$field[name]] = $field[value];
      //    }
    $tasks[] = $task;
  }
}
$time = microtime()-$time;
$results[sql1] = $sql1;
$results[query] = $searchTerm;
$results[time] = $time;
$results[tasks] = $tasks;;
//print_r($res);

echo json_encode($results);
