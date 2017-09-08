<?php
class fos extends apiBaseClass {

	function faq($apiMethodParams) {
	$retJSON = $this->createDefaultJson();
//			include_once APIConstants::$AllIncludes;
			$id = ( isset($apiMethodParams->id) )? $apiMethodParams->id : 2026;
			$id = ($id) ? $id : 2026;
			$fetch_themes = ( isset($apiMethodParams->fetch_themes) ) ? $apiMethodParams->fetch_themes : false;
			$lang = ( isset($apiMethodParams->lang) )? $apiMethodParams->lang : "ru";
			$retJSON = get_notes($id,$lang,true, $fetch_themes);
	return $retJSON;
	}

	function form($apiMethodParams) {
	$retJSON = $this->createDefaultJson();
//			include_once APIConstants::$AllIncludes;

			$id = ( isset($apiMethodParams->id) )? $apiMethodParams->id : 2026;
			$id = ($id) ? $id : 2026;
			$lang = ( isset($apiMethodParams->lang) )? $apiMethodParams->lang : "ru";

			$data=FOS($id,$lang);
			if($id==2026){
			        $data['fields']=getFields(2026,4);
			        $data['fields']['action']="http://pm.citrt.net/plugins/webservices/service.php";
			}
			$retJSON = $data;

	return $retJSON;
	}

	function form2($apiMethodParams) {
        $retJSON = $this->createDefaultJson();
//                      include_once APIConstants::$AllIncludes;

                        $id = ( isset($apiMethodParams->id) )? $apiMethodParams->id : 2026;
                        $id = ($id) ? $id : 2026;
                        $lang = ( isset($apiMethodParams->lang) )? $apiMethodParams->lang : "ru";

                        $data=FOS2($id,$lang);
                        if($id==2026){
                                $data['fields']=getFields(2026,4);
                                $data['fields']['action']="http://pm.citrt.net/feng-services/v1/";
                        }
                        $retJSON = $data;
        return $retJSON;
        }

	function themes($apiMethodParams) {
	$retJSON = $this->createDefaultJson();
//			include_once APIConstants::$AllIncludes;

			$id = ( isset($apiMethodParams->id) )? $apiMethodParams->id : 3791;
			$id = ($id) ? $id : 3791;
			$lang = ( isset($apiMethodParams->lang) )? $apiMethodParams->lang : "ru";

			$data=FOS($id,$lang);
			if($id==3791){
			        $data['fields']=getFields(3791,4);
			        $data['fields']['action']="http://pm.citrt.net/plugins/webservices/task.php";
			}
			$retJSON = $data;
	return $retJSON;
	}

}
?>
