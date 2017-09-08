<?
include_once "statusage.php"; //by almaz - usage control
include_once '/fengoffice/plugins/functions.php';
//#education

function opengoo_edu_get_District_of_PM($EDUTATARRUID)
{
    $sql = "SELECT PM FROM reference_Districts WHERE EDUTATARRU  = '" . $EDUTATARRUID . "' ";
    $res = ssql($sql);
    return $res[1]["PM"];
}

function opengoo_get_project_name($ProjectID, $Convert = true)
{
    $sql = " SELECT name FROM og_projects WHERE id = '$ProjectID'";
    $orslt = ssql($sql);
    if ($Convert) {
        return str2utf($orslt[1]["name"], "w");
    } else {
        return $orslt[1]["name"];
    }
}

function opengoo_list_subprojects($ProjectID, $Level = 1)
{
    $postlevel = $Level + 2;
    $temp = 0;
    $flag = 0;
    for ($i = 2; $i <= 10; $i++) {
        $plevelsql = " ";
        for ($z = $i + 1; $z <= 10; $z++) {
            $plevelsql .= " AND p" . $z . "=0";
        }
        $sql = "SELECT id,name,description FROM og_projects WHERE p" . ($i - 1) . "=" . $ProjectID . " AND p" . $i . ">0" . $plevelsql . " ORDER BY name";

        $result = ssql($sql);
        if ($result) {
            return $result;
        }
    }
    $sql = "SELECT id FROM og_projects WHERE p" . $Level . " = '$ProjectID' AND p" . $postlevel . " = '0'";//??? почему этот код здесь?
    $orslt = ssql($sql);
    return $orslt;
}

function opengoo_internal_objecttype_convertor($ObjectType)
{
    switch ($ObjectType) {
        case "task":
            $object_manager = "ProjectTasks";
            break;
    }
    return $object_manager;
}

function opengoo_list_objects_by_projectid($ProjectID, $ObjectType)
{
    $object_manager = opengoo_internal_objecttype_convertor($ObjectType);
    $sql = "SELECT * FROM og_workspace_objects WHERE workspace_id = '$ProjectID' AND object_manager = '$object_manager' ORDER BY created_on DESC";
    $res = ssql($sql);
    return $res;
}

function opengoo_get_object_info_by_objectid($ObjectID, $ObjectType)
{
    switch ($ObjectType) {
        case "task":
            return opengoo_get_task_info_by_objectid($ObjectID);
            break;
    }
}

function opengoo_get_task_info_by_objectid($TaskID)
{
    return opengoo_get_task_info($TaskID);
}

function opengoo_get_object_properties($ObjectID, $ObjectType)
{
    $rel_object_manager = opengoo_internal_objecttype_convertor($ObjectType);
    $sql = "SELECT * FROM og_object_properties WHERE rel_object_id = '$ObjectID' AND rel_object_manager = '$rel_object_manager' ORDER BY id ASC";
    $res = ssql($sql);
    return $res;
}

function opengoo_get_task_info($TaskID)
{
    $sql = "SELECT * FROM og_project_tasks WHERE id = '$TaskID'";
    $taskinfo = ssql($sql);
    if (is_array($taskinfo)) {
        $taskdetails["info"] = $taskinfo[1];
        $taskdetails["properties"] = opengoo_get_object_properties($TaskID, 'task');
    } else {
        return false;
    }
    return $taskdetails;
}

function do_offset($level)
{
    $offset = "";             // offset for subarry
    for ($i = 1; $i < $level; $i++) {
        $offset = $offset . "<td></td>";
    }
    return $offset;
}

function show_array($array, $level, $sub)
{
    if (is_array($array) == 1) {          // check if input is an array
        foreach ($array as $key_val => $value) {
            $offset = "";
            if (is_array($value) == 1) {   // array is multidimensional
                echo "<tr>";
                $offset = do_offset($level);
                echo $offset . "<td>" . $key_val . "</td>";
                show_array($value, $level + 1, 1);
            } else {                        // (sub)array is not multidim
                if ($sub != 1) {          // first entry for subarray
                    echo "<tr nosub>";
                    $offset = do_offset($level);
                }
                $sub = 0;
                echo $offset . "<td main " . $sub . " width=\"120\">" . $key_val .
                    "</td><td width=\"120\">" . $value . "</td>";
                echo "</tr>\n";
            }
        } //foreach $array
    } else { // argument $array is not an array
        return;
    }
}

function html_show_array($array)
{
    echo "<table cellspacing=\"0\" border=\"2\">\n";
    show_array($array, 1, 0);
    echo "</table>\n";
}

?>
