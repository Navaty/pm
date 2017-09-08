<?php
include ("../db.inc.php");
include ("../functions.php");
echo '<pre>';

        $source = $_POST["source"];
        $service = $_POST["service"];
        $incidents = $_POST["incidents"];
	$incidents = explode('-', $incidents);
	$postincident = $incidents[2];
	//echo 'У нас такая последовательность';
        $query = "SELECT * FROM lena_incidents WHERE Active=1 ORDER BY Rank";
	$res = ssql($query);
        foreach($res as $item) {
//		echo '<pre>';
		$ressource = unserialize($item["SourceID"]);
		if($ressource == NULL) $flagsource = true; else $flagsource=false;
//		echo '<br />источник пуст?';
//		var_dump($flagsource);
		$resservice = unserialize($item["ServiceID"]);
		if($resservice == NULL) $flagservice = true; else $flagservice=false;
//                echo '<br />услуга пуста?';
//                var_dump($flagservice);

		$resincidents = unserialize($item["IncidentID"]);
                if($resincidents == NULL) $flagincidents = true; else $flagincidents=false;
//                echo '<br />инцидент пуст?';
//                var_dump($flagincidents);
//		echo '<br /><br />';
		$resexecutor = $item["ExecutorID"];
		$resreaders = unserialize($item["ReadersID"]);
		if(in_array($source, $ressource)||$flagsource) {
			if(in_array($service, $resservice)||$flagservice) {
				foreach($resincidents as $incident) {
					$masincident = explode('-', $incident);
					if($masincident[2]==$postincident) {
						$flagincidents = true;
						continue;
 		                        }
				}
				if($flagincidents) {
					        $executor = $resexecutor;
                                                $readers = $resreaders;
                                                $readers[count($readers)]=$executor;
                                                $returnresult["executor"]=$executor;
                                                $returnresult["readers"]=$readers;
                                                var_dump($returnresult);
						$read=$returnresult["readers"];
						foreach($read as $i) {
							$query="SELECT display_name as name FROM og_users WHERE id='$i'";
							$res = ssql($query);
							foreach($res as $j) {
								echo $j["name"].'<br />';
							}
						}
                                                return $returnresult;
				}
			}
		}
        }

?>
