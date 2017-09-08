<?php
$data = array( "fengtask.createtesttask" => json_encode(array(
        'appeal' => 'test',
        'test' => 'test'
)));

$url = 'http://pm.citrt.net/feng-services/v1/';
$ch = curl_init($url);
$postString = http_build_query($data, '', '&');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
print_r($response);
?>
