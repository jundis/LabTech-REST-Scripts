<?php
ini_set('display_errors', 1); //Display errors in case something occurs

$labtechurl = "https://lt.domain.tld" //Enter your LabTech URL here.
$apikey = "LT API KEY HERE"; //Enter your LabTech REST API key here.
$timezone = "America/Chicago"; //Set your timezone here. 

date_default_timezone_set($timezone);
$datenow = date("Y-m-d\TH:i:s", strtotime("-10 minutes"));
$date7 = date("Y-m-d\TH:i:s", strtotime("-7 days"));

$header_data =array(
 "Authorization: LTToken ". $apikey,
);

$url = $labtechurl . '/WCC2/api/ComputerStubs?$filter=substringof(%27Server%27,OS)%20and%20LastCheckin%20lt%20datetime%27' . $datenow . "%27%20and%20LastCheckin%20gt%20datetime%27" . $date7 . "%27";

$ch = curl_init(); //Initiate a curl session_cache_expire

//Create curl array to set the API url, headers, and necessary flags.
$curlOpts = array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => $header_data,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HEADER => 1,
);
curl_setopt_array($ch, $curlOpts); //Set the curl array to $curlOpts

$answerTData = curl_exec($ch); //Set $answerTData to the curl response to the API.
$headerLen = curl_getinfo($ch, CURLINFO_HEADER_SIZE);  //Get the header length of the curl response
$curlBodyTData = substr($answerTData, $headerLen); //Remove header data from the curl string.

// If there was an error, show it
if (curl_error($ch)) {
	die(curl_error($ch));
}
curl_close($ch);

//Funky conversion for LT Data.
$dataTData = json_decode($curlBodyTData); //Decode the JSON returned by the CW API.
$dataTData = json_decode(json_encode($dataTData->value),true);
$dataTData = $dataTData[0];

echo "<table>";
foreach($dataTData as $k=>$v)
{
	echo "<tr><td>$k</d><td>$v</td></tr>";
}
echo "</table>";

?>