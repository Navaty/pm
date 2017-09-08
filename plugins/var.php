<?php
include_once "statusage.php"; //by almaz - usage control
include ("report_terminals/take_tasks_test.php");
include_once("connect_db_func.php");
include_once("functions.php");
//ini_set('error_reporting' E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
date_default_timezone_set("Europe/Moscow");
$current_time = time();

/*
$subs = Array( user_id => Array (
	    0 => 169,
            1 => 276,
            2 => 424,
            3 => 423,
            4 => 426,
            5 => 91,
            6 => 92,
            7 => 274,
            8 => 300
        )

);

$subscriptions2id4project = opengoo_get_project_role("5471","подписчик", false, true);
$merged4subs["user_id"] = $subscriptions2id4project;
$mergedsubs["user_id"] = array_merge($subs["user_id"], $subscriptions2id4project);
print_r($mergedsubs);
*/


//var_dump($mergedsubs);

$task_id = "526427";
echo "<br/>----------------------".$task_id."--------------------------<br/>";

$tasklive = get_info_about_task($task_id, $current_time);
var_dump($tasklive);

$task_id="510962";
echo "<br/>----------------------".$task_id."--------------------------<br/>";

$tasklive = get_info_about_task($task_id, $current_time);
//var_dump($tasklive);

echo "<br/>------------------------------------------------------------<br/>";

echo date('d/m/Y H:i:s', $current_time), '<br>';
echo "lol";
/*echo "<br>";
 foreach($tasklive["companiesworktime"] as  $item_worktime) {
	echo $item_worktime["name"], " ",$item_worktime["time"],'<br>';
}


$query_created_completed_on = "SELECT created_on, completed_on, assigned_to_company_id FROM og_project_tasks WHERE id = 386759";
$created_completed_on = ssql($query_created_completed_on);
var_dump($created_completed_on);
$createtask = datetime_to_timestamp($created_completed_on[1]["created_on"]);
$completetask = datetime_to_timestamp($created_cpmpleted_on[1]["completed_on"]);
get_info_about_workers_time("386759",$current_time,
*/

//get_info_about_task("389657", $current_time);
/*
$url = 'http://pm.citrt.net/plugins/inspektor.php';
$data = array('appeal' => 'test', 'telefon' => '223322');

// use key 'http' even if you send the request to https://...
 $options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

var_dump($result); */

// Server in the this format: <computer>\<instance name> or 
// <server>,<port> when using a non default port number
/* $server = '10.11.63.252\SQLEXPRESS';

// Connect to MSSQL
$link = mssql_connect($server, 'sa', 'phpfi');

if (!$link) {
    die('Something went wrong while connecting to MSSQL');
} */
?>
