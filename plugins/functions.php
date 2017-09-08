<?
include_once "statusage.php"; //by almaz - usage control
include_once "module_xml.php";
include_once "module_opengoo.php";

function NOW()
{
    date_default_timezone_set('UTC');
    return date("Y.m.d H:i:s");
}

function mes($str)
{
    return mysql_escape_string($str);
}

function logger($log_string,
                $type = "log",
                $FUNCTIONNAME = false)
{
    $text = '';
    //2011-07-12 00:19:21
    $_nfms["FUNCTIONNAME"] = 'function logger()';
    $t = microtime(true);
    $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
    $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
    //$text .= date('Y-m-d H:i:s:u')."\t";
    $text .= $d->format("Y-m-d H:i:s.u") . "\t";
    $text .= $_SERVER['REMOTE_ADDR'] . "\t";
    $text .= $type . "\t";
    $text .= $FUNCTIONNAME . "\t";
    $text .= $_SERVER["REQUEST_URI"] . "\t";
    $text .= $log_string . "\n";
    $logfile = "files/opengoo.post." . date('Y-m-d') . ".log";
    if (!is_writable($logfile)) {
    };
    if (!$handle = fopen($logfile, 'a')) {
        return;
    }
    if (!fwrite($handle, $text)) {
        return;
    }
    fclose($handle);
}

function loggerOLD($log_string, $type = "log", $FUNCTIONNAME = false)
{
    $date = date('Y-m-d H:i:s');
    $text = '';
    $text .= $date . "\t";
    $text .= $_SERVER['REMOTE_ADDR'] . "\t";
    $text .= $type . "\t";
    $text .= $FUNCTIONNAME . "\t";
    $text .= $log_string . "\n";
    if ($type == "error") {
        if (!$logfile) {
            $logfile = "files/opengoo.post.log";
        };
        if (!is_writable($logfile)) {
        };
        if (!$handle = fopen($logfile, 'a')) {
            return;
        }
        if (!fwrite($handle, $text)) {
            return;
        }
        fclose($handle);
    }
}

if (count($_REQUEST) >= 1) {
    //  logger(print_r($_REQUEST,true),"error",__FUNCTION__);
} else {
    //  logger("no request data","error");
}

function ssql($sql, $ENCODING = false)
{
    $rowcount = 1;
    global $con;
    if (!$ENCODING) {
        $ENCODING = 'utf8';
    }
    $sql1 = "set character set '$ENCODING'";
    mysql_query($sql1);
    $sql1 = "set names $ENCODING";
    mysql_query($sql1);
    mysql_query($sql);
    $result = mysql_query($sql);
    if ($result) {
        while ($row = mysql_fetch_array($result)) {
            if (is_array($row)) {
                foreach ($row as $k => $v) {
                    if (!is_numeric($k)) {
                        $array[$rowcount][$k] = $v;
                    }
                }
                $rowcount++;
            }
        }
    }
    if (isset($array)) {
        if (is_array($array)) {
            return $array;
        } else {
            return false;
        }
    }
}

function fpc($FileName, $Data, $isAppend = false)
{
    $filename = "/fengoffice/plugins/debuglogs/" . $FileName . ".log";
    //// file_put_contents for debug purposes
    global $Debug;
    $Debug = false; //sazan
    if ($Debug) {
        if (is_array($Data)) {
            $data = print_r($Data, 1);
        } else {
            $data = $Data;
        }
        if ($isAppend) {
            file_put_contents($filename, $data . "\n", FILE_APPEND);
        } else {
            file_put_contents($filename, $data);
        }
    }
}

