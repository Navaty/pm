<?
include("../db.inc.php");
include ("../functions.php");

$sql = "SELECT * FROM og_project_tasks WHERE assigned_to_user_id = '25' AND trashed_by_id = '0' AND completed_by_id ='0' 
ORDER BY priority DESC,due_date DESC LIMIT 2000
";
$res = ssql($sql);

function trS() { echo "<tr>";} 
function trE() { echo "</tr>";} 
function tdS() { echo "<td valign=top>";} 
function tdE() { echo "</td>";} 
?>
<html>
<head>
</head>
<body>
<table border=1 cellspacing=0 cellpadding=0 style="width: 800px">
<?
foreach($res as $k=>$v) {
  trS();
  //      print_r($v); 
  tdS();    echo $v["id"];
  tdE(); 

  tdS();    echo $v["title"];
  tdE(); 



  tdS();   echo $v["text"];
  tdE(); 

  tdS();    echo $v["assigned_on"];
  tdE(); 

  trE();
}
?></table>
</body>
</html>