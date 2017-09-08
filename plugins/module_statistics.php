<?
include_once "statusage.php"; //by almaz - usage control
function project_child($ProjectID)
{
    $temp = 0;
    for ($n = 1; $n < 10; $n++) {
        if ($n == 9) {
            $sql = "SELECT id,name from `og_projects` where p9 = $ProjectID and p10 > 0";
            $result = ssql($sql);
            if (is_array($result)) {
                foreach ($result as $sk => $sv) {
                    $mas_child[$temp] = $sv;
                    $temp++;
                }
            }
        } else {
            $sql = "SELECT id,name from `og_projects` where p" . $n . " = $ProjectID and p" . ($n + 1) . " > 0 and p" . ($n + 2) . " = 0";
            $result = ssql($sql);
            if (is_array($result)) {
                foreach ($result as $sk => $sv) {
                    $mas_child[$temp] = $sv;
                    $temp++;
                }
            }
        }
    }
    return $mas_child;
}


function rd($Date)
{
    $arr = explode(".", $Date);
    $str = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
    return $str;
}

function project_task_count($ProjectID, $StartTime = "", $EndTime = "")
{
    if ($StartTime) {
        $sql_start = "AND start_date >= '" . mes(rd($StartTime)) . " 00:00:00' ";
    }
    if ($EndTime) {
        $sql_end = "AND start_date <= '" . mes(rd($EndTime)) . " 23:59:59' ";
    }
    $sql = "
				  SELECT COUNT(*) AS COUNT FROM og_project_tasks WHERE id in (
				  SELECT object_id FROM og_workspace_objects WHERE object_manager = 'ProjectTasks' AND workspace_id = '$ProjectID' 
				  ) AND archived_by_id ='0' AND trashed_by_id = '0'         $sql_start        $sql_end

				  ";
//    echo $sql; //sazan
    $result = ssql($sql);

    return $result[1]["COUNT"];
}

function project_task_details($ProjectID, $StartTime = "", $EndTime = "")
{
    if ($StartTime) {
        $sql_start = "AND start_date >= '" . mes(rd($StartTime)) . " 00:00:00' ";
    }
    if ($EndTime) {
        $sql_end = "AND start_date <= '" . mes(rd($EndTime)) . " 23:59:59' ";
    }
    $sql = "
				  SELECT *  FROM og_project_tasks WHERE id in (
				  SELECT object_id FROM og_workspace_objects WHERE object_manager = 'ProjectTasks' AND workspace_id = '$ProjectID' 
				  ) AND archived_by_id ='0' AND trashed_by_id = '0'         $sql_start        $sql_end

				  ";
    $result = ssql($sql);
    return $result;
}

global $i;
global $total;
function tasks_per_project($ProjectID, $level = '1', $StartTime = "", $EndTime = "")
{
    global $i, $total;
    $projects = project_child($ProjectID);
    if (is_array($projects)) {
        $level++;
        foreach ($projects as $v) {
            $i++;
            if ($i % 2 == 1) {
                $htmlclass = "class='row1'";
            } else {
                $htmlclass = "class='row2'";
            }
            echo "<tr $htmlclass>";
            echo "<td align=>";
            echo "<span title='" . $v["id"] . "'>";
            echo "<div class='level" . $level . "'>";
            echo $v["name"];
            echo "</div>";
            echo "</td>";
            echo "<td align='center'>";
#//	    if($level>2)  {
            echo $count = project_task_count($v["id"], $StartTime, $EndTime);
            $total = $total + $count;
//	    }
            echo "</td>";
//            echo "<td>";
//            echo "</td>";
//            echo "<td>";
//            echo "</td>";
            echo "</tr>";
            tasks_per_project($v["id"], $level, $StartTime, $EndTime);

        }
    }
}

?>