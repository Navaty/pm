<?
include("/fengoffice/config/config.php");

$username       = "fenguser2016";//DB_USER;
$password       = "ccPass2016Word";//DB_PASS;
$db_name        = DB_NAME;

$con = mysql_connect(DB_HOST,$username,$password)
       or die ("Could not connect");

mysql_select_db ($db_name)
        or die ("Could not select database");


#almaz$sql = "SET NAMES cp1251";
#almaz$z = mysql_query($sql);
//mysql_query ( "set character_set_client='utf8'");
//mysql_query ( "set character_set_results='utf8'");
//mysql_query ( "set collation_connection='utf8_general_ci'");
//mysql_query("set names utf8");

?>
