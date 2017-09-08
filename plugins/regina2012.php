<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$placeid = $_REQUEST["placeid"];
$sphereid = $_REQUEST["sphereid"];
$serviceid = $_REQUEST["serviceid"];
$control_place = $_REQUEST["control_place"];
$incidentid = $_REQUEST["incidentid"];
$show_incidents = $_REQUEST["show_incidents"];
$update_incident = $_REQUEST["update_incident"];

function control_place($PlaceID,$SphereID,$ServiceID,$IncidentID) {
  $sql = "SELECT ID FROM сс_incidents_projects_places WHERE PlaceID = '$PlaceID' AND SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IncidentID = '$IncidentID' AND IsDeleted = '0'";
  $res = ssql($sql);
  $ID = $res[1]["ID"];
  if($ID > 0) {
    $sql = "UPDATE сс_incidents_projects_places SET IsDeleted = '1', DeleteDate = NOW() WHERE PlaceID = '$PlaceID' AND SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IncidentID = '$IncidentID'  AND IsDeleted = '0'";
    $res = usql($sql);
    return "unchecked";
  } else {
    $sql = "INSERT INTO сс_incidents_projects_places (`PlaceID`,`SphereID`,`ServiceID`,`IncidentID`) VALUE ('$PlaceID','$SphereID','$ServiceID','$IncidentID')";
    $res = usql($sql);
    return "checked";
  }
}
function show_incidents_form($SphereID,$ServiceID) {
  $sql = "SELECT * FROM сс_incidents_projects WHERE SphereID = 'SphereID' AND ServiceID = 'ServiceID' AND IsDeleted = '0' ";
  $res = ssql($sql);
  if($res[1]) {
    return print_r($res,1);
  } else {
    return "нет данных";
  }
}
function is_incident_checked($SphereID,$ServiceID,$IncidentID) {
  $sql = "SELECT ID FROM сс_incidents_projects WHERE SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IncidentID = '$IncidentID' AND IsDeleted = '0' ";
  $res = ssql($sql);
  if($res[1]["ID"]) {
    return "checked";
  } else {
    return " ";
  }
}

function show_incidents($sphereid,$serviceid) {
  $incidents = opengoo_list_subprojects(2565);
  foreach($incidents as $k=>$v) {
    $rand = rand()."-".$k."-".$v["id"];
    $incidents_form .= "
       <input
        type='checkbox'
        id='$rand'
        onclick='update_incident(".$sphereid.",".$serviceid.",".$v["id"].");'
        ".is_incident_checked($sphereid,$serviceid,$v["id"])."
        />
        <label for='$rand'>".$v["name"]."</label>
        <br/>";
  }
  return  $incidents_form;
}

function update_incident($SphereID,$ServiceID,$IncidentID) {
echo  $sql = "SELECT ID FROM сс_incidents_projects WHERE SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IncidentID = '$IncidentID' AND IsDeleted = '0' ";
  $res = ssql($sql);
  if($res[1]["ID"]) {
    $sql = "UPDATE сс_incidents_projects SET IsDeleted = '1', DeleteDate = NOW() WHERE SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IncidentID = '$IncidentID' AND IsDeleted = '0'";
    $res = usql($sql);
    $sql = "UPDATE сс_incidents_projects_places SET IsDeleted = '1', DeleteDate = NOW() WHERE SphereID = '$SphereID' AND ServiceID = '$ServiceID' AND IncidentID = '$IncidentID' AND IsDeleted = '0'";
    //    $res = usql($sql);
    logger($sql,"error");
    return $sql;
    //    return show_incidents();
    //    return print_r($res,1);
  } else {
    $sql = "INSERT INTO  сс_incidents_projects (`SphereID`,`ServiceID`,`IncidentID`) VALUES ('$SphereID','$ServiceID','$IncidentID')";
    $res = usql($sql);
    return "обновил";
  }
}


if($control_place) {
  echo control_place($placeid,$sphereid,$serviceid,$incidentid);
}
if($show_incidents_form) {
  echo show_incidents($sphereid,$serviceid);
}
if($update_incident) {
  echo update_incident($sphereid,$serviceid,$incidentid);
}
if($show_incidents) {
  echo show_incidents($sphereid,$serviceid);
}
?>