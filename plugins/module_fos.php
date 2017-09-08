<?
include_once "statusage.php"; //by almaz - usage control
include_once "/fengoffice/plugins/module_opengoo.php";

global $globalfields;
function make_fieldtypes()
{
    global $globalfields;
    $fieldtypes = opengoo_list_subprojects(2776);
    foreach ($fieldtypes as $k => $v) {
        $fieldxml = opengoo_get_projectdescription_by_projectID($v["id"]);
        $fieldarr = xml2array($fieldxml);
        $globalfields[$fieldarr["xml"]["id"]]["id"] = $fieldarr["xml"]["id"];
        $globalfields[$fieldarr["xml"]["id"]]["name"] = $fieldarr["xml"]["name"];
        $globalfields[$fieldarr["xml"]["id"]]["comment"] = $fieldarr["xml"]["comment"];
        $globalfields[$fieldarr["xml"]["id"]]["multiple"] = $fieldarr["xml"]["multiple"];
        $globalfields[$fieldarr["xml"]["id"]]["function"] = $fieldarr["xml"]["function"];
    }
}

make_fieldtypes();


function build_fields($Data)
{
    $html .= "<p>";
    foreach ($Data["0"]["field"] as $mk => $field) {
        $name = $field["name"];
        $fieldtype = $field["fieldtype"];
        $fieldinput = $field["fieldinput"];
        $fieldname = $field["fieldname"];
        $html .= "<div class='h'>";
        if ($fieldinput || $fieldtype) {
            $colspan = "";
        } else {
            $colspan = "style='width:90%;'";
        }
        if ($field["isrequired"]) {
            $required = "<ins class='rq'>*</ins>";
        } else {
            $required = "";
        }
        $html .= "<label for='' $colspan><span>" . $field["name"] . ": $required</span></label>";
        if ($fieldinput && !$fieldtype) {
            $html .= $fieldinput;
        }
        if ($fieldtype) {
            if (!$fieldname) {
                if ($name) {
                    $fieldname = $name;
                } else {
                    $fieldname = $mk;
                }
            }
            $html .= "\t\t" . feedback_fieldtype("addons[" . $fieldname . "]", $fieldname, $fieldtype) . "\n";
        }
        $html .= "</div>";
    }
    $html .= "</p>";
    return $html;
}


