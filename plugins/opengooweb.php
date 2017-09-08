<?
//include_once "statusage.php"; //by almaz - usage control
include("db.inc.php");
include("functions.php");

$file = 'etwas.txt';

file_put_contents($file, print_r($_REQUEST, true));

$appeal = $_REQUEST["appeal"];
$data = $_REQUEST["data"];
$isname = $_REQUEST["isname"];
$debug = @$_REQUEST["debug"];
$title = @$_REQUEST["title"];
$autoclose = @$_REQUEST["autoclose"];
$projectids = @$_REQUEST["projectid"];
$mainprojectname = @$_REQUEST["mainprojectname"];
$mainprojectlevel = @$_REQUEST["mainprojectlevel"];
$email_prefix = @$_REQUEST["email_prefix"];
$email_suffix = @$_REQUEST["email_suffix"];
$data["Контактный_э-адрес"] = $email_prefix . "@" . $email_suffix;

$oktellsession = $_REQUEST["oktellsession"];


if (is_array($projectids)) { // Смотрим передаются ли со стороны клинета проекты
    foreach ($projectids as $k => $v) {
        if ($v) {
            $data[$k] = opengoo_get_projectname_by_projectID($v);
        }
    }
}


$xml = file_get_contents("config.xml"); // читаем содержимое xml файла
$xmlObj = simplexml_load_string($xml);
$xmldata = objectsIntoArray($xmlObj); // xml конвертируем в массив

foreach ($xmldata["is"] as $k => $v) {
    if ($isname == $xmldata["is"][$k]["isid"]) {
        $iskey = $k;
        break;
    }
}

$projectid = $xmldata["is"][$iskey]["workspace_id"];
if (!$_REQUEST["assignedbyid"]) {
    $assignedbyid = $xmldata["is"][$iskey]["assigned_by_id"];
} else {
    $assignedbyid = $_REQUEST["assignedbyid"];
}
$subscriptions = $xmldata["is"][$iskey]["subscriptions"];
$assigned2id4project = opengoo_get_project_role($projectids[$mainprojectname], "исполнитель", $mainprojectlevel);
if ($assigned2id4project) {
    $assigned2id = $assigned2id4project;
} elseif ($_REQUEST["assigned2id"]) {
    $assigned2id = $_REQUEST["assigned2id"];
} else {
    $assigned2id = $xmldata["is"][$iskey]["assigned_to_user_id"];
}

