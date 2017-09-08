<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");

function yulia($str) {
  $sql = "SELECT id,text FROM og_project_tasks WHERE text LIKE ('%".$str."%')  LIMIT 300";
  $res = ssql($sql);
  return $res;
}
$res = yulia($_REQUEST["s"]);
?>
<table border=1>
<?
foreach($res as $v) {
  $i++;
  echo "<tr>";
  echo "<td>$i</td>";
  foreach($v as $kv) {
    echo "<td>$kv</td>";
  }
  echo "</tr>";
}
?></table>
