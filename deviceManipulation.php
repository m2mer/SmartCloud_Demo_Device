<?php


class deviceManipulation {


	private $con;      /* DB connection */

	private $mqtt;     /* MQTT connection*/

	function __construct($mqtt) {
	
		$con = new mysqli("localhost", "root", "root");
		if(!$con)
		{
			die('can not connect: '. mysqli_error($con));
		}
		$this->con = $con;
		$con->select_db("db_device");
		$this->mqtt = $mqtt;
	}

	function testDeviceRegister() {
		//echo "testDeviceRegister called\n";

		$con = $this->con;
		if(!$con)
		{
			die('can not connect: '. mysqli_error($con));
		}

		$con->select_db("db_device");
		$rst = $con->query("INSERT INTO Device_info (Type, Vendor, MAC, BSSID, UUID, Status) 
				VALUES('lamp', 'heelight', '2c3ae82205b1', '', '2c3ae82205b10000000000001234', '0')");
		if(!$rst)
		{
			die("insert Device_info fail: ". mysqli_error($con));
		}

		//$con->close();
	}

	function deviceRegister($type, $vendor, $mac, $bssid, $uuid) {
		echo "deviceRegister called."." Type:".$type." Vendor:".$vendor." MAC:".$mac." BSSID:".$bssid." UUID:".$uuid."\n";

		$con = $this->con;
		$rst = $con->query("INSERT INTO Device_info (Type, Vendor, MAC, BSSID, UUID) 
				VALUES('$type', '$vendor', '$mac', '$bssid', '$uuid')");
		if(!$rst)
		{
			die("insert fail: ". mysqli_error($con));
		}

		$rst = $con->query("INSERT INTO Lamp_status (UUID, online, onoff, lightness, color, mode) 
				VALUES('$uuid', '1', '0', '0', '0', '0')");
		if(!$rst)
		{
			die("insert Device_info fail: ". mysqli_error($con));
		}

		//$con->close();
		
		/* send registration_notify to APP */
		$this->registrationNotify($type, $vendor, $mac, $bssid, $uuid, $status);

	}

	function registrationNotify($type, $vendor, $mac, $bssid, $uuid, $status) {
		$topic = "device/registration_notify";
		//$msg = "{\"type\":\"$type\",\"Vendor\":\"$vendor\",\"MAC\":\"$mac\",\"BSSID\":\"$bssid\",\"UUID\":\"$uuid\",\"status\":\"$status\"}";
		//$msg = "{\"MAC\":\"$mac\",\"action\":\"1\",\"value\":\"1\"}";
		//$msg = "{\"UUID\":\"$uuid\"}";
		$msg = "{\"UUID\":\"$uuid\"}";

        	$this->mqtt->publish($topic, $msg, 0);
       	 	//$this->mqtt->close();

	}

	function statusUpdate($uuid, $attribute, $value)
	{
		$con = $this->con;
                $rst = $con->query("UPDATE Lamp_status SET $attribute = '$value'
                                WHERE UUID = '$uuid' ");
                if(!$rst)
                {
                        die("update fail: ". mysqli_error($con));
                }

	}

	function allStatusNotify()
	{
                $con = $this->con;
                $rst = $con->query("SELECT * from Lamp_status");
		echo "$rst->num_rows items in Lamp_status\n";
		
		while($row = $rst->fetch_array())
		{
			$uuid = $row['UUID'];
			$online = $row['online'];
			$onoff = $row['onoff'];
			$lightness = $row['lightness'];
			$color = $row['color'];
			$mode = $row['mode'];

			$topic = "device/status_notify";
			$msg = "{\"UUID\":\"$uuid\",\"online\":\"$online\",\"onoff\":\"$onoff\",\"lightness\":\"$lightness\",\"color\":\"$color\",\"mode\":\"$mode\"}";
			/* sometimes fwrite error, don't know why */
        		$this->mqtt->publish($topic, $msg, 0);
		}

	}





}

?>