global $Debug;
$Debug = 1;
function usql($sql, $ENCODING = false)
{
    global $con;
    global $Debug;
    if (!$ENCODING) {
        $sql1 = "set character set 'utf8'";
        $sql1 = "set names utf8";
        mysql_query($sql1);
    }
    if ($Debug) {
        $start = microtime(1);
    }
    $result = mysql_query($sql);
    if ($Debug) {
        $difftime = microtime(1) - $start;
        if ($difftime > 0.01) {
            $longsql = $difftime . "\t" . $sql . "\n\n\n\n\n\n\n\n";
            fpc("sqls", $longsql, 1);
        }
    }
// if($ENCODING) { mysql_query("SET NAMES latin1");}
    $id = mysql_insert_id();
    if ($id) {
        return $id;
    } else {
        return $result;
    }
}

function opengoo_webservice_insert_task($ProjectID, $Title, $Assigned2ID, $AssignedByID, $Appeal, $Data, $Subscriptions = false)
{
    $Title = "{feng} " . $Title;
    $properties = '';
    $newtaskid = opengoo_insert_project_task($Title, $Appeal, $Assigned2ID, $AssignedByID);
    if ($newtaskid > 0) {
        $stick_task_with_project = opengoo_insert_workspace_task($newtaskid, $ProjectID, $AssignedByID);
        //adding searchable objects
        $searchable_taskid = opengoo_insert_searchable_tasks($newtaskid, 'uid', $newtaskid);
        $searchable_title = opengoo_insert_searchable_tasks($newtaskid, 'title', $Title);
        $searchable_text = opengoo_insert_searchable_tasks($newtaskid, 'text', $Appeal);
        if (is_array($Data)) {
            foreach ($Data as $k => $v) {
                $properties .= opengoo_task_property_4_email($k, $v);
                $propertyid = "property" . opengoo_insert_task_property($newtaskid, $k, $v);
                $temp = opengoo_insert_searchable_tasks($newtaskid, $propertyid, $v);
            }
        }
        $emailbody = opengoo_task_email_body($newtaskid, $Title, $Appeal, $properties, $Assigned2ID);

        if (is_array($Subscriptions)) {
            foreach ($Subscriptions["user_id"] as $k => $v) {
                $do_subscriptions = opengoo_insert_task_subscription($newtaskid, $v);
                $send_reminder = opengoo_insert_task_reminder($newtaskid, $v);
                $display_name = opengoo_get_display_name_of_userid($Assigned2ID);
                $emails = opengoo_insert_queued_email($v, $AssignedByID, 'Задача _...."' . $Title . '" возложена на ' . $display_name, $emailbody);
            }
        } elseif ($Subscriptions) {
            $do_subscriptions = opengoo_insert_task_subscription($newtaskid, $Subscriptions);
            $send_reminder = opengoo_insert_task_reminder($newtaskid, $Subscriptions);
            $display_name = opengoo_get_display_name_of_userid($Assigned2ID);
            $emails = opengoo_insert_queued_email($Subscriptions, $AssignedByID, 'Задача ....."' . $Title . '" возложена на' . $display_name, $emailbody);
        }
        $read = opengoo_insert_task_read($newtaskid, $AssignedByID);
    }//end of if($newtaskid)
    if ($newtaskid > 0) {
        return $newtaskid;
    } else {
        return -1;
    }
}

function opengoo_webservice_insert_task2($ProjectID, $Title, $Assigned2ID, $AssignedByID, $Appeal, $Data, $Subscriptions = false)
{
    $properties = '';
    $newtaskid = opengoo_insert_project_task($Title, $Appeal, $Assigned2ID, $AssignedByID);
    if ($newtaskid > 0) {
        $stick_task_with_project = opengoo_insert_workspace_task($newtaskid, $ProjectID, $AssignedByID);
        //adding searchable objects
        $searchable_taskid = opengoo_insert_searchable_tasks($newtaskid, 'uid', $newtaskid);
        $searchable_title = opengoo_insert_searchable_tasks($newtaskid, 'title', $Title);
        $searchable_text = opengoo_insert_searchable_tasks($newtaskid, 'text', $Appeal);
        if (is_array($Data)) {
            foreach ($Data as $k => $v) {
                $properties .= opengoo_task_property_4_email($k, $v);
                $propertyid = "property" . opengoo_insert_task_property($newtaskid, $k, $v);
                $temp = opengoo_insert_searchable_tasks($newtaskid, $propertyid, $v);
            }
        }
        $emailbody = opengoo_task_email_body($newtaskid, $Title, $Appeal, $properties, $Assigned2ID);

        if (is_array($Subscriptions)) {
            foreach ($Subscriptions as $k => $v) {
                $do_subscriptions = opengoo_insert_task_subscription($newtaskid, $v);
                $send_reminder = opengoo_insert_task_reminder($newtaskid, $v);
                $display_name = opengoo_get_display_name_of_userid($Assigned2ID);
                $emails = opengoo_insert_queued_email($v, $AssignedByID, 'Задача ....."' . $Title . '" возложена на ' . $display_name, $emailbody);
            }
        }
        $read = opengoo_insert_task_read($newtaskid, $AssignedByID);
    }//end of if($newtaskid)
    if ($newtaskid > 0) {
        return $newtaskid;
    } else {
        return -1;
    }
}


