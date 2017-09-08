<?
include_once "statusage.php"; //by almaz - usage control

include ("db.inc.php");
include ("functions.php");

function bz_curl($PostData,$URL) {
  $data = http_build_query($PostData);
  $curl = curl_init();
  curl_setopt($curl,CURLOPT_URL,$URL);
  curl_setopt($curl,CURLOPT_HEADER, 0);
  curl_setopt($curl,CURLOPT_POST, 1);
  curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl,CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/5.0)');
  curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
  $res = curl_exec($curl);
  if(!$res){
    $error = curl_error($curl).'('.curl_errno($curl).')';
    return $error;
  }
  curl_close($curl);
  return $res;
}


function eduhelp_get_comment($CommentID) {
  $sql = "SELECT * FROM og_comments WHERE id = '".$CommentID."'";
  $res = ssql($sql,"utf8");
  $data["comment"]  = $res[1]["text"];
  $data["taskid"]   = $res[1]["rel_object_id"];
  $data["authorid"] = $res[1]["updated_by_id"];
  return $data;
}

function eduhelp_update_comment($CommentID) {
  $sql = "UPDATE og_comments SET EDUHELPIsPublished = 1, EDUHELPEntryTime = NOW() WHERE ID = '$CommentID'";
  logger($sql,"error"); 
  $res = usql($sql);
  return $res;
}

function eduhelp_iscommentpublished($CommentID) {
  $sql = "SELECT * FROM og_comments WHERE ID = '$CommentID' AND EDUHELPIsPublished =1 ";
  $res = ssql($sql,1);
  if($res[1]["EDUHELPIsPublished"]==1) {
    return true;
  } else {
    return false;
  }
}

function eduhelp_send_comment($CommentID) {
  $url = "http://help.edu.tatar.ru/support/feng/answer/";
  $commentdata = eduhelp_get_comment($CommentID);
  $TaskID  = $commentdata["taskid"];
  $Comment = $commentdata["comment"];
  $Author  = $commentdata["authorid"];

  $data["code"] = $TaskID;
  $data["appeal"] = $Comment;
  if($Author) {
    $data["author"] = $Author;
  }
  $res = bz_curl($data,$url);
  if($res) {
    $res = eduhelp_update_comment($CommentID);
    return "Опубликовано";
  } else {
    return "problem";
  }
}

function eduhelp_ispermitted2publish($UserID) {
  if(in_array($UserID,opengoo_HELPEDU_get_users())){
    return true;
  } else {
    return false;
  }
}

function opengoo_get_group_users($GroupID) {
  $sql = "SELECT * FROM og_group_users WHERE group_id = '$GroupID'";
  $res = ssql($sql);
  return $res;
}

function opengoo_HELPEDU_get_users() {
  $res = opengoo_get_group_users(10000042);
  foreach($res as $v) {
    $data[] = $v["user_id"];
  }
  return $data;
}

function sqlerrorlog($type, $info, $file, $row) {
  // if($type) {
  global $evalcodename;
  $errorlog     =   "$type $info @".$evalcodename." Row $row\r\n";
  $errorshow    =   "$type $info @<b>".$evalcodename."</b> Row <b>$row</b>\r\n";
  logger( $errorlog,"error" );
  echo $errorshow;
  // }
}

$commentid = $_REQUEST["commentid"];
$send = $_REQUEST["send"];

if($commentid && $send) {
  echo $res = eduhelp_send_comment($commentid);
  //  echo eduhelp_get_comment($commentid);
} 
?>