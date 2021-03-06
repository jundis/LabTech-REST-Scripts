<html>
<head>
   <link rel="stylesheet" href="bootstrap.min.css" media="screen">
   <meta http-equiv="refresh" content="120">
</head>

<div class="table-responsive">
<table class="sortable table table-striped table-bordered table-hover"">
	<thead>
		<tr>
			<th>Client</th>
			<th>Server</th>
			<th>Last Checkin</th>
		</tr>
	</thead>
	<tbody>
<?php
ini_set('display_errors', 1); //Display errors in case something occurs

//CHANGE THESE
$ltuser="apiuser";
$ltpassword="PASSWORD HERE";
$timezone = "America/Chicago"; //Set your timezone here.
$labtechurl = "https://lt.domain.tld"; //Set your LabTech URL here.
$labtechversion = "11"; //Change to 10.5 or 10 if not on LT11.


//Set variables
date_default_timezone_set($timezone);
$datenow = date("Y-m-d\TH:i:s", strtotime("-10 minutes"));
$date7 = date("Y-m-d\TH:i:s", strtotime("-7 days"));
$timez = new DateTime("now", new DateTimeZone($timezone));
$utccode = $timez->format('P');

$url = NULL; //to be changed in a moment. Prevents IDE errors.
if ($labtechversion == "11")
{
	$url = $labtechurl . '/WCC2/api/ComputerStubs?$filter=contains(OS,%27Server%27)%20and%20LastCheckin%20lt%20' . $datenow . "$utccode%20and%20LastCheckin%20gt%20" . $date7 . "$utccode";
}
else
{
	$url = $labtechurl . '/WCC2/api/ComputerStubs?$filter=substringof(%27Server%27,OS)%20and%20LastCheckin%20lt%20datetime%27' . $datenow . "%27%20and%20LastCheckin%20gt%20datetime%27" . $date7 . "%27";
}
$urlapi = $labtechurl . '/WCC2/API/APIToken';

//CURL for API key
$ch = curl_init(); //Initiate a curl session_cache_expire

$body = json_encode(array("username" => $ltuser, "password" => $ltpassword));
//Create curl array to set the API url, headers, and necessary flags.
$curlOpts = array(
	CURLOPT_URL => $urlapi,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
	CURLOPT_POSTFIELDS => $body,
	CURLOPT_POST => 1,
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


$apikey = str_replace('"', "", $curlBodyTData); //Enter your LabTech REST API key here.

$header_data =array(
 "Authorization: LTToken ". $apikey,
);

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

foreach($dataTData as $array)
{
	$date=strtotime($array['LastCheckin']); //Convert date entered JSON result to time.
	$dateformat=date('m-d-Y g:i:sa',$date); //Convert previously converted time to a better time string.
	if($date < strtotime("-7 days"))
	{
		$color = "9599a0";
	}
	elseif($date >= strtotime("-7 days") && $date < strtotime("-2 days"))
	{
		$color = "e8e545";
	}
	elseif($date >= strtotime("-2 days") && $date < strtotime("-4 hours"))
	{
		$color = "e2a151";
	}
	else
	{
		$color = "d48888";
	}
	
	echo "<tr>
	<td>".$array['Client']."</td>
	<td>".$array['Name']."</td>
	<td bgcolor=#" . $color . ">".$dateformat."</td>
	</tr>";
}


?>

	</tbody>
</table>
<?php
echo '&nbsp;&nbsp;&nbsp;Updated: ' . date("m/d/y h:ia");
?>
</div>
</html>