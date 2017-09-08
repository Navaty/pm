<?
include_once "statusage.php"; //by almaz - usage control
include("db.inc.php");
include("functions.php");
global $responsiblecolor;
$responsiblecolor = "lightyellow";

$token = $_REQUEST["token"];
if ($token == "lil2016new") {
    global $logined;
    $logined = 1;
}

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

function is_place_checked($PlaceID, $SphereID, $ServiceID, $IncidentID)
{
    $sql = "SELECT ID FROM сс_incidents_projects_places WHERE PlaceID = '$PlaceID' AND SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IncidentID = '$IncidentID' AND IsDeleted = '0'";
    $res = ssql($sql);
    if ($res[1]["ID"] > 0) {
        return "checked";
    } else {
        return false;
    }
}

function get_subscribers($ProjectID)
{
    $resposible = opengoo_get_project_role($ProjectID, "подписчик", false, true);
    return $resposible;
}

function show_subscribers($Data)
{
    if (is_array($Data)) {
        foreach ($Data as $v) {
            $res .= "<div style='padding-left: 25px; border: dotted 1px grey;'>" . opengoo_get_display_name_of_userid($v) . "</div>";
        }
    }
    return $res;
}

function get_responsible($ProjectID)
{
    $resposible = opengoo_get_project_role($ProjectID, "исполнитель");
    return $resposible;
}