function opengoo_insert_project_task($TaskTitle, $TaskText, $Assigned2UserID, $AssignedByID, $Milestoneid = '0')
{
    $now = NOW();
    $assigned_to_company_id = opengoo_get_companyid_of_userid(1);//$Assigned2UserID);
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "project_tasks (
 `parent_id`, `title`, `text`, `due_date`, `start_date`, `assigned_to_company_id`, `assigned_to_user_id`, `assigned_on`, 
 `assigned_by_id`, `time_estimate`, `completed_on`, `completed_by_id`, `created_on`, `created_by_id`, `updated_on`, `updated_by_id`, 
 `trashed_on`, `trashed_by_id`, `archived_on`, `archived_by_id`, `started_on`, `started_by_id`, `priority`, `state`, 
 `order`, `milestone_id`, `is_private`, `is_template`, `from_template_id`, `repeat_end`, `repeat_forever`, `repeat_num`, 
 `repeat_d`, `repeat_m`, `repeat_y`, `repeat_by`, `object_subtype`
 ) 
 VALUES (
 '0', '$TaskTitle', '$TaskText', '$now', '$now','$assigned_to_company_id','$Assigned2UserID', '$now', 
 '$AssignedByID', '0', '0000-00-00 00:00:00', '0', '$now', '$AssignedByID', '$now', '$AssignedByID', 
 '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '200', '0', 
 '0','$Milestoneid', '0', '0', '0', '0000-00-00 00:00:00', '0', '0', 
 '0', '0', '0', '', '1'
 )";
    $lastid = mysql_insert_id();
    //mysql_query($sql);
    return usql($sql);
// return $lastid;
//return $sql;
}

function webservice_list_workspace_objects($ProjectID, $ObjectType = false, $Page = 1, $Rows = 20)
{
    $limit_sql = " LIMIT " . ($Page - 1) * $Rows . "," . ($Rows);
    if (!$ObjectType) {
        $object_manager_sql = " AND object_manager != 'ApplicationLogs' AND object_manager != 'ApplicationReadLogs'";
        $sql = "SELECT * FROM og_workspace_objects WHERE workspace_id = '$ProjectID' $object_manager_sql $limit_sql ";
    } else {
        $object_manager_arr = explode(",", $ObjectType);
        if (is_array($object_manager_arr)) {
            foreach ($object_manager_arr as $v) {
                $object_manager_sql_temp .= " OR object_manager = '$v' ";
            }
        }
        $object_manager_sql = " object ( $object_manager_sql_temp ) ";
        $sql = "
    SELECT * FROM og_workspace_objects WHERE object_id in (
      SELECT object_id FROM og_workspace_objects WHERE object_manager IN ('$ObjectType')
    )";
    }
    //echo $sql;
    $res = ssql($sql);
    if (is_array($res)) {
        foreach ($res as $k => $v) {
            $new_res[$k] = $v;
            if ($v["object_manager"] == "ProjectMessages") {
                $news = ssql("SELECT * FROM og_project_messages WHERE id = '" . $v["object_id"] . "'");
                $new_res[$k]["object_title"] = $news[1]["title"];
                $new_res[$k]["object_text"] = $news[1]["text"];
            }
        }
    }
    return $new_res;//print_r($res);
}

