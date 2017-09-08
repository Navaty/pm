<?php
class fengtask extends apiBaseClass {


	function createTestTask($apiMethodParams) {
		$retJSON=$this->createDefaultJson();
		$CacheName = "fengtask.createTestTask".md5(serialize($apiMethodParams));
		$cache = file_get_contents2($CacheName);

	if ($cache != false) {
			return unserialize($cache);
	} else {

		if($apiMethodParams->Хочет_получить_ответ == "Нет") {
                        $autoclose = 1;
                }

                unset($apiMethodParams->Хочет_получить_ответ);


		if(isset($apiMethodParams->appeal)) {
//			include_once APIConstants::$AllIncludes;
 			  $ProjectId     = '5361';
			  $Title         = 'Тестовая Задача';
			  $Assigned2iD   = 469;
			  $AssignedById  = 106;
			  $Data = $apiMethodParams;
			  $Appeal = $Data->appeal;
			  unset($Data->appeal);
			  if(!$autoclose)		  $Subscrip     = array("user_id"=>array(469));
			  $taskid = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,(array)$Data,$Subscrip);
			  if(is_numeric($taskid)) $retJSON->true = $taskid;
			  else $retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);

			if(is_numeric($taskid) && $autoclose) $retJSON->Закрыть = opengoo_complete_task($taskid,$AssignedById);

		} else {
			$retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);
		}
		  $dir = APIConstants::$CacheDir;
		  $filePath = $dir. "/".$CacheName;
		  file_put_contents($filePath,serialize($retJSON));

	}
	return $retJSON;
	}

        function createUslugiMobile($apiMethodParams) {

                $retJSON=$this->createDefaultJson();

                $Title         = opengoo_get_projectname_by_projectID($apiMethodParams->id)." ".opengoo_get_projectname_by_projectID($apiMethodParams->themeId);
                $Assigned2iD   = 472;
                $AssignedById  = 106;

		if($apiMethodParams->Хочет_получить_ответ == "Нет") {
			$autoclose = 1;
		}

//		unset($apiMethodParams->Хочет_получить_ответ);

                if(isset($apiMethodParams->themeId) && isset($apiMethodParams->id) && isset($apiMethodParams->Сообщение)) {
//                      include_once APIConstants::$AllIncludes;
                          $ProjectId     = "5762";
                          $Appeal        = $apiMethodParams->Сообщение;

                          unset($apiMethodParams->id);
                          unset($apiMethodParams->themeId);
                          unset($apiMethodParams->Сообщение);

                          $Data         = (array) $apiMethodParams;
                          if(!$autoclose) $Subscrip     = array("user_id"=>array(217,104,290,325,316,464,472,385));
                        //217-Олеся Головкова;204Альбина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
                        //464-Нигматуллина Алия;472-Саженкова Екатерина;385-Мансурова Фарюза;
                        $taskid = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
                        if(is_numeric($taskid))  $retJSON->taskid = $taskid;
                          else $retJSON = APIConstants::json_error(APIConstants::$ERROR_CREATE_FENGTASK);
			if(is_numeric($taskid) && $autoclose) opengoo_complete_task($taskid,$AssignedById); 
                } else {
                        $retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);
                 }

        return $retJSON;
        }

	function createPortalv2($apiMethodParams) {

		$retJSON=$this->createDefaultJson();

		if(isset($apiMethodParams->themeId) && isset($apiMethodParams->id) && isset($apiMethodParams->Сообщение)) {
//			include_once APIConstants::$AllIncludes;
			  $ProjectId     = $apiMethodParams->id;
			  $Title         = "Портал BetaUslugi: ".opengoo_get_projectname_by_projectID($apiMethodParams->id)." ".opengoo_get_projectname_by_projectID($apiMethodParams->themeId);
			  $Assigned2iD   = 472;
			  $AssignedById  = 106;
			  $Appeal        = $apiMethodParams->Сообщение;

			  unset($apiMethodParams->id);
			  unset($apiMethodParams->themeId);
			  unset($apiMethodParams->Сообщение);

			  $Data         = (array) $apiMethodParams;
			  $Subscrip     = array("user_id"=>array(217,104,290,325,316,464,472,385,644));
			//217-Олеся Головкова;204Альбина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
			//464-Нигматуллина Алия;472-Саженкова Екатерина;385-Мансурова Фарюза;644-Базгутдинова Рузана
			$taskid = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
			if(is_numeric($taskid))  $retJSON->taskid = $taskid;
                          else $retJSON = APIConstants::json_error(APIConstants::$ERROR_CREATE_FENGTASK);
		} else {
			$retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);
		 }

	return $retJSON;
	}


}
