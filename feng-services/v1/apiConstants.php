<?php
class APIConstants {

    //Для хранения кэша
    public static $CacheDir = "/fengoffice/cache";

    public static $Cacheable_Requests = true;

    public static $CacheableKey = "nonecacheable";

    public static $CacheableSecondsKey = "cacheableseconds";

    public static $NoneCacheableFuncs = array("fengtask.createtesttask","fengtask.createuslugimobile","fengtask.createportalv2");

    public static $UncacheableStatus = "uncacheable";

    //Все инклуды
    public static $AllIncludes = "/fengoffice/feng-services/v1/feng_includes.php";

    //Результат запроса - параметр в JSON ответе
    public static $RESULT_CODE="resultCode";
    
    //Ответ - используется как параметр в главном JSON ответе в apiEngine
    public static $RESPONSE="response";
    
    //Нет ошибок
    public static $ERROR_NO_ERRORS = 0;
    
    //Ошибка в переданных параметрах
    public static $ERROR_PARAMS = 101;
    
    //Ошибка в подготовке SQL запроса к базе
    public static $ERROR_STMP = 1;

    //Ошибка запись не найдена
    public static $ERROR_RECORD_NOT_FOUND = 103;
    
    //Ошибка создания задачи в ФенгОффис
    public static $ERROR_CREATE_FENGTASK = 104;

    //Ошибка в параметрах запроса к серверу. Не путать с ошибкой переданных параметров в метод
    public static $ERROR_ENGINE_PARAMS = 100;
    
    //Ошибка zip архива
    public static $ERROR_ENSO_ZIP_ARCHIVE = 1001;
    
    public function error_name($error_code) {
	switch($error_code) {
	  case APIConstants::$ERROR_PARAMS : return "Wrong Parameters"; break;
	  case APIConstants::$ERROR_STMP : return "Sql connect error"; break;
          case APIConstants::$ERROR_RECORD_NOT_FOUND : return "Record not found"; break;
          case APIConstants::$ERROR_CREATE_FENGTASK : return "Cant create FengOffice task"; break;
          case APIConstants::$ERROR_ENGINE_PARAMS : return "Server engine error"; break;
          case APIConstants::$ERROR_NO_ERRORS : return "All is fine"; break;

	  default : return "Unknown error";
		break;
	}
    }

    public function json_error($status) {
           $retJSON=$this->createDefaultJson();
           $retJSON->errno = $status;  $retJSON->error = APIConstants::error_name($status);
        return $retJSON;
     }

}
?>