function opengoo_get_project_notes($ProjectID, $Limit = 3)
{
    $sql = "SELECT 
		id,title,text 
	FROM 
		og_project_messages 
	WHERE 
		ID IN (SELECT object_id FROM og_workspace_objects WHERE workspace_id = '$ProjectID')
		AND
		trashed_by_id = 0
	";
    if ($Limit && $Limit > 0) {
        $sql .= " LIMIT $Limit";
    }
    $res = ssql($sql);
    return $res;
}

function opengoo_get_project_xml($ProjectID)
{
    $description = opengoo_get_projectdescription_by_projectID($ProjectID);
    return $description;
}

function opengoo_add_project($ProjectName, $ProjectDescription, $Projects, $PID, $GroupID, $UserID = 25, $ProjectColor = 18)
{
    $sql = "INSERT INTO `og_projects`
    (`name`,
     `description`, `show_description_in_overview`,
     `completed_on`, `completed_by_id`, `created_on`, `created_by_id`, `updated_on`, `updated_by_id`,
     `color`, `parent_id`,
     `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`, `p9`, `p10`)
VALUES
    ('$ProjectName',
     '$ProjectDescription','1',
     '0000-00-00 00:00:00','0',NOW(),'$UserID',NOW(),'$UserID',
     '$ProjectColor','0',
     $Projects)
";
    $projectid = usql($sql);
    $sql1 = "UPDATE og_projects SET p" . $PID . " = id WHERE p" . $PID . " = '999999'";
    usql($sql1);
    $mmm = opengoo_add_project_permission($projectid, $GroupID);
    return $projectid;
}

function opengoo_add_project_permission($ProjectID, $GroupID)
{
    $sql = "
INSERT INTO  `fengoffice`.`og_project_users` (`project_id` ,`user_id` ,`created_on` ,`created_by_id` ,`can_read_messages` ,`can_write_messages` ,`can_read_tasks` ,`can_write_tasks` ,`can_read_milestones` ,`can_write_milestones` ,`can_read_files` ,`can_write_files` ,`can_read_events` ,`can_write_events` ,`can_read_weblinks` ,`can_write_weblinks` ,`can_read_mails` ,`can_write_mails` ,`can_read_contacts` ,`can_write_contacts` ,`can_read_comments` ,`can_write_comments` ,`can_assign_to_owners` ,`can_assign_to_other`)
VALUES ( '$ProjectID',  '$GroupID',  NOW(),  '25',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1'
)
";
    logger($sql, "error", __FUNCTION__);
    $og = usql($sql);
    return $og;
}


function opengoo_insert_searchable_tasks($TaskID, $ColumnName, $Content)
{
    $sql = "INSERT INTO " . TABLE_PREFIX . "searchable_objects (`rel_object_manager`,`rel_object_id`,`column_name`,`content`,`project_id`,`is_private`,`user_id`)
         VALUES ('ProjectTasks','$TaskID','$ColumnName','$Content','0','0','0')
         ";
    $res = usql($sql);
    return res;
}


function opengoo_insert_workspace_objects($TaskID, $ProjectID, $AssignedByID)
{
    $now = NOW();
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "workspace_objects (
 `workspace_id`, `object_manager`, `object_id`, `created_by_id`, `created_on`
 )
 VALUES (
 '$ProjectID','ProjectTasks','$TaskID','$AssignedByID','$now'
 )
 ";
    return usql($sql);
}

function opengoo_insert_task_reminder($TaskID, $Assigned2UserID, $Minutes = 1440)
{
    $Date = '0000-00-00 00:00:00';//NOW();
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "object_reminders (
 `object_id`,`object_manager`,`user_id`,`type`,`context`,`minutes_before`,`date`
 )
 VALUES (
 '$TaskID','ProjectTasks','0','reminder_email','due_date','$Minutes','$Date'
 )";
    return 1;
    // return usql($sql);
}

