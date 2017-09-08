<?php

include ("../../db.inc.php");
include ("../../functions.php");
header("Content-Type: text/html; charset=utf-8");
$post = $_REQUEST;
$command = $post["command"];
//echo '<pre>';
switch($command){
	case "give_sources":
		$query = "SELECT * FROM report_incidents_sources WHERE active = 1";
		$res = ssql($query);
	break;
	case "change_classifier":
		$source_id = htmlspecialchars($post["source_id"]);
		$query = "SELECT * FROM report_incidents_classifier WHERE source_id = ".$source_id." AND active = 1";
		$res = ssql($query);
	break;
	case "change_source_list":
                $query = "SELECT * FROM report_incidents_sources WHERE active = 1";
                $res = ssql($query);
        break;
	case "write_to_base":
		$source = htmlspecialchars($post["source"]);
		$classifier = htmlspecialchars($post["classifier"]);
		$active = htmlspecialchars($post["active"]);
		$query = "SELECT id FROM report_incidents_criteria WHERE source=".$source." AND classifier=".$classifier;
		$res = ssql($query);
		if(is_array($res)) {
			$res = "record_exist";
		}
		else {
			$query = "INSERT INTO report_incidents_criteria (source, classifier, active) VALUES (".$source.", ".$classifier.", ".$active.")";
			$res = usql($query);
		}
	break;
	case "write_to_base_source":
		$source = htmlspecialchars($post["source"]);
		$active = htmlspecialchars($post["active"]);
		$query = "SELECT id FROM report_incidents_sources WHERE name=".$source;
		$res = ssql($query);
                if(is_array($res)) {
                        $res = "record_exist";
                }
                else {
                        $query = "INSERT INTO report_incidents_sources (name, active) VALUES ('".$source."', ".$active.")";
                        $res = usql($query);
                }
	break;
	case "write_to_base_classifier":
		$classifier = htmlspecialchars($post["classifier"]);
                $source = htmlspecialchars($post["source"]);
                $active = htmlspecialchars($post["active"]);
                $query = "SELECT id FROM report_incidents_classifier WHERE name=".$classifier." AND source_id=".$source;
                $res = ssql($query);
                if(is_array($res)) {
                        $res = "record_exist";
                }
                else {
                        $query = "INSERT INTO report_incidents_classifier (name, source_id, active) VALUES ('".$classifier."', ".$source.", ".$active.")";
                        $res = usql($query);
                }
        break;
	case "show_all_criteria":
		$query = "SELECT ric.id as id, ris.name as sourcename, ricl.name as classifiername, ric.active FROM report_incidents_criteria as ric, report_incidents_sources as ris, report_incidents_classifier as ricl WHERE ris.id = ric.source AND ricl.id = ric.classifier ORDER BY active DESC, source ASC";
		$res = ssql($query);
	break;
	case "show_all_sources":
                $query = "SELECT id, name, active FROM report_incidents_sources ORDER BY active DESC";
                $res = ssql($query);
        break;
	case "show_all_classifiers":
                $query = "SELECT ric.id as id, ric.name as name, ric.active as active, ris.name as source FROM report_incidents_sources as ris, report_incidents_classifier as ric WHERE ris.id = ric.source_id ORDER BY  active DESC, name ASC";
                $res = ssql($query);
        break;
	case "delete_sourfier":
		$id = $post["record_id"];
		$query = "DELETE FROM report_incidents_criteria WHERE id=".$id;
		$res = usql($query);
	break;
        case "delete_source":
                $id = $post["record_id"];
                $query = "DELETE FROM report_incidents_sources WHERE id=".$id;
                $res = usql($query);
        break;
        case "delete_classifier":
                $id = $post["record_id"];
                $query = "DELETE FROM report_incidents_classifier WHERE id=".$id;
                $res = usql($query);
        break;
	case "active_sourfier":
		$id = $post["record_id"];
		$active = $post["active"];
		$flag = true;
		switch($active){
			case "0":
				$query = "UPDATE report_incidents_criteria SET active=1 WHERE id=".$id;
			break;
			case "1":
				$query = "UPDATE report_incidents_criteria SET active=0 WHERE id=".$id;
			break;
			default:
				$flag = false;
			break;
		}
		if($flag) {
			$res = usql($query);
		}
		else {
			$res=$flag;
		}
	break;
        case "active_source":
                $id = $post["record_id"];
                $active = $post["active"];
                $flag = true;
                switch($active){
                        case "0":
                                $query = "UPDATE report_incidents_sources SET active=1 WHERE id=".$id;
                        break;
                        case "1":
                                $query = "UPDATE report_incidents_sources SET active=0 WHERE id=".$id;
                        break;
                        default:
                                $flag = false;
                        break;
                }
                if($flag) {
                        $res = usql($query);
                }
                else {
                        $res=$flag;
                }
        break;
        case "active_classifier":
                $id = $post["record_id"];
                $active = $post["active"];
                $flag = true;
                switch($active){
                        case "0":
                                $query = "UPDATE report_incidents_classifier SET active=1 WHERE id=".$id;
                        break;
                        case "1":
                                $query = "UPDATE report_incidents_classifier SET active=0 WHERE id=".$id;
                        break;
                        default:
                                $flag = false;
                        break;
                }
                if($flag) {
                        $res = usql($query);
                }
                else {
                        $res=$flag;
                }
        break;
	case "edit_sourfier":
                $id = $post["record_id"];
                $query = "SELECT * FROM report_incidents_criteria WHERE id = ".$id;
                $res = ssql($query);
		$verstka = '';
		if(is_array($res)) {
			foreach($res as $item) {
				$query1 = "SELECT * FROM report_incidents_sources";
				$res1 = ssql($query1);
				$query2 = "SELECT * FROM report_incidents_classifier WHERE source_id=".$item["source"];
				$res2 = ssql($query2);
				if((is_array($res1))&&(is_array($res2))) {
					foreach($res1 as $item1) {
						if($item1["id"] == $item["source"]) {
							$verstka .= '<option value="'.$item1["id"].'" selected>'.$item1["name"].'</option>';
						}
						else {
							$verstka .= '<option value="'.$item1["id"].'">'.$item1["name"].'</option>';
						}
					}
					$res[] = $verstka;
					$verstka = '';
					foreach($res2 as $item2) {
                                                if($item2["id"] == $item["classifier"]) {
                                                        $verstka .= '<option value="'.$item2["id"].'" selected>'.$item2["name"].'</option>';
                                                }
                                                else {
                                                        $verstka .= '<option value="'.$item2["id"].'">'.$item2["name"].'</option>';
                                                }
                                        }
					$res[] = $verstka;
					$verstka = '<label for="newsource_active">Активность</label>';
					if($item["active"]==1) {
						$verstka .= '<input type="checkbox" checked name="edit_active" id="edit_active" class="ui-widget-content ui-corner-all">';
					}
					else {
						$verstka .= '<input type="checkbox" name="edit_active" id="edit_active" class="ui-widget-content ui-corner-all">';
					}
					$res[] = $verstka;
				}
			}
		}
	break;
	default:
		$res = Array();
	break;
}
$send = json_encode($res);
echo $send;
?>
