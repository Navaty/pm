<?php
include ("../db.inc.php");
include ("../functions.php");

function get_responsible($ProjectID) {
  $resposible = opengoo_get_project_role($ProjectID,"исполнитель");
  return $resposible;
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

function get_spheres() {
  $arr = opengoo_list_subprojects(948); //Сферы
  return $arr;
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
function get_places() {
  $arr = opengoo_list_subprojects(2554);
  return $arr;
}

function get_sphere_service($ProjectID) {
  $arr = opengoo_list_subprojects($ProjectID);
  return $arr;
}

$fields = get_additional_fields();
$spheres = get_spheres();
$places = get_places();
$id = $_REQUEST["id"];
$query = "SELECT * FROM lena_incidents WHERE id='$id'";
$edit_res = ssql($query);
foreach($edit_res as $edit_res_item) {
$editcomment=$edit_res_item["Comment"];
$editrank=$edit_res_item["Rank"];
$editid=$edit_res_item["ID"];
$editservices=unserialize($edit_res_item["ServiceID"]);
$editsources=unserialize($edit_res_item["SourceID"]);
$editincidents=unserialize($edit_res_item["IncidentID"]);
$editreaders=unserialize($edit_res_item["ReadersID"]);
$editexecutor=$edit_res_item["ExecutorID"];
$editactive=$edit_res_item["Active"];
$editid=$edit_res_item["ID"];
?>
<form action='lenaphp/lena_editincidents.php' id="editLenaform">
<input type="hidden" name="editid" value="<?php echo $editid; ?>">
<table style="vertical-align: top;">
<tr>
<td style="vertical-align: top;">
<label>Порядок<sup style="color: red;">*</sup></label><br />
<input type="text" name="editrank" value="<?php echo $edit_res_item["Rank"]; ?>" />
</td>
<td>
<label>Источник</label><br />
<select size="10" name="editsource[]" multiple style="width: 300px;">
<?php
foreach($places as $item) {
        foreach($editsources as $edititem) {
	        if($edititem==$item["id"]) {
        	        $selected = "selected";
                        break;
                }
           	else $selected="";
      	}

	echo "<option value='".$item["id"]."'".$selected.">".$item["name"]."</option>\n";
}
?>
</select>
</td><td>
<label>Сфера + Услуга</label><br />
<select multiple size="10" name="editservice[]" style="width: 300px;">
<?php
	foreach($spheres as $spherev) {
        	$sphereid = $spherev["id"];
	        $spherename = $spherev["name"];
		echo "<optgroup label='&nbsp;&nbsp;".$spherename."'></optgroup>\n";
	        $services = get_sphere_service($sphereid);
        	$count_services = count($services);
		if($count_services==0) {
			$services[1]['name'] = 'НЕТ ДАННЫХ!';
			$services[1]['id'] = 0;
			$count_services = 1;
		}
		foreach($services as $service) {
			$serviceid = $service["id"];
			$servicename = $service["name"];
			foreach($editservices as $item) {
				if($item==$serviceid) {
					$selected = "selected";
					break;
				}
				else $selected="";
			}
			echo "<option value='".$serviceid."' ".$selected.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$servicename."</option>\n";
		}
	}
?>
</select>
</td><td>
<label>Инциденты</label><br />
<select multiple size="10" name="editincidents[]" style="width: 300px;">
<?php
echo '<pre>';
//var_dump($spheres);
	foreach($spheres as $spherev) {
                $sphereid = $spherev["id"];
                $spherename = $spherev["name"];
		echo "<optgroup label='&nbsp;&nbsp;".$spherename."'></optgroup>\n";
                $services = get_sphere_service($sphereid);
                foreach($services as $service) {
			$serviceid = $service["id"];
                        $servicename = $service["name"];
			echo "<optgroup label='&nbsp;&nbsp;&nbsp;&nbsp;".$servicename."'></optgroup>\n";
			$sql = "SELECT cip.IncidentID AS IncidentID, op.Name FROM сс_incidents_projects AS cip, og_projects AS op WHERE  cip.IncidentID = op.id AND cip.SphereID = '$sphereid'	AND cip.ServiceID = '$serviceid' AND cip.IsDeleted = '0'";
			$res = ssql($sql);
			foreach($res as $res_item) {
				$incidentname = $res_item["Name"];
				$incidentsid = $res_item["IncidentID"];
	                        foreach($editincidents as $editincitem) {
        	                        if($editincitem==$sphereid."-".$serviceid."-".$incidentsid) {
                	                        $selected = "selected";
                        	                break;
                                	}
	                                else $selected="";
        	                }

				echo "<option value='".$sphereid."-".$serviceid."-".$incidentsid."' ".$selected.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$incidentname."</option>\n";
			}
		}
        }
?>
</select>
</td><td style="vertical-align: top;">
<label>Исполнитель<sup style="color: red;">*</sup></label><br />
<select size="1" name="editexecutor" id="editexecutor" style="width: 200px;">
<?php
	$sql = "SELECT id, name FROM og_companies ORDER BY name";
	$companies = ssql($sql);
	foreach($companies as $company) {
		$companyid = $company["id"];
		$companyname = $company["name"];
		$sql = "SELECT id, display_name as name FROM og_users WHERE company_id='$companyid' ORDER BY display_name";
		$persons = ssql($sql);
		if(count($persons)>0) {
                	echo "<optgroup label='&nbsp;".$companyname."'></optgroup>\n";
			foreach($persons as $person) {
				$personid = $person["id"];
				$personname = $person["name"];
				if($personid == $editexecutor)  $selected="selected";
				else $selected="";
				echo "<option value='".$personid."' ".$selected.">&nbsp;&nbsp;&nbsp;&nbsp;".$personname."</option>\n";
			}
		}
	}
?>
</select>
</td><td>
<label>Подписчики</label><br />
<select multiple size="10" name="editreaders[]" id="editreaders" style="width: 300px;">
<?php
        $sql = "SELECT id, name FROM og_companies  ORDER BY name";
        $companies = ssql($sql);
        foreach($companies as $company) {
                $companyid = $company["id"];
                $companyname = $company["name"];
                $sql = "SELECT id, display_name as name FROM og_users WHERE company_id='$companyid' ORDER BY display_name";
                $persons = ssql($sql);
                if(count($persons)>0) {
                        echo "<optgroup label='&nbsp;".$companyname."'></optgroup>\n";
                        foreach($persons as $person) {
                                $personid = $person["id"];
                                $personname = $person["name"];
                                foreach($editreaders as $editreadersitem) {
                                        if($editreadersitem==$personid) {
                                                $selected = "selected";
                                                break;
                                        }
                                        else $selected="";
                                }

                                echo "<option value='".$personid."'".$selected.">&nbsp;&nbsp;&nbsp;&nbsp;".$personname."</option>\n";
                        }
                }
        }
?>
</select><br />
</td>
<td style="vertical-align: top;">
<label>Комментарий<sup style="color: red;">*</sup></label><br />
<textarea name="editcomment" cols="20" rows="10"><?php echo $edit_res_item["Comment"]; ?></textarea>
</td>
<td>
<input type="checkbox" name="editactive" value="1" <?php if($editactive=="1") echo 'checked';?>>Активна
</td>
</tr>
</table>
</form>
<button id="editForm">Редактировать</button>
<button id="closeForm">Закрыть</button>
<?php 
	}
?>
