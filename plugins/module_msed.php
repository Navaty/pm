<?
include_once "statusage.php"; //by almaz - usage control

function msed_get_category($ID=false) {
  if($ID) {
    $link = "?id=".$ID;
  }
  $url = "http://85.233.79.237/webservices/msed/reference/subject_category.php".$link;
  $json = file_get_contents($url);
  //if($ID) {  echo $url;}
  $arr = json_decode($json,1);
  return $arr;
}
?>