function opengoo_insert_task_subscription($TaskID, $Assigned2UserID)
{
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "object_subscriptions (`object_id`,`object_manager`,`user_id`
 )
 VALUES (
 '$TaskID','ProjectTasks','$Assigned2UserID'
 )";
    return usql($sql);
}

function opengoo_insert_queued_email($ToID, $FromID, $Subject, $Body)
{
    $now = NOW();
    $to = opengoo_get_email_of_userid($ToID);
    $from = opengoo_get_email_of_userid($FromID);
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "queued_emails (`to`,`from`,`subject`,`body`,`timestamp`
 )
 VALUES (
 '$to','$from','$Subject','$Body','$now'
 )";
    return usql($sql);
}

function opengoo_insert_queued_email_without_feng($ToEmail, $FromEmail, $Subject, $Body)
{
    $now = NOW();
    $to = $ToEmail;
    $from = $FromEmail;
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "queued_emails (`to`,`from`,`subject`,`body`,`timestamp`
 )
 VALUES (
 '$to','$from','$Subject','$Body','$now'
 )";
    return usql($sql);
}


function opengoo_insert_task_read($TaskID, $AssignedByID)
{
    $now = NOW();
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "read_objects (`rel_object_manager`,`rel_object_id`,`user_id`,`is_read`,`created_on`
 )
 VALUES (
 'ProjectTasks','$TaskID','$AssignedByID','1','$now'
 )";
    return usql($sql);
}

function opengoo_insert_workspace_task($TaskID, $WorkspaceID, $AssignedByID)
{
    $now = NOW();
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "workspace_objects (
 `workspace_id`,`object_manager`,`object_id`,`created_by_id`,`created_on`
 )
 VALUES (
 '$WorkspaceID','ProjectTasks','$TaskID','$AssignedByID','$now'
 )";
    $sql;
    return usql($sql);
}

function opengoo_task_property_4_email($Name, $Value)
{
    return "<b>$Name</b> : $Value <br/>\n";
}

function opengoo_insert_task_property($TaskID, $Name, $Value)
{
    $sql = "
 INSERT INTO " . TABLE_PREFIX . "object_properties (
 `rel_object_id`,`rel_object_manager`,`name`,`value`
 )
 VALUES (
 '$TaskID','ProjectTasks','$Name','$Value'
 )";
    $propertyid = usql($sql);
    return $propertyid;
}

function opengoo_get_companyid_of_userid($UserID)
{    //returns company_id(int)
    $sql = "
  SELECT company_id
  FROM " . TABLE_PREFIX . "users
  WHERE	id='$UserID'
 ";
    $userdata_arr = ssql($sql);
    return $userdata_arr[1]["company_id"];
}

function opengoo_get_email_of_userid($UserID)
{    //returns company_id(int)
    $sql = "
  SELECT email
  FROM " . TABLE_PREFIX . "users
  WHERE	id='$UserID'
 ";
    $userdata_arr = ssql($sql);
    return $userdata_arr[1]["email"];
}

function opengoo_get_display_name_of_userid($UserID)
{    //returns company_id(int)
    $sql = "
  SELECT display_name
  FROM " . TABLE_PREFIX . "users
  WHERE	id='$UserID'
 ";
    $userdata_arr = ssql($sql);
    return $userdata_arr[1]["display_name"];
}

function opengoo_get_projectname_by_taskID($TaskID)
{
    $sql = "SELECT workspace_id FROM og_workspace_objects WHERE object_id = '$TaskID' AND object_manager = 'ProjectTasks'";
    $res = ssql($sql);
    if (is_array($res[1])) {
        $sql = "SELECT name FROM og_projects WHERE id = '" . $res[1]["workspace_id"] . "'";
        $res = ssql($sql);
        return $res[1]["name"];
    } else {
        return false;
    }
}