function show_incidents($SphereID, $ServiceID, $Fields, $Count = false)
{
    global $responsiblecolor;
    $sql = "
SELECT cip.IncidentID AS IncidentID, op.Name
FROM сс_incidents_projects AS cip,
     og_projects AS op
WHERE  cip.IncidentID = op.id
AND cip.SphereID = '$SphereID' 
AND cip.ServiceID = '$ServiceID' 
AND cip.IsDeleted = '0'
 ";
    //AND IncidentID = '$IncidentID'
    $res = ssql($sql);
    if (is_array($res) && $Count == false) {
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
    } elseif ($Count == 1) {
        $count = count($res);
        if ($count == 0) {
            return 1;
        } else {
            return $count;
        }
    } elseif ($Count == 'array') {
        return $res;
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

function make_place($Places, $SphereID, $ServiceID, $IncidentID)
{
    global $logined;
    foreach ($Places as $placek => $placev) {
        $placeid = $placev["id"];
        $html .= "\t<td align='center' valign='top'";
        if (is_place_checked($placeid, $SphereID, $ServiceID, $IncidentID)) {
            $html .= " style='background-color: lightgreen' ";
        }
        $html .= ">";
        if ($logined == 1) {
            $html .= "<input type=\"checkbox\" " . is_place_checked($placeid, $SphereID, $ServiceID, $IncidentID);
            $html .= " onclick='check_place(" . $placeid . "," . $SphereID . "," . $ServiceID . "," . $IncidentID . ")' />";
        }
        $html .= "</td>\n";
        //$html .= "<input type='checkbox'/></td>\n";
    }
    return $html;
}

//echo "<pre>";
$fields = get_additional_fields();
$spheres = get_spheres();
$places = get_places();

$token = $_REQUEST["token"];
if ($token == "lil2016new") {
    $js_path = "http://cc.citrt.net/oktell/js/";
}
?>
<html>
<head>
    <script type="text/javascript" src="<?= $js_path; ?>/jquery.min.js"></script>
    <script type="text/javascript" src="<?= $js_path; ?>/jquery-ui.custom.min.js"></script>
    <script type="text/javascript" src="<?= $js_path; ?>/incidents_regina.js"></script>
</head>
<body>
<table border=1 cellspacing=0 cellpadding='4'>
    <thead>
    <tr style='background-color: yellow'>
        <th rowspan="2">Сфера</th>
        <th rowspan="2">Услуга</th>
        <th rowspan="2">Классификатор инцидентов</th>
        <th colspan="<?= count($places); ?>">Источники</th>
    </tr>
    <tr style='background-color: yellow'>
        <?php
        foreach ($places as $k => $v) {
            echo "\t<th>";
            echo $v["name"];
            if (get_subscribers($v["id"])) {
                echo "<div style='background-color: $responsiblecolor'>Подписчики: " . show_subscribers(get_subscribers($v[id])) . "</div>";
            }
            echo "</th>\n";
        }
        ?>
    </tr>
    </thead>
    <tbody style="height: 550px; overflow: auto;">
    <?php
    //СФЕРА
    foreach ($spheres as $spherev) {
        $sphereid = $spherev["id"];
        $spherename = $spherev["name"];
        $services = get_sphere_service($sphereid);
        $count_services = count($services);
        if ($count_services == 0) {
            $services[1]['name'] = 'НЕТ ДАННЫХ!';
            $services[1]['id'] = 0;
            $count_services = 1;
        }
        //УСЛУГИ
        $row_services = "";
        foreach ($services as $service) {
            $id_service++;
            $serviceid = $service["id"];
            $servicename = $service["name"];
            $incidents = show_incidents($sphereid, $serviceid, $fields, 'array');
            $count_incidents = count($incidents);
            if ($count_incidents == 0) {
                $incidents[1]['name'] = "НЕТ ДАННЫХ";
                $incidents[1]['id'] = 0;
                $count_incidents = 1;
            }
            //Инциденты
            $row_incidents = "";
            $id_incident = 0;
            foreach ($incidents as $incidentv) {
                $id_incident++;
                $incidentid = $incidentv["IncidentID"];
                $incidentname = opengoo_get_project_name($incidentid, false);
                if ($id_incident > 1) {
                    $row_incidents .= "<tr id_incident_more_1>\n";
                }

                $row_incidents .= "\t<td valign='top' bztype='Инцидент'>" . $incidentname;
                if (get_responsible($incidentid)) {
                    $row_incidents .= "<div style='background-color: $responsiblecolor'>Исполнитель: " . opengoo_get_display_name_of_userid(get_responsible($incidentid)) . "</div>";
                }
                if (get_subscribers($incidentid)) {
                    $row_incidents .= "<div style='background-color: $responsiblecolor'>Подписчики: " . show_subscribers(get_subscribers($incidentid)) . "</div>";
                }
                $row_incidents .= "&nbsp;<span onclick='show_incidents(" . $sphereid . "," . $serviceid . ")' title='Редактирование классификатора' style='background-color: CCC; width:30px/'>[+/-]</div>
         <div id='idincidentform-main" . $sphereid . "-" . $serviceid . "'
           style='display:none;z-index:10;position:fixed;top:0px;left:50%; height:500px; overflow: auto; background-color: white;border: solid 2px red;text-align: left; padding:10px;font-size:12px'>
          <input type=button onclick=\"$('#idincidentform-main" . $sphereid . "-" . $serviceid . "').hide(); location.reload();\"  value=\"Закрыть\"/>
          <div id='idincidentform" . $sphereid . "-" . $serviceid . "' >нет данных</div>
</div>
";
                $row_incidents .= "</td>\n";
                $row_incidents .= make_place($places, $sphereid, $serviceid, $incidentid);
                //	    $row_incidents .= "</tr incidents_end>\n";
            }
            //Инциденты конец
            $count_rows[$serviceid] = $count_incidents;
            $count_rows[$sphereid] = $count_rows[$sphereid] + $count_incidents;
            $row_services .= "\t<td valign='top' rowspan='" . $count_rows[$serviceid] . "' bztype='Услуга'>" . $servicename;
            if (get_responsible($serviceid)) {
                $row_services .= "<div style='background-color: $responsiblecolor'>Исполнитель: " . opengoo_get_display_name_of_userid(get_responsible($serviceid)) . "</div>";
            }
            if (get_subscribers($serviceid)) {
                $row_services .= "<div style='background-color: $responsiblecolor'>Подписчики: " . show_subscribers(get_subscribers($serviceid)) . "</div>";
            }
            $row_services .= "</td>\n";
            $row_services .= $row_incidents;
            $row_services .= "</tr services_end>\n";
        }
        //Услуги конец
        $row_spheres .= "<tr sphera>\n\t";
        $row_spheres .= "<td valign='top' rowspan='" . $count_rows[$sphereid] . "' bztype='Сфера'>" . $spherename;
        if (get_responsible($sphereid)) {
            $row_spheres .= "<div style='background-color: $responsiblecolor'>Исполнитель: " . opengoo_get_display_name_of_userid(get_responsible($sphereid)) . "</div>";
        }
        if (get_subscribers($sphereid)) {
            $row_spheres .= "<div style='background-color: $responsiblecolor'>Подписчики: " . show_subscribers(get_subscribers($sphereid)) . "</div>";
        }
        $row_spheres .= "</td>\n";
        //	if($count_rows[$sphereid]>1) {
        $row_spheres .= $row_services;
        //} else {
        //	  $row_spheres .= "</tr>\n";
        //}

    }
    //Сфера конец
    echo $row_spheres;
    echo "<tr>\n";
    /*
    $in_1_rows = show_incidents($sphereid,$serviceid,$fields,1);


          echo
          $row = "\t<td rowspan='$rowspan' width='10%' valign='top'><h3>".$spherename."</h3>";
          if(get_responsible($sphereid)) {
            $row .= "<div style='background-color: $responsiblecolor'>Исполнитель: ".opengoo_get_display_name_of_userid(get_responsible($sphereid))."</div>";
          }
          $row .= "</td>\n";
          echo "\t<td valign='top' width='20%'><h4>".$servicename."</h4>";
          if(get_responsible($serviceid)) {
            echo "<div style='background-color: $responsiblecolor'>Исполнитель: ".opengoo_get_display_name_of_userid(get_responsible($serviceid))."</div>";
          }
          echo "</td>\n";
          echo "\t<td bztype='классификатор инцидентов' align='left' rowspan='".$inrows."'><div
                onclick='show_incidents(".$sphereid.",".$serviceid.")'/>".show_incidents($sphereid,$serviceid,$fields,1)."</div>
             </div>
    </td>\n";
        echo "</tr>\n";
      }
    }
    */
    ?></tbody>
    </tr>
</table>
</body>
</html>
