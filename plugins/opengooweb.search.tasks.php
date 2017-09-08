<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$tasks_arr = opengoo_search_task($_REQUEST["search_task"],$_REQUEST["fieldname"]);

logger("test:".$_REQIEST["fieldname"],"error",__FUNCTION__);

echo "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";

echo "<tasks>\n";

if(is_array($tasks_arr)) {
 foreach($tasks_arr as $k=>$v) {
 if($v["description"]) { $desc = $v["description"];} else { $desc = "-";}
  echo "\t<task>\n";
  echo "\t\t<id>".$v["id"]."</id>\n";
  echo "\t\t<title>".$v["title"]."</title>\n";
  echo "\t\t<text>".htmlspecialchars($v["text"])."</text>\n";
  echo "\t\t<assigned_by_id>".$v["assigned_by_id"]."</assigned_by_id>\n";
  echo "\t\t<assigned_on>".$v["assigned_on"]."</assigned_on>\n";
  echo "\t\t<completed_by_id>".$v["completed_by_id"]."</completed_by_id>\n";
  echo "\t\t<completed_on>".$v["completed_on"]."</completed_on>\n";
  echo "\t\t<trashed_by_id >".$v["trashed_by_id "]."</trashed_by_id>\n";
  if(is_array($v["DATA"])) {
    echo "\t\t<data>\n";
    foreach($v["DATA"] as $kd=>$vd) {
      if(!is_integer($kd)) {
	$key = str_replace("/","-",$kd);
	$key = str_replace(",","",$key);
	echo "\t\t\t<".$key.">".htmlspecialchars($vd)."</".$key.">\n";
      }
    }
    echo "\t\t</data>\n";
  }
  if(is_array($v["COMMENTS"])) {
    echo "\t\t<comments>\n";
    foreach($v["COMMENTS"] as $kd=>$vd) {
      echo "\t\t\t<commentid".$vd["id"].">\n";
      echo "\t\t\t\t<text>".htmlspecialchars($vd["text"])."</text>\n";
      echo "\t\t\t\t<updated_on>".$vd["updated_on"]."</updated_on>\n";
      echo "\t\t\t\t<updated_by_id>".$vd["updated_by_id"]."</updated_by_id>\n";
      echo "\t\t\t</commentid".$vd["id"].">\n";
    }
    echo "\t\t</comments>\n";
  }
  echo "\t</task>\n";
 }			   
}

echo "</tasks>";
mysql_close($con);
?>