function opengoo_get_projectname_by_projectID($ProjectID)
{
    $sql = "SELECT name FROM og_projects WHERE id = '" . $ProjectID . "'";
    $res = ssql($sql);
    return $res[1]["name"];
}

function opengoo_search_task($String, $FieldName = "Внешний_номер", $IsLikeMode = false, $SearchByName = true)
{
    if ($IsLikeMode) {
        $String_sql = "oop.value LIKE ('%$String%')";
    } else {
        $String_sql = "oop.value = '$String'";
    }
    if ($SearchByName) {
        $SearchName_sql = "AND 	oop.name = '$FieldName'";
    }
    $sql = "
    SELECT	id,title,text,assigned_by_id,assigned_on,completed_by_id,completed_on,trashed_by_id 
    FROM 	og_project_tasks 
    WHERE 	id IN (
    		SELECT	oop.rel_object_id AS id
         	FROM 	og_object_properties AS oop
         	WHERE 	oop.rel_object_manager =  'ProjectTasks'
         	$SearchName_sql
         	AND 	$String_sql
    )
    AND		trashed_by_id = '0'
    ORDER BY 	ID DESC 
    LIMIT 	10
";
    $res = ssql($sql);
    logger($sql, "error", __FUNCTION__);
    if (is_array($res)) {
        foreach ($res as $k => $v) {
            $sql = "
       SELECT	name,value
       FROM 	" . TABLE_PREFIX . "object_properties
       WHERE	rel_object_id = '" . $v["id"] . "'
       AND   	rel_object_manager = 'ProjectTasks'
       ORDER BY id
       ";
            $data = ssql($sql);
            logger($sql, "error", __FUNCTION__);
            if (is_array($data)) {
                foreach ($data as $dk => $dv) {
                    $data_res[$dv["name"]] = $dv["value"];
                }
                $res[$k]["DATA"] = $data_res;
            }
            $sql = "
      SELECT  id,text,updated_on,updated_by_id
      FROM " . TABLE_PREFIX . "comments
      WHERE rel_object_id = '" . $v["id"] . "'
      AND rel_object_manager = 'ProjectTasks'
      AND trashed_by_id = '0'
      ORDER BY id DESC
      ";
            $comments = ssql($sql);
            logger($sql, "error", __FUNCTION__);
            if ($comments) {
                $res[$k]["COMMENTS"] = $comments;
            }
        }
    }
    return $res;
}

function opengoo_get_projectdescription_by_projectID($ProjectID)
{
    $sql = "SELECT description FROM og_projects WHERE id = '" . $ProjectID . "'";
    $res = ssql($sql);
    return $res[1]["description"];
}

function opengoo_get_projectid_by_taskID($TaskID)
{
    $sql = "SELECT workspace_id FROM og_workspace_objects WHERE object_id = '$TaskID' AND object_manager = 'ProjectTasks'";
    $res = ssql($sql);
    return $res[1]["workspace_id"];
}


function opengoo_get_projectparentid_by_projectID($ProjectID)
{
    $sql = "SELECT p1,p2,p3,p4,p5,p6,p7,p8,p9,p10 FROM og_projects WHERE id = '$ProjectID'";
    $res = ssql($sql);
    for ($i = 1; $i <= 10; $i++) {
        $level = "p" . $i;
        if ($res[1][$level] == $ProjectID) {
            $parent = "p" . ($i - 1);
        }
    }
    return $res[1][$parent];
}

function opengoo_complete_task($TaskID, $CompletedByID)
{
    if ($TaskID && $CompletedByID) {
        $now = NOW();
        $sql = "UPDATE og_project_tasks  SET completed_on = '$now', completed_by_id = '$CompletedByID' WHERE id = '$TaskID'";
        $res = usql($sql);
    }
    return $res;
}

