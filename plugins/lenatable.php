<?php
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
?>
<table id="table_records">
        <tbody>
                <tr>
                        <td class="names_headersmall">
                                <p>Порядок</p>
                        </td>
                        <td class="names_header">
                                <p>Источник</p>
                        </td>
                        <td class="names_header">
                                <p>Сфера + Услуга</p>
                        </td>
                        <td class="names_header">
                                <p>Индидент</p>
                        </td>
                        <td class="names_header">
                                <p>Исполнитель</p>
                        </td>
                        <td class="names_header">
                                <p>Подписчики</p>
                        </td>
                        <td class="names_header">
                                <p>Комментарий</p>
                        </td>

                </tr>
                <?php
                        $query = "SELECT * FROM lena_incidents ORDER BY rank";
                        $res = ssql($query);
                        foreach($res as $res_item) {
                                $comment = $res_item["Comment"];
                                $sources = unserialize($res_item["SourceID"]);
                                $services = unserialize($res_item["ServiceID"]);
                                $incidents = unserialize($res_item["IncidentID"]);
                                $readers = unserialize($res_item["ReadersID"]);
                                $executor = $res_item["ExecutorID"];
                                $active = $res_item["Active"];
                                $rank = $res_item["Rank"];
                                $id= $res_item["ID"];
                                if($active==1)
                                        $tdclass="active";
                                else
                                        $tdclass="none_active";
                                echo '<tr>';
                                echo '<td class="'.$tdclass.'">';
                                echo $rank;
                                echo '</td>';
                                echo '<td class="'.$tdclass.'">';
                                $counter=1;
                                if($sources==NULL) echo 'Все источники';
                                else {
                                        foreach($sources as $source) {
                                                $query="SELECT name as source FROM og_projects WHERE id = '$source'";
                                                $res_source = ssql($query);
                                                foreach($res_source as $item_source) {
                                                        echo $counter.'. '.$item_source["source"].'<br /><hr />';
                                                }
                                                $counter++;
                                        }
                                }
                                echo '</td>';

                                echo '<td class="'.$tdclass.'">';
                                $counter=1;

                                if($services==NULL) echo 'Все услуги';
                                else {
                                        foreach($services as $service) {
                                                $query="SELECT name as service, p6 as sphere_id FROM og_projects WHERE id='$service'";
                                                $res = ssql($query);
                                                foreach($res as $res_item) {
                                                        $name = $res_item["service"];
                                                        $sphere_id = $res_item["sphere_id"];
                                                        $query="SELECT name as sphere FROM og_projects WHERE id='$sphere_id'";
                                                        $res_sphere = ssql($query);
                                                        foreach($res_sphere as $spere_item) {
                                                                $sphere = $sphere_item["sphere"];
                                                        }
                                                        echo $counter.'. '.$sphere.' '.$name.'<br /><hr />';
                                                }
                                                $counter++;
                                        }
                                }
                                echo '</td>';

                                echo '<td class="'.$tdclass.'">';
                                $counter=1;
                                if($incidents==NULL) echo 'Все инциденты';
                                else {
                                        foreach($incidents as $incident) {
                                                $sper_serv_inc = explode('-', $incident);
                                                $query = "SELECT name as incident FROM og_projects WHERE id='$sper_serv_inc[2]'";
                                                $query_incident = ssql($query);
                                                $query = "SELECT name as service FROM og_projects WHERE id='$sper_serv_inc[1]'";
                                                $query_service = ssql($query);
                                                $query = "SELECT name as sphere FROM og_projects WHERE id='$sper_serv_inc[0]'";

                                                $query_sphere = ssql($query);
                                                $query_sphere = $query_sphere[1];
                                                $query_service = $query_service[1];
                                                $query_incident = $query_incident[1];
                                                $res["sphere"] = $query_sphere["sphere"];
                                                $res["service"] = $query_service["service"];
                                                $res["incident"] = $query_incident["incident"];
                                                echo $counter.". ".$res["sphere"]." - ". $res["service"]." - ".$res["incident"]."<br /><hr />";
                                                $counter++;
                                        }
                                }
                                echo '</td>';

                                echo '<td class="'.$tdclass.'">';
                                $query = "SELECT ou.display_name as name, oc.name as company FROM og_users as ou, og_companies as oc WHERE ou.id='$executor' AND oc.id = ou.company_id";
                                $res = ssql($query);
                                foreach($res as $item) {
                                        echo $item["company"].' - '.$item["name"];
                                }
                                echo '</td>';

                                echo '<td class="'.$tdclass.'">';
                                foreach($readers as $reader) {
                                        $query = "SELECT ou.display_name as name, oc.name as company FROM og_users as ou, og_companies as oc WHERE ou.id='$reader' AND oc.id = ou.company_id";
                                        $res = ssql($query);
                                        foreach($res as $item) {
                                                echo $item["company"].' - '.$item["name"].'<br />';
                                        }

                                }
                                echo '</td>';
                                echo '<td class="'.$tdclass.'">';
                                echo $comment;
                                echo '</td>';
                                echo '<td>';
                                echo '<button class="edit" value="'.$id.'">Редактировать</button>';
                                echo '</td>';
                                echo '</tr>';
                        }
                ?>
        </tbody>
</table>

