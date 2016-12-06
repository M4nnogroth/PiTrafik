<?php 
function getAuthKey () {
	session_start();

	$id = session_id();
	$url = 'https://api.vasttrafik.se/token';
	$authRequest = curl_init($url);

	curl_setopt($authRequest, CURLOPT_URL, $url);
	curl_setopt($authRequest, CURLOPT_POST, 1);
		//Varje användare behöver unikt device_id (scope=)
	curl_setopt($authRequest, CURLOPT_POSTFIELDS, 'grant_type=client_credentials&scope=device_'.$id);
	curl_setopt($authRequest, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($authRequest, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($authRequest, CURLOPT_SSL_VERIFYHOST, 0);

		//nyckel:hemlighet mellan rad 18,19 
	curl_setopt($authRequest, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/x-www-form-urlencoded',
		'Authorization: Basic ' . base64_encode('Auth key goes here')
		));

	$authResponse = curl_exec($authRequest);

	return (json_decode($authResponse, true)['access_token']);		
		//echo json_decode($authResponse, true)['access_token'];		
}

function getId($name, $authKey) {
	$url = 'https://api.vasttrafik.se/bin/rest.exe/v2/location.name?input='.urlencode($name).'&format=json'; 
	$headers = array(
		'Authorization: Bearer '.$authKey
		);
	$authRequest = curl_init($url);

	curl_setopt($authRequest, CURLOPT_URL, $url);
	curl_setopt($authRequest, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($authRequest, CURLOPT_RETURNTRANSFER, true);		
	curl_setopt($authRequest, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($authRequest, CURLOPT_SSL_VERIFYHOST, 0);

	$authResponse = curl_exec($authRequest);
	echo (curl_error($authRequest));
	return json_decode($authResponse, true);
}

function getDeparture($id, $currentDate, $currentTime, $authKey) {
	$url = 'https://api.vasttrafik.se/bin/rest.exe/v2/departureBoard?id='.$id.'&date='.$currentDate.'&time='.$currentTime.'&format=json'; 
	$headers = array(
		'Authorization: Bearer '.$authKey
		);
	$authRequest = curl_init($url);

	curl_setopt($authRequest, CURLOPT_URL, $url);
	curl_setopt($authRequest, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($authRequest, CURLOPT_RETURNTRANSFER, true);		
	curl_setopt($authRequest, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($authRequest, CURLOPT_SSL_VERIFYHOST, 0);

	$authResponse = curl_exec($authRequest);
	echo (curl_error($authRequest));
	return json_decode($authResponse, true);
}

function getRemainingTime($serverTime, $time) {
	$serverTime = strtotime($serverTime);
	$time = strtotime($time);

	while ($serverTime>$time) {
		$time+=60*60*24;
	}

	return ($time-$serverTime)/60;
}

//WIP
function getWeather() {
	$url = 'http://www.yr.no/place/Sverige/Västra_Götaland/Göteborg/forecast.xml';

	$request = curl_init($url);

	curl_setopt($request, CURLOPT_URL, $url);
	curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($request, CURLOPT_FOLLOWLOCATION,1);

	$xml = curl_exec($request);
	echo (curl_error($request));
	

	return new simpleXMLElement($xml);
}
?>