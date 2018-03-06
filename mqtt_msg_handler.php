<?php

require("mqtt/phpMQTT.php");
require("deviceManipulation.php");


$server = "www.futureSmart.top";
$port = 1883;                    
$username = "root";             
$password = "root";            
$client_id = "SmartCloud_Device";

$mqtt = new phpMQTT($server, $port, $client_id);
if(!$mqtt->connect(true, NULL, $username, $password)) {
	exit(1);
}

$topics['device/device_register'] = array("qos" => 0, "function" => "msg_handler");
$topics['device/status_update'] = array("qos" => 0, "function" => "msg_handler");
$topics['user/user_connect'] = array("qos" => 0, "function" => "msg_handler");
$mqtt->subscribe($topics, 0);



$deviceMp = new deviceManipulation($mqtt);


while($mqtt->proc()){
		
}


$mqtt->close();






function msg_handler($topic, $msg){
	echo "\nMsg Recieved: " . date("r") . "\n";
	echo "Topic: {$topic}\n";
	echo "Msg: $msg\n\n";

	global $deviceMp;

	switch($topic)
	{
	case "device/device_register":
		$rt = json_decode($msg);
		$type = $rt->{"type"};
		$vendor = $rt->{"vendor"};
		$mac = $rt->{"MAC"};
		$bssid = $rt->{"BSSID"};
		
/*
		//sscanf($mac, "%02x:%02x:%02x:%02x:%02x:%02x", $mac_addr[0], $mac_addr[1], $mac_addr[2], $mac_addr[3], $mac_addr[4], $mac_addr[5]);
		//sscanf($mac, "%2c:%2c:%2c:%2c:%2c:%2c", $mac_addr[0], $mac_addr[1], $mac_addr[2], $mac_addr[3], $mac_addr[4], $mac_addr[5]);
		//var_dump($mac_addr);
		$mac = $mac_addr[0].$mac_addr[1].$mac_addr[2].$mac_addr[3].$mac_addr[4].$mac_addr[5];
		echo "get MAC:".$MAC;
		sscanf($bssid, "%02x:%02x:%02x:%02x:%02x:%02x", $bssid_addr[0], $bssid_addr[1], $bssid_addr[2], $bssid_addr[3], $bssid_addr[4], $bssid_addr[5]);
		//var_dump($bssid_addr);
		$bssid = $bssid_addr[0].$bssid_addr[1].$bssid_addr[2].$bssid_addr[3].$bssid_addr[4].$bssid_addr[5];
		if($bssid == NULL)		
			$bssid = '000000000000';

		$uuid = $MAC.$BSSID.'1234';
*/
		$uuid = $mac;
		$deviceMp->deviceRegister($type, $vendor, $mac, $bssid, $uuid);

		break;
	case "device/status_update";
                $rt = json_decode($msg);
                $uuid = $rt->{"UUID"};
                $attribute = $rt->{"attribute"};
                $value = $rt->{"value"};

		$deviceMp->statusUpdate($uuid, $attribute, $value);
		break;
	case "user/user_connect";

		$deviceMp->allStatusNotify();
		break;
	}
}

function convert_json($msg) {
	$rt = json_decode($msg);
	$type = $rt->{"type"};
	$vendor = $rt->{"vendor"};
	$mac = $rt->{"MAC"};
	$bssid = $rt->{"BSSID"};

	echo "type:".$type." vendor:".$vendor;
	echo "\n";
}
