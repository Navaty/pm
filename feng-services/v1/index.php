<?php
header('Content-type: application/json; charset=UTF-8');
//print_r($_REQUEST);

if(count($_REQUEST)>0){
    require_once 'apiEngine.php';
    foreach ($_REQUEST as $apiFunctionName => $apiFunctionParams) {
        $APIEngine=new APIEngine($apiFunctionName,$apiFunctionParams);
        echo $APIEngine->callApiFunction();
//print_r(preg_replace('/\_/', '.', $apiFunctionName));
        break;
    }
}else{
    if(!isset($jsonError)) $jsonError = new stdClass();
    $jsonError->error='No function called';
    echo json_encode('No function called');
}
?>