if (isset($debug)) {
    echo "<pre>";
    print_r($_REQUEST);
} elseif (strlen($title) < 5) {
    echo -1;
    echo "\n";
    echo "title length less than 5";
    logger("title less than 5", "error", __FUNCTION__);
} elseif (!$projectid) {
    echo -1;
    echo "\n";
    echo "no projectid";
} elseif (!$assigned2id) {
    echo -1;
    echo "\n";
    echo "no assigned2id user";
} elseif (!$assignedbyid) {
    echo -1;
    echo "\n";
    echo "no assignedbyid user";
} elseif (strlen($appeal) < 3) {
    echo -1;
    echo "\n";
    echo "appeal less than 3";
} else {
    // Создаем название задачи
    // Добавляем задачу в СУП
    switch ($isname) {
        case "stiseochered":
            $title = opengoo_get_projectname_by_projectID($projectids[$mainprojectname]) . " : " . $data[Фамилия] . " " . $data[Имя] . " " . $data[Отчество];
            echo $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            break;

        case "stismdbu":
            $projectid = $_REQUEST["projectids"][Тип_звонка];
            $title = "МДБУ: " . opengoo_get_projectname_by_projectID($projectids[Тип_звонка]) . " : " . $data[Фамилия] . " " . $data[Имя] . " " . $data[Отчество];
            echo $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            break;

        case "oktell":
            $title = opengoo_get_projectname_by_projectID($projectids[$mainprojectname]) . " : " . $data[Фамилия] . " " . $data[Имя] . " " . $data[Отчество];
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            break;

        case "cceducation":
            logger("test", "error");
            $title = opengoo_get_projectname_by_projectID($projectids[$mainprojectname]) . " : " . $data[Фамилия] . " " . $data[Имя] . " " . $data[Отчество];
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            break;

        case "mobilefeedback":
            $title = "test-" . $title;
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            logger("taskid for mobile:" . $TASKID, "error", __FUNCTION__);
            break;

        case "moninfomats":
            $oldtaskid = opengoo_search_task_by_titleName($projectid, "{feng} " . $title);
            if ($oldtaskid > 0) {
                logger("comment added to taskid:" . $oldtaskid, "error", __FUNCTION__);
                $comment = $appeal;
                $comment_res = opengoo_insert_task_comment($oldtaskid, $comment, $assignedbyid);
                echo $oldtaskid;
            } else {
                logger("comment not found, adding task", "error", __FUNCTION__);
                $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
                echo $TASKID;
            }
            break;

        case "incidentinfomats":
            $title = $data["Классификация_инцидента"] . " : " . $data[Фамилия] . " " . $data[Имя] . " " . $data[Отчество];
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            break;

        /*  case "stiszags":
            if($_REQUEST['projectid']['ФОС']!="")	$subscriptions2id4project = opengoo_get_project_role($_REQUEST['projectid']['ФОС'],"подписчик",false, true);
                $mergedsubs["user_id"] = array_unique(array_merge($subscriptions["user_id"],$subscriptions2id4project));

            $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$mergedsubs);
            echo $TASKID;
            break; */
        case "stis60": //almaz
            //$title = $data["Классификация_инцидента"]." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
            $assigneduser = opengoo_get_project_role($_REQUEST['projectid']['ТипОшибки'], "исполнитель");
            $subscriptions2id4project = opengoo_get_project_role($_REQUEST['projectid']['ТипОшибки'], "Подписчик", false, true);
            if (is_array($subscriptions2id4project)) {
                foreach ($subscriptions2id4project as $v) {
                    $subpeople[] = $v;
                }
            }
            if ($assigneduser > 1) {
                $assigned2id = $assigneduser;
            }
            $subpeople[] = $assigned2id;
            $mergedsubs = array_unique(array_merge($subscriptions, $subpeople));

            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $mergedsubs);
            echo $TASKID;
            break;
        case "stis11---": // almaz
            //$title = $data["Классификация_инцидента"]." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
            $assigneduser = opengoo_get_project_role($_REQUEST['projectid']['ТипОшибки'], "исполнитель");
            if ($assigneduser > 1) {
                $assigned2id = $assigneduser;
            }
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            break;
        case "stis30---": // almaz
            //$title = $data["Классификация_инцидента"]." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
            $assigneduser = opengoo_get_project_role($_REQUEST['projectid']['ТипОшибки'], "исполнитель");
            if ($assigneduser > 1) {
                $assigned2id = $assigneduser;
            }
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            break;
        case "stis53---": // almaz
            //$title = $data["Классификация_инцидента"]." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
            $assigneduser = opengoo_get_project_role($_REQUEST['projectid']['ТипОшибки'], "исполнитель");
            if ($assigneduser > 1) {
                $assigned2id = $assigneduser;
            }
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            break;


        case "education":
            ob_start();
            $districtid = $_REQUEST["district_id"];
            if ($districtid) {
                $projectid = opengoo_edu_get_District_of_PM($districtid);
                $assigned2id = opengoo_get_project_role($projectid, "исполнитель");
                $titlte = "Вопрос тьютору: " . $title;
                $newsubs = $subscriptions;
                $newsubs[user_id][] = $assigned2id;
                $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $newsubs);

                $output = ob_get_contents();
                ob_end_clean();
                logger("alloutput: " . $output, "error");


            } else {
                $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            }
            echo $TASKID;
            break;

        default:
            $TASKID = opengoo_webservice_insert_task($projectid, $title, $assigned2id, $assignedbyid, $appeal, $data, $subscriptions);
            echo $TASKID;
            break;
    }

    // Добавляем задачи также в другие проекты
    if (is_array($projectids)) {
        if (isset($projectids['Классификатор'])) apply_project_params($TASKID, (array)$projectids['Классификатор']);

        foreach ($projectids as $k => $v) {
            if ($v > 0) {
                $projectid_temp = opengoo_insert_workspace_objects($TASKID, $v, $assignedbyid);
            }
        }
    }

    switch ($isname) {
        case "kindergarten":
            if ($data["org_obr_id"] > 0) {
                $org_obr_id = opengoo_insert_workspace_objects($TASKID, $data["org_obr_id"], $assignedbyid);
            }
            if ($data["problem_id"] > 0) {
                $problem_id = opengoo_insert_workspace_objects($TASKID, $data["problem_id"], $assignedbyid);
            }
            break;

        case "moninfomats":
            $infomatname = "Инфомат" . $data[Номер];
            $projects = opengoo_search_projects($infomatname);
            if (is_array($projects)) {
                logger("infomat found, adding task into it", "error", __FUNCTION__);
                $infomat_id = opengoo_insert_workspace_objects($TASKID, $projects[1][id], $assignedbyid);
            } else {
                logger("infomat not found,creating project and adding task into it", "error", __FUNCTION__);
                $infomatprojectid = opengoo_add_project($infomatname, $data[Инфомат],
                    "'60','7','916','917','1070','999999','0','0','0','0'",
                    '6', 10000029,
                    $UserID = 25, $ProjectColor = 18);
                logger("adding project permission", "error", __FUNCTION__);
                $infomat_id = opengoo_insert_workspace_objects($TASKID, $infomatprojectid, $assignedbyid);
            }
            break;

        case "incidentinfomats":
            if ($d["incidenttype"] > 0) {
                $newprojectid = opengoo_insert_workspace_objects($TASKID, $d["incidenttype"], $assignedbyid);
            }
            if ($d["infomatno"] > 0) {
                $newprojectid = opengoo_insert_workspace_objects($TASKID, $d["infomatno"], $assignedbyid);
            }
            if ($d["service"] > 0) {
                $newprojectid = opengoo_insert_workspace_objects($TASKID, $d["service"], $assignedbyid);
            }
            break;

        default:
            break;
    }

    if ($autoclose == 1) {
        $taskid = opengoo_complete_task($TASKID, $assignedbyid);
    } else {
        logger("nothing", "error", __FUNCTION__);
    }
    if ($TASKID > 1) {
        logger("taskid: $TASKID", "error", __FUNCTION__);
    } else {
        logger("no taskid $TASKID", "error", __FUNCTION__);
    }
}
mysql_close($con);

/*
Отправляем номер задачи в Монго
*/
$url = 'http://85.233.79.237/esb/oktell/index.php';
$data = array('command' => 'taskid', 'sessionid' => $oktellsession, 'taskid' => $TASKID);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

#$res = curl_exec($ch);
?>
