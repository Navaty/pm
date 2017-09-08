<?php
require_once('MySQLWorker.php');
require_once ('apiConstants.php');

class APIEngine {

    private $apiFunctionName;
    private $apiFunctionParams;
    private $filePath;

    //Функция
    function get_cache_response($name, $params) {

	$iscacheable=true;
	//Возвращаем ответ некешируемый
	if(!APIConstants::$Cacheable_Requests) $iscacheable=false; //Если все запросы не кешируются
	if(strtolower($params->{APIConstants::$CacheableKey}) == md5(APIConstants::$CacheableKey)) $iscacheable=false; //Если в параметрах есть ключ некэшируемости и он равен хэшу этого ключа для предотвращения случайностей
	if(in_array(strtolower($name), APIConstants::$NoneCacheableFuncs, true)) $iscacheable=false; //Если метод определен в массиве нехэшируемых запросов

	$seconds = (is_numeric($params->{APIConstants::$CacheableSecondsKey}))? $params->{APIConstants::$CacheableSecondsKey} : 1800; //Если ключ времени кеша число, то ставим его

	unset($params->{APIConstants::$CacheableKey});
	unset($params->{APIConstants::$CacheableSecondsKey});
	if(!$iscacheable) return APIConstants::$UncacheableStatus;

	$this->filePath = APIConstants::$CacheDir."/".$name.".".md5(serialize($params));//определяем путь к кэшу
          if(!file_exists($this->filePath)) { // если не существует данный КЕШ
            return false;
          }
          elseif(filemtime($this->filePath) < ( time() -  $seconds  )  ) { // если жизнь данного кеша меньше чем SECONDS
            return false;
          }
          else { // значит кеш есть и он по требованиям актуальный
            $fileContent = file_get_contents($this->filePath); // читаем локальный кеш
            return unserialize($fileContent);//unserialize($fileContent);
	 }
    }


    //Статичная функция для подключения API из других API при необходимости в методах
    static function getApiEngineByName($apiName) {
        require_once 'apiBaseClass.php';
        require_once $apiName . '.php';
        $apiClass = new $apiName();
        return $apiClass;
    }
    
    //Конструктор
    //$apiFunctionName - название API и вызываемого метода в формате apitest_helloWorld
    //$apiFunctionParams - JSON параметры метода в строковом представлении
    function __construct($apiFunctionName, $apiFunctionParams) {
        $this->apiFunctionParams = stripcslashes($apiFunctionParams);
        //Парсим на массив из двух элементов [0] - название API, [1] - название метода в API
        $this->apiFunctionName = explode('_', $apiFunctionName);
    }

    //Создаем JSON ответа
    function createDefaultJson() {
        $retObject = json_decode('{}');
        $response = APIConstants::$RESPONSE;
        $retObject->$response = json_decode('{}');
        return $retObject;
    }
    
    //Вызов функции по переданным параметрам в конструкторе
    function callApiFunction() {
        $resultFunctionCall = $this->createDefaultJson();//Создаем JSON  ответа
        $apiName = strtolower($this->apiFunctionName[0]);//название API проиводим к нижнему регистру
        if (file_exists($apiName . '.php')) {
            $apiClass = APIEngine::getApiEngineByName($apiName);//Получаем объект API
            $apiReflection = new ReflectionClass($apiName);//Через рефлексию получем информацию о классе объекта
            try {
                $functionName = $this->apiFunctionName[1];//Название метода для вызова
                $apiReflection->getMethod($functionName);//Провераем наличие метода
                $jsonParams = json_decode($this->apiFunctionParams);//Декодируем параметры запроса в JSON объект
//		$this->filePath = APIConstants::$CacheDir. "/".$apiName.".".$functionName.".".md5(serialize($jsonParams));
//		return $this->filePath;
		if ($jsonParams) {
                    if (isset($jsonParams->responseBinary)){//Для возможности возврата не JSON, а бинарных данных таких как zip, png и др. контетнта
//                        return $apiClass->$functionName($jsonParams);//Вызываем метод в API
                    }else{
			$cache_response = $this->get_cache_response($apiName.".".$functionName, $jsonParams); //Читаем кеш по названию метода и его параметрам
                        $resultFunctionCall = ($cache_response && $cache_response!=APIConstants::$UncacheableStatus)? $cache_response : $apiClass->$functionName($jsonParams);//Если кеш не NULL и при этом он кешируемый, то отдаем кеш, в других случаях Вызыаем метод в API который вернет JSON обект
			if(!$cache_response) file_put_contents($this->filePath,serialize($resultFunctionCall)); //Если кеша нету и метод кешируемый, то создаем его
                    }
                } else {
                    //Если ошибка декодирования JSON параметров запроса
                    $resultFunctionCall->errno = APIConstants::$ERROR_ENGINE_PARAMS;
                    $resultFunctionCall->error = 'Error given params';
                }
            } catch (Exception $ex) {
                //Непредвиденное исключение
                $resultFunctionCall->error = $ex->getMessage();
            }
        } else {
            //Если запрашиваемый API не найден
            $resultFunctionCall->errno = APIConstants::$ERROR_ENGINE_PARAMS;
            $resultFunctionCall->error = 'File not found';
            $resultFunctionCall->REQUEST = $_REQUEST;
        }
        return json_encode($resultFunctionCall);
    }
}

?>
