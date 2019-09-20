<?php
class fengtask extends apiBaseClass {

	private $fos_subscribers = array("user_id"=>array(104,290,325,316,522,644,472,532,345,695,711,702));
                        //217-Олеся Головкова;522-Кузьмина Алина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
                        //644-Базгутдинова Рузана;472-Саженкова Екатерина;385-Мансурова Фарюза,616-Нарине 345-Хабибрахманова Эльвира Григорян;556-Гарипова Резеда;

	function createTestTask($apiMethodParams) {
		$retJSON=$this->createDefaultJson();

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


	return $retJSON;
	}

        function createUslugiMobile($apiMethodParams) {
                if($apiMethodParams->Устройство=="https://betauslugi2.tatar.ru") {
                        return $this->createPortalv2($apiMethodParams);
                }


                $retJSON=$this->createDefaultJson();

                $Title         = "МП Услуги РТ: " . opengoo_get_projectname_by_projectID($apiMethodParams->id)." ".opengoo_get_projectname_by_projectID($apiMethodParams->themeId);
                $Assigned2iD   = 472;
                $AssignedById  = 106;

		if($apiMethodParams->Хочет_получить_ответ == "Нет") {
			$autoclose = 1;
		}

//		unset($apiMethodParams->Хочет_получить_ответ);

                if(isset($apiMethodParams->themeId) && isset($apiMethodParams->id) && isset($apiMethodParams->Сообщение)) {
                          $ProjectId     = "5762";
                          $Appeal        = $apiMethodParams->Сообщение;

                          unset($apiMethodParams->id);
                          unset($apiMethodParams->themeId);
                          unset($apiMethodParams->Сообщение);

                          $Data         = (array) $apiMethodParams;
                          if(!$autoclose) $Subscrip     = $this->fos_subscribers;
                        //217-Олеся Головкова;204Альбина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
                        //464-Нигматуллина Алия;472-Саженкова Екатерина;385-Мансурова Фарюза
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

                if($apiMethodParams->Хочет_получить_ответ == "Нет") {
                        $autoclose = 1;
                }

		if(isset($apiMethodParams->themeId) && isset($apiMethodParams->id) && isset($apiMethodParams->Сообщение)) {
			  $ProjectId     = $apiMethodParams->id;
			  $Title         = "Портал newuslugi.tatarstan.ru: ".opengoo_get_projectname_by_projectID($apiMethodParams->id)." ".opengoo_get_projectname_by_projectID($apiMethodParams->themeId);
			  $Assigned2iD   = 472;
			  $AssignedById  = 106;
			  $Appeal        = $apiMethodParams->Сообщение;

			  unset($apiMethodParams->id);
			  unset($apiMethodParams->themeId);
			  unset($apiMethodParams->Сообщение);

			  $Data         = (array) $apiMethodParams;
			  if(!$autoclose) $Subscrip     = $this->fos_subscribers;
			//217-Олеся Головкова;204Альбина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
			//464-Нигматуллина Алия;472-Саженкова Екатерина;385-Мансурова Фарюза
			$taskid = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
			if(is_numeric($taskid))  $retJSON->taskid = $taskid;
                          else $retJSON = APIConstants::json_error(APIConstants::$ERROR_CREATE_FENGTASK);
                        if(is_numeric($taskid) && $autoclose) opengoo_complete_task($taskid,$AssignedById);
		} else {
			$retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);
		 }

	return $retJSON;
	}

	function createPortal($apiMethodParams) {

                $retJSON=$this->createDefaultJson();

                if(isset($apiMethodParams->themeId) && isset($apiMethodParams->id) && isset($apiMethodParams->Сообщение)) {
                          $ProjectId     = $apiMethodParams->id;
                          $Title         = "Портал uslugi.tatarstan.ru: ".opengoo_get_projectname_by_projectID($apiMethodParams->id)." ".opengoo_get_projectname_by_projectID($apiMethodParams->themeId);
                          $Assigned2iD   = 472;
                          $AssignedById  = 106;
                          $Appeal        = $apiMethodParams->Сообщение;

                          unset($apiMethodParams->id);
                          unset($apiMethodParams->themeId);
                          unset($apiMethodParams->Сообщение);

                          $Data         = (array) $apiMethodParams;
                          $Subscrip     = $this->fos_subscribers;
                        //217-Олеся Головкова;204Альбина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
                        //464-Нигматуллина Алия;472-Саженкова Екатерина;385-Мансурова Фарюза
                        $taskid = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
                        if(is_numeric($taskid))  $retJSON->taskid = $taskid;
                          else $retJSON = APIConstants::json_error(APIConstants::$ERROR_CREATE_FENGTASK);
                } else {
                        $retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);
                 }

        return $retJSON;
        }

        function createInspektor($apiMethodParams) {

                $retJSON=$this->createDefaultJson();

                if(isset($apiMethodParams->appeal)) {
                          $ProjectId     = "5361";
                          $Title         = "ФОС Народный Инспектор";
                          $Assigned2iD   = 472;
                          $AssignedById  = 106;
                          $Appeal        = $apiMethodParams->appeal;

                          unset($apiMethodParams->appeal);

                          $Data         = (array) $apiMethodParams;
                          $Subscrip     = array("user_id"=>array(217,325,316,644,472,695,711,702));
                        //217-Олеся Головкова;204Альбина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
                        //644-Базгутдинова Рузана;472-Саженкова Екатерина;385-Мансурова Фарюза,616-Нарине Григорян;556-Гарипова Резеда;
                        $taskid = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
                        if(is_numeric($taskid))  $retJSON->taskid = $taskid;
                          else $retJSON = APIConstants::json_error(APIConstants::$ERROR_CREATE_FENGTASK);
                } else {
                        $retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);
                 }

        return $retJSON;
        }

        function createPeopleControl($apiMethodParams) {

                $retJSON=$this->createDefaultJson();

                if(isset($apiMethodParams->appeal)) {
                          $ProjectId     = "5667";
                          $Title         = "ФОС Народный Контроль";
                          $Assigned2iD   = 472;
                          $AssignedById  = 106;
                          $Appeal        = $apiMethodParams->appeal;

                          unset($apiMethodParams->appeal);

                          $Data         = (array) $apiMethodParams;
                          $Subscrip     = array("user_id"=>array(217,325,316,644,472,345,695,711,702));
                        //217-Олеся Головкова;204Альбина;104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;
                        //644-Базгутдинова Рузана;472-Саженкова Екатерина;385-Мансурова Фарюза;616-Нарине Григорян;556-Гарипова Резеда;345-Хабибрахманова Эльвира
                        $taskid = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
                        if(is_numeric($taskid))  $retJSON->taskid = $taskid;
                          else $retJSON = APIConstants::json_error(APIConstants::$ERROR_CREATE_FENGTASK);
                } else {
                        $retJSON = APIConstants::json_error(APIConstants::$ERROR_PARAMS);
                 }

        return $retJSON;
        }



}
