<?php
class apiBaseClass {
    
    public $mySQLWorker=null;//Одиночка для работы с базой
    
    //Конструктор с возможными параметрами
    function __construct($dbName=null,$dbHost=null,$dbUser=null,$dbPassword=null) {
        if (isset($dbName)){//Если имя базы передано то будет установленно соединение с базой
            $this->mySQLWorker = MySQLiWorker::getInstance($dbName,$dbHost,$dbUser,$dbPassword);
        }
	//Инклудим всё
	include_once APIConstants::$AllIncludes;
    }
    
    function __destruct() {
        if (isset($this->mySQLWorker)){             //Если было установленно соединение с базой, 
            $this->mySQLWorker->closeConnection();  //то закрываем его когда наш класс больше не нужен
        }
    }
    
//Функция описана в /fengoffice/cache.php 
//Инклудится в APIConstants::AllIncludes
/*    function file_get_contents2($Str) {
	  $seconds = 30*60;
	  $dir = APIConstants::$CacheDir;
	  $filePath = $dir. "/".$Str;
	  if(!file_exists($filePath)) { // если не существует данный КЕШ
	    return false;
	  }
	  elseif(filemtime($filePath) < ( time() -  $seconds  )  ) { // если жизнь данного кеша меньше чем SECONDS
	    return false;
	  }
	  else { // значит кеш есть и он по требованиям актуальный
	    $fileContent = file_get_contents($filePath); // читаем локальный кеш
	  }
	  return $fileContent;
    } */
    
    //Создаем дефолтный JSON для ответов
    function createDefaultJson() {
        $retObject = json_decode('{}');
        return $retObject;
    }
    
    //Заполняем JSON объект по ответу из MySQLiWorker
    function fillJSON(&$jsonObject, &$stmt, &$mySQLWorker) {
        $row = array();
        $mySQLWorker->stmt_bind_assoc($stmt, $row);
        while ($stmt->fetch()) {
            foreach ($row as $key => $value) {
                $key = strtolower($key);
                $jsonObject->$key = $value;
            }
            break;
        }
        return $jsonObject;
    }
}

?>
