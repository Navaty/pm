<?php
//from bzModule
function get_spheres()
{
    $arr = opengoo_list_subprojects(948); //Сферы
    return $arr;
}

function get_sphere_service($ProjectID)
{
    $arr = opengoo_list_subprojects($ProjectID);
    return $arr;
}

function get_places()
{
    $arr = opengoo_list_subprojects(2554);
    return $arr;
}

function get_places2()
{
    $arr = opengoo_list_subprojects(2554);
    return $arr;
}

function is_place_checked($PlaceID, $SphereID, $ServiceID)
{
    $sql = "SELECT ID FROM сс_incidents_projects_places WHERE PlaceID = '$PlaceID' AND SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IsDeleted = '0'";
    $res = ssql($sql);
    if ($res[1]["ID"] > 0) {
        return "checked";
    } else {
        return false;
    }
}

global $globalfields;
function make_fieldtypes2()
{
    global $globalfields;
    $fieldtypes = opengoo_list_subprojects(2568);
    foreach ($fieldtypes as $k => $v) {
        $fieldxml = opengoo_get_projectdescription_by_projectID($v["id"]);
        $fieldarr = xml2array($fieldxml);
        $globalfields[$fieldarr["xml"]["id"]]["name"] = $fieldarr["xml"]["name"];
        $globalfields[$fieldarr["xml"]["id"]]["comment"] = $fieldarr["xml"]["comment"];
    }
}

make_fieldtypes2();

function get_fieldtype2($FieldID, $type = "name")
{
    global $globalfields;
    return $globalfields[$FieldID][$type];
}

function get_responsible($ProjectID)
{
    $resposible = opengoo_get_project_role($ProjectID, "исполнитель");
    return $resposible;
}

function show_incidents($SphereID, $ServiceID, $Fields)
{
    $SphereID = mes($SphereID);
    $ServiceID = mes($ServiceID);

    global $responsiblecolor;
    $sql = "SELECT IncidentID FROM сс_incidents_projects WHERE SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IsDeleted = '0'  ";
    $res = ssql($sql);
    if (is_array($res)) {
        foreach ($res as $k => $v) {
            $incidents .= "<b>" . opengoo_get_project_name($v["IncidentID"], false) . "</b>";
            $field_s = get_incident_fields(opengoo_get_projectdescription_by_projectID($v["IncidentID"]));
            if (get_responsible($v["IncidentID"])) {
                $incidents .= "<div style='background-color: $responsiblecolor'>Исполнитель: " . opengoo_get_display_name_of_userid(get_responsible($v["IncidentID"])) . "</div>";
            }
            if (is_array($field_s)) {
                foreach ($field_s as $k => $v) {
                    $incidents .= "<div style='padding-left: 10px;'><small><font color=red>[ " . get_additional_field($v, $Fields) . " ]</font></small></div>";
                }
            }
            $incidents .= "<hr/>";
        }
    } else {
        $incidents = "<b><font color='red'>нет данных</font></b>";
    }
    return $incidents;
}

function get_additional_fields()
{
    $arr = opengoo_list_subprojects(2568);
    foreach ($arr as $v) {
        $xml = opengoo_get_projectdescription_by_projectID($v["id"]);
        $xml_arr = xml2array($xml);
        $id = $xml_arr["xml"]["id"];
        $name = $xml_arr["xml"]["name"];
        $comment = $xml_arr["xml"]["comment"];
        $data[$id]["idname"] = opengoo_get_project_name($v["id"], false);
        $data[$id]["name"] = $name;
        $data[$id]["comment"] = $comment;
    }
    return $data;
}

function get_incident_fields($XML)
{
    $xml_arr = xml2array($XML);
    //  print_r($xml_arr[xml][fieldtypes]);
    if ($xml_arr[xml][fieldtypes]) {
        //    print_r($xml_arr);
        return $pieces = explode(",", $xml_arr["xml"]["fieldtypes"]);
    } else {
        return false;
        echo "not array\n";
    }
    //  echo "\n\n";
}


function get_additional_field($FieldID, $Data)
{
    return $Data[$FieldID]["name"];
}

function show_places($Places)
{
    foreach ($Places as $placek => $placev) {
        $placeid = $placev["id"];
        $html .= "\t<input
           type='radio'
           id='place-" . $placeid . "'
           name='place'
           onclick='show_spheres(" . $placeid . ");'/>
           <label
            for='place-" . $placeid . "'>" . $placev["name"] . "</label>\n";
    }
    return $html;
}
