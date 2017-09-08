<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
global $responsiblecolor;
$responsiblecolor = "lightyellow";

function get_spheres() {
  $arr = opengoo_list_subprojects(948); //Сферы
  return $arr;
}
function get_sphere_service($ProjectID) {
  $arr = opengoo_list_subprojects($ProjectID);
  return $arr;
}
function get_places() {
  $arr = opengoo_list_subprojects(2554);
  return $arr;
}
function is_place_checked($PlaceID,$SphereID,$ServiceID) {
  $sql = "SELECT ID FROM сс_incidents_projects_places WHERE PlaceID = '$PlaceID' AND SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IsDeleted = '0'";
  $res = ssql($sql);
  if($res[1]["ID"]>0) {
    return "checked";
  } else {
    return false;
  }
}
function get_responsible($ProjectID) {
  $resposible = opengoo_get_project_role($ProjectID,"исполнитель");
  return $resposible;
}

function show_incidents($SphereID,$ServiceID,$Fields,$Count=false) {
  global $responsiblecolor;
  $sql = "SELECT IncidentID FROM сс_incidents_projects WHERE SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IsDeleted = '0' ";
  $res = ssql($sql);
  if(is_array($res) && $Count==false) {
    foreach($res as $k=>$v) {
      $incidents .= "<b>".opengoo_get_project_name($v["IncidentID"],false) . "</b>";
      $field_s = get_incident_fields(opengoo_get_projectdescription_by_projectID($v["IncidentID"]));
      if(get_responsible($v["IncidentID"])) {
	$incidents .= "<div style='background-color: $responsiblecolor'>Исполнитель: ".opengoo_get_display_name_of_userid(get_responsible($v["IncidentID"]))."</div>";
      }
      if(is_array($field_s)) {
	foreach($field_s as $k=>$v) {
	  $incidents .= "<div style='padding-left: 10px;'><small><font color=red>[ ".get_additional_field($v,$Fields)." ]</font></small></div>";
	}
      }
      $incidents .= "<hr/>";
    }
  }
  elseif($Count) {
    $count = count($res);
    if($count==0) {
      return 1;
    }
    else {
      return $count;
    }
  }
  else {
    $incidents = "<b><font color='red'>нет данных</font></b>";
  }
  return $incidents;
}
function get_additional_fields() {
  $arr = opengoo_list_subprojects(2568);
  foreach($arr as $v) {
    $xml = opengoo_get_projectdescription_by_projectID($v["id"]);
    $xml_arr = xml2array($xml);
    $id = $xml_arr["xml"]["id"];
    $name = $xml_arr["xml"]["name"];
    $comment = $xml_arr["xml"]["comment"];
    $data[$id]["idname"] = opengoo_get_project_name($v["id"],false);
    $data[$id]["name"] = $name;
    $data[$id]["comment"] = $comment;
  }
  return $data;
}
function get_incident_fields($XML) {
  $xml_arr = xml2array($XML);
  //  print_r($xml_arr[xml][fieldtypes]);
  if($xml_arr[xml][fieldtypes]) {
    //    print_r($xml_arr);
    return $pieces = explode(",", $xml_arr["xml"]["fieldtypes"]);
  } else {
    return false;    echo "not array\n";
  }
  //  echo "\n\n";
}
function get_additional_field($FieldID,$Data) {
  return $Data[$FieldID]["name"];
}
//echo "<pre>";
$fields = get_additional_fields();
$spheres = get_spheres();
$places = get_places();

$token = $_REQUEST["token"];
if($token=="regina") {
  $js_path = "http://cc.citrt.net/oktell/js/";
}
?>
<html>
<head>
  <script type="text/javascript" src="<?=$js_path;?>/jquery.min.js"></script> 
  <script type="text/javascript" src="<?=$js_path;?>/jquery-ui.custom.min.js"></script> 
  <script type="text/javascript" src="<?=$js_path;?>/incidents_regina.js"></script> 
</head>
<body>
<table border=1 cellspacing=0 cellpadding='4'>
 <tr style='background-color: yellow'>
  <th rowspan="2">Сфера</th>
  <th rowspan="2">Услуга</th>
  <th rowspan="2">Классификатор инцидентов</th>
  <th colspan="<?=count($places);?>">Источники</th>
  </tr>
  <tr style='background-color: yellow'>
<?
  foreach($places as $k=>$v) {
  echo "<th>".$v["name"]."</th>";
}
?>
</tr>
<?
foreach($spheres as $spherek=>$spherev) {
  $sphereid = $spherev["id"];
  $spherename = $spherev["name"];
  $services = get_sphere_service($sphereid);
  //  if(count($services)==0) { $rowspan = 1;} else { $rowspan= count($services);}
  $row = "\t<td rowspan='$rowspan' width='10%' valign='top'><h3>".$spherename."</h3>";
  if(get_responsible($sphereid)) {
    $row .= "<div style='background-color: $responsiblecolor'>Исполнитель: ".opengoo_get_display_name_of_userid(get_responsible($sphereid))."</div>";
  }
  $row .= "</td>\n";
  for($i=1;$i<=$rowspan;$i++) {
    echo "<tr>\n";
    if($i==1) {
      echo $row;
    }
    $serviceid = $services[$i]["id"];
    $servicename = $services[$i]["name"];
    $inrows = show_incidents($sphereid,$serviceid,$fields,1);
    echo "\t<td valign='top' width='20%'><h4>".$servicename."</h4>";
    if(get_responsible($serviceid)) {
     echo "<div style='background-color: $responsiblecolor'>Исполнитель: ".opengoo_get_display_name_of_userid(get_responsible($serviceid))."</div>";
    }
    echo "</td>\n";
    echo "\t<td bztype='классификатор инцидентов' align='left' rowspan='".$inrows."'><div
            onclick='show_incidents(".$sphereid.",".$serviceid.")'/>".show_incidents($sphereid,$serviceid,$fields,1)."</div>
         <div
           id='idincidentform-main".$sphereid."-".$serviceid."'
           style='display:none;z-index:10;position:fixed;top:0px;left:50%;background-color: white;border: solid 2px red;text-align: left; padding:10px;font-size:12px'>
          <input
           type=button
           onclick=\"$('#idincidentform-main".$sphereid."-".$serviceid."').hide(); location.reload();\"
           value=\"Закрыть\"/>
          <div id='idincidentform".$sphereid."-".$serviceid."'>нет данных</div>
         </div>
</td>\n";
    foreach ($places as $placek=>$placev) {
      $placeid = $placev["id"];
      echo "\t<td align='center' valign='top'";
      if(is_place_checked($placeid,$sphereid,$serviceid)) {
	echo " style='background-color: lightgreen' ";
      }
      echo ">";
      echo "<input 
              type=\"checkbox\" ".is_place_checked($placeid,$sphereid,$serviceid)."
               onclick='check_place(".$placeid.",".$sphereid.",".$serviceid.")'/></td>\n";
    }
    echo "</tr>\n";
  }
}
?>
</tr>
</table>
</body>
</html>