<?
$username       = "fenguser2016";//DB_USER;
$password       = "ccPass2016Word";//DB_PASS;
$db_name        = DB_NAME;

$con = mysql_connect(DB_HOST,$username,$password)
       or die ("Could not connect");

mysql_select_db ($db_name)
        or die ("Could not select database");

mysql_query("set names 'utf8'");
?>
