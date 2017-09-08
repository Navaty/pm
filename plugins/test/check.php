<?php

echo "<font face=Verdana>Проверка работоспособности веб сервера FengOffice</font></br>";


if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$start = ip2long("10.11.125.1");
$end = ip2long("10.11.125.255");
$myip = ip2long($ip);
if( ($myip > $start AND $myip < $end) || $ip == "85.233.69.33"){

echo "<title>Web service status</title>";

function secondsToTime($seconds) {
    $seconds = round($seconds);
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}

function do_ping() {
$uptime = secondsToTime( exec ("echo $(cut -d ' ' -f 1 </proc/uptime)"));
$cpuL = shell_exec ("./check.sh");
//if ($fping <= "75" && $fping >= "0") {
//$p_result = "<font color=green>GOOD</font>";
//} else if ($fping <= "150" && $fping > "75") {
//$p_result = "<font color=yellow>AVERAGE</font>";
//} else if ($fping > "150") {
//$p_result = "<font color=red>BAD</font>";
//} else if ($fping == "") {
//echo "<font face=Verdana>Host <b>$host</b> is not responding to ping.</font>";
//exit;
//}
echo "<font face=Verdana>FengOffice Frontend Server Uptime: $uptime  <br/> $cpuL </font>";
}

do_ping($fping);

} else { echo "<font face=Verdana>Отказано в доступе, пожалуйста, обратитесь к Администратору</font>"; }

?>