function project_child($ProjectID)
{
    logger("test", "error", __FUNCTION__);
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

function get_fields($ProjectID, $type = "html")
{
    global $globalfields;
    $xml = opengoo_get_projectdescription_by_projectID($ProjectID);
    $xml_arr = xml2array($xml);
    $fieldtypes = explode(",", $xml_arr["xml"]["fieldtypes"]);
    if (is_array($fieldtypes)) {
        foreach ($fieldtypes as $k => $v) {
            if ($v > 0) {
                $field = $globalfields[$v];
                if ($type == "html") {
                    $fields .= make_field($field);
                } else {
                    $fields[$k] = build_field($field);
                }
            }
        }
    }
    return $fields;
}

function build_field($FieldData)
{
    $id = $FieldData["id"];
    $name = $FieldData["name"];
    $required = $FieldData["required"];
    $comment = $FieldData["comment"];
    $field["showname"] = $name;
    $field["fieldname"] = $name;
    $field["isrequired"] = $required;
    $field["comment"] = $comment;
    $field["type"] = build_fieldtype($FieldData);
    return $field;
}

function build_fieldtype($FieldData)
{
    $id = $FieldData["id"];
    switch ($id) {
        case "1":
            $res = "file";
            break;
        case "2":
            $res = "maps";
            break;
        case "3":
            $res = "textarea";
            break;
        default:
            $res = "text";
            break;
    }
    return $res;
}

function make_field($FieldData)
{
    $id = $FieldData["id"];
    $name = $FieldData["name"];
    $required = $FieldData["required"];
    $comment = $FieldData["comment"];
    $id = rand();
    $field = "\t\t<div class='h' id='$id'>\n";
    $field .= "\t\t\t<label for='$id'>";
    $field .= "<span>" . $name;
    if ($required) {
        $field .= " <ins class='rq'>*</ins>";
    }
    $field .= "\t</span></label>\n";
    $field .= get_fieldtype($FieldData);
    if ($comment) {
        $field .= "<p class=\"f-hint\">$comment</p>";
    }
    $field .= "\t\t</div>\n";
    return $field;
}

function get_fieldtype($FieldData)
{
    $id = $FieldData["id"];
    switch ($id) {
        case "1":
            $res = "<input type=\"file\"/>";
            break;
        case "2":
            //    $res = "<iframe src=\"http://s.tatarstan.org/test/\" frameborder=\"0\" name=\"ymap\" width=\"350\" height=\"200\"></iframe>";
            $res = "<div class=\"YMapsID\" style=\"width:350px;height:200px\"></div>";
            break;
        case "3":
            $rid = "txt" . rand();
            $res = "<textarea id='" . $rid . "' class='max280'></textarea><!--<input size=2 id='ch" . $rid . "'/>-->";
            break;
        default:
            $res = "<input value=''/>";
            break;
    }
    return $res;
}

function get_maxim($ProjectID)
{
    $maximdata = opengoo_get_projectdescription_by_projectID($ProjectID);
    $maxim_arr = xml2array($maximdata);
    $maxim = $maxim_arr["xml"]["maxim"];
    return $maxim;
}

function get_tag_data($Count, $Maxim = false)
{
    $dpx = 9;
    if ($Maxim > $Count) {
        $Count = $Maxim;
    }
    $project_count = $Count;
    if ($project_count < 1) {
        $px = $dpx;
        $cr = "#4D94DB";
    } elseif ($project_count < 5) {
        $px = $dpx + 3;
        $cr = "#3385D6";
    } elseif ($project_count < 100) {
        $px = $dpx + 5;
        $cr = "#1975D1";
    } elseif ($project_count < 125) {
        $px = $dpx + 7;
        $cr = "#0066CC";
    } elseif ($project_count < 150) {
        $px = $dpx + 9;
        $cr = "#005CB8";
    } elseif ($project_count < 175) {
        $px = $dpx + 11;
        $cr = "#0052A3";
    } elseif ($project_count < 200) {
        $px = $dpx + 13;
        $cr = "#00478F";
    } elseif ($project_count < 300) {
        $px = $dpx + 15;
        $cr = "#003D7A";
    } else {
        $px = $dpx + 17;
        $cr = "#003366";
    }
    $res["px"] = $px;
    $res["cr"] = $cr;
    return $res;
}

function count_tasks_per_project($ProjectID)
{
    $sql = "
                                  SELECT count(*) AS COUNTAS
                                  FROM a_statistics_projects_and_tasks
                                  WHERE workspace_id = '" . mes($ProjectID) . "'
                                  AND archived_by_id ='0'
                                  AND trashed_by_id = '0'
                                  ";
    $res = ssql($sql);
    return $res[1][COUNTAS];
    //  print_r(project_child($ProjectID));
}

function fos_bbCode($text)
{
    preg_match("#\[rus\](.*)\[\/rus\]#is", $text, $rusLng);
    preg_match("#\[tat\](.*)\[\/tat\]#is", $text, $tatLng);
    if (!$rusLng && !$tatLng) {
        return array(
            'ru' => $text,
            'ta' => NULL
        );
    }
    return array(
        'ru' => (($rusLng) ? $rusLng[1] : NULL),
        'ta' => (($tatLng) ? $tatLng[1] : NULL)
    );
}

function fos_bbCodeByLang($text, $lang)
{
    preg_match("#\[" . $lang . "\](.*)\[\/" . $lang . "\]#is", $text, $matches);
    return (isset($matches[1])) ? $matches[1] : $text;
}

?>