function opengoo_search_task_by_titleName($ProjectID, $TitleName)
{
    $sql = "
	 SELECT		opt.id AS TASKID
	 FROM 		og_project_tasks AS opt,
	 		og_workspace_objects AS owo
	 WHERE 		opt.title = '" . $TitleName . "'
	 AND		opt.id = owo.object_id
	 AND		owo.object_manager = 'ProjectTasks'
	 AND		owo.workspace_id = '" . $ProjectID . "'
	 AND		opt.trashed_by_id = '0'
	 AND		opt.completed_by_id = '0'
	 ORDER BY opt.id DESC
	 LIMIT 1
	 ";
    mysql_query("set names utf8");
    $res = ssql($sql, "utf8");
    logger("taskid:" . $res[1]["TASKID"], "error", __FUNCTION__);
    return $res[1]["TASKID"];
}

function opengoo_insert_task_comment($TaskID, $Comment, $AssignedByID)
{
    $sql = "
	 INSERT INTO og_comments 
	 (`rel_object_id`,`rel_object_manager`,`text`,`is_private`,`is_anonymous`,`created_on`,`created_by_id`,`updated_on`,`updated_by_id`,`trashed_by_id`)
	 VALUES 
	 ('$TaskID','ProjectTasks','$Comment','0','0','" . NOW() . "','$AssignedByID','" . NOW() . "','$AssignedByID','0')
	 ";
    $res = usql($sql);
    return $res;
}

function opengoo_get_project_role($ProjectID, $Role, $Level = 0, $AllRoles = false)
{
    if ($Level == 1) {
        $ProjectID = opengoo_get_projectparentid_by_projectID($ProjectID);
    }
    $sql = "SELECT contact_id FROM og_project_contacts WHERE project_id = '$ProjectID' AND role = '$Role'";
    $res = ssql($sql);
    if ($AllRoles == false) {
        if ($res[1]["contact_id"]) {
            $sql = "SELECT user_id FROM og_contacts WHERE id = '" . $res[1]["contact_id"] . "'";
            $res = ssql($sql);
            return $res[1]["user_id"];
        }
    } else {
        if (is_array($res)) {
            foreach ($res as $v) {
                $sql = "SELECT user_id FROM og_contacts WHERE id = '" . $v["contact_id"] . "'";
                $res = ssql($sql);
                $res_v[] = $res[1]["user_id"];
            }
            return $res_v;
        }
    }
}

function opengoo_task_email_body($TaskID, $Subject, $Body, $Properties, $UserID)
{
    //  $Subject = "{feng}".$Subject;
    $display_name = opengoo_get_display_name_of_userid($UserID);
    $projectname2 = opengoo_get_projectname_by_taskID($TaskID);
    $projectname1 = opengoo_get_projectname_by_projectID(opengoo_get_projectparentid_by_projectID(opengoo_get_projectid_by_taskID($TaskID)));
    $projectname = $projectname1 . " / " . $projectname2;

    $url = 'http://pm.citrt.net/index.php?c=task&a=view_task&id=' . $TaskID;
    $body = '
<div style="font-family: Verdana, Arial, sans-serif; font-size: 12px;">
 <a href="http://pm.citrt.net/index.php?c=task&a=view_task&id=' . $TaskID . '" style="font-size: 18px;">Задача "' . $Subject . '" возложена на ' . $display_name . '</a><br><br>
 <b>Проект/Направление</b>: <span style="border-color: #006633; background-color: #006633; color: #F1F5EC; 
 padding: 1px 5px; font-size: 90%;">
 ' . $projectname . '</span><br><br>
 <b>Ссылка на задачу</b> : <a href="' . $url . '">' . $url . '</a><br/>
 <b>Задача №:</b> : ta' . $TaskID . '
 <br><br>
 ' . $Body . '
 <br/><br/>
 ' . $Properties . '
 <br/><br/>
 <div style="color: #818283; font-style: italic; border-top: 2px solid #818283; padding-top: 2px; font-family: Verdana, Arial, sans-serif; font-size: 12px;">
 Это системное извещение, НЕ отвечайте на это письмо<br>
 <a href="http://pm.citrt.net/index.php?c=files&a=file_details&id=1884" target="_blank" style="font-family: Verdana, Arial, sans-serif; font-size: 12px;">Правила работы в СУПе</a> 
 <a href="http://pm.citrt.net/index.php?c=files&a=file_details&id=1841" target="_blank" style="font-family: Verdana, Arial, sans-serif; font-size: 12px;">Знакомство с СУПом</a>
 </div>
</div>
 ';
    return $body;
}

function search_executor_readers($source, $service, $postincident)
{
    $query = "SELECT * FROM lena_incidents WHERE Active=1 ORDER BY Rank";
    $res = ssql($query);
    foreach ($res as $item) {
        $ressource = unserialize($item["SourceID"]);
        if ($ressource == NULL) $flagsource = true; else $flagsource = false;
        $resservice = unserialize($item["ServiceID"]);
        if ($resservice == NULL) $flagservice = true; else $flagservice = false;

        $resincidents = unserialize($item["IncidentID"]);
        if ($resincidents == NULL) $flagincidents = true; else $flagincidents = false;

        $resexecutor = $item["ExecutorID"];
        $resreaders = unserialize($item["ReadersID"]);
        if (in_array($source, $ressource) || $flagsource) {
            if (in_array($service, $resservice) || $flagservice) {
                foreach ($resincidents as $incident) {
                    $masincident = explode('-', $incident);
                    if ($masincident[2] == $postincident) {
//Добавляю очередную проверку на serviceID
                        if ($masincident[1] == $service) {
//
                            $flagincidents = true;
                            continue;
                        }
                    }
                }
                if ($flagincidents) {
                    $executor = $resexecutor;
                    $readers = $resreaders;
                    $readers[count($readers)] = $executor;
                    $returnresult["executor"] = $executor;
                    $returnresult["readers"] = $readers;
                    return $returnresult;
                }
            }
        }
    }
}


function post_with_curl($PostData, $URL)
{
    $data = http_build_query($PostData);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $URL);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/5.0)');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($curl);
    if (!$res) {
        $error = curl_error($curl) . '(' . curl_errno($curl) . ')';
        return $error;
    }
    curl_close($curl);
    return $res;
}

function apply_project_params($taskid, $projects)
{
    if (!is_numeric($taskid)) return false;
    $priority_values = array('100', '200', '300', '400');
    foreach ($projects as &$project) {
        $projects_params = (array)simplexml_load_string(opengoo_get_project_xml($project));
        if (isset($projects_params['worktime']) && is_numeric($projects_params['worktime']))
            set_worktime($taskid, $projects_params['worktime']); else set_worktime($taskid);
        if (isset($projects_params["priority-class"]) && in_array($projects_params["priority-class"], $priority_values, true))
            ssql("update og_project_tasks set priority = " . $projects_params["priority-class"] . " WHERE id = '$taskid'");
    }
}

function set_worktime($taskid, $worktime = 72)
{
    $begin = get_start_date($taskid);
    $end = $begin[1]['start_date'];
//Достали время
    if (DateTime::createFromFormat('Y-m-d H:i:s', $end) !== FALSE) {
        // it's a date
        while ($worktime > 0) {//Работаем, пока к задаче не добавим все время
            if ($worktime > 24) //Добавляем по дням, если возможно
                $end = (date('Y-m-d H:i:s', strtotime($end . " +24 hours")));
            else $end = (date('Y-m-d H:i:s', strtotime($end . " +" . $worktime . " hours"))); //Ну или остаток дня
            if (!isWeekend($end)) $worktime = $worktime - 24; //Вычитаем рабочие часы, выходные не учитываем
        }
        ssql("update og_project_tasks set due_date = '$end' WHERE id = '$taskid'"); //Обновляем информацию по задаче
    }
    return true;
}

function isWeekend($date)
{
    return (date('N', strtotime($date)) >= 6);
}

function get_start_date($taskid)
{
    return ssql("SELECT start_date FROM og_project_tasks WHERE id = '$taskid'");
}


?>
