Device manipulation for SmartCloud Demo

## Test steps:
0.run mqtt_msg_handler.php to receive mqtt message;  

1.use below commands to register and operate a device.  
mosquitto_pub -h "www.futureSmart.top" -t 'device/device_register' -m '{"type":"lamp","vendor":"ht","MAC":"2c3ae82205b1"}'  
mosquitto_pub -h "www.futureSmart.top" -t 'device/status_update' -m '{"UUID":"2c3ae82205b1","attribute":"onoff","value":"1"}'

2.use below command to simulate an APP open;  
mosquitto_pub -h "www.futureSmart.top" -t 'user/user_connect' -m ""

3.access database in each step to check result.  
use db_device;  
select * from Device_info;  
select * from Lamp_status;

## Database
TABLE `Device_info`(<br>
&emsp;`id` int(8) unsigned NOT NULL AUTO_INCREMENT,<br>
&emsp;`type` varchar(255) NOT NULL COMMENT 'type',<br>
&emsp;`vendor` varchar(255) NOT NULL COMMENT 'vendor',<br>
&emsp;`MAC` char(12) NOT NULL COMMENT 'MAC',<br>
&emsp;`BSSID` char(12) NOT NULL COMMENT 'BSSID',<br>
&emsp;`UUID` varchar(28) NOT NULL COMMENT 'UUID',<br>
&emsp;PRIMARY KEY (`id`)<br>
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

TABLE `Lamp_status`(<br>
&emsp;`UUID` varchar(28) NOT NULL COMMENT 'UUID',<br>
&emsp;`online` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'onoff_line',<br>
&emsp;`onoff` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'onoff',<br>
&emsp;`lightness` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'lightness',<br>
&emsp;`color` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'color',<br>
&emsp;`mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'mode',<br>
&emsp;PRIMARY KEY (`UUID`)<br>
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

## MQTT protocol
| Topic | Message | Direction | Note 
| - | :- | :- | - 
| device/device_register |{<br>"BSSID"："323besfe", </br>"UUID":"2c3ae82205b1", </br>type":"lamp",<br>"vendor":"ht",<br>"MAC":"2c3ae82205b1"<br>}| pub: Device <br> sub: Cloud | esp8266 payload length limit， **BSSID and UUID is important** |
| device/registration_notify | {<br>"UUID":"2c3ae82205b1"<br>} | pub: cloud <br> sub: APP | UUID use MAC for now
| ***device/device_operate*** | {<br>"UUID":"2c3ae82205b1"<br>"action":"onoff"<br>"value":"1"<br>} | pub: APP <br> sub: Device | turn on/off light |
| ***device/device_operate*** | {</br> "UUID":"2c3ae82205b1",</br> "action":"lightness",</br>"value":"2"</br>} | | lightness operate	|
| ***device/device_operate*** | {</br> "UUID":"2c3ae82205b1",</br> "action":"color",</br>"value":"200, 50, 50"</br>} | | color operate **HSB** or **HSV** type	|
| ***device/device_operate*** | {</br> "UUID":"2c3ae82205b1",</br> "action":"mode",</br>"value":"Lightning"</br>} | | light mode operate	value is **Lightning, Reading, Meal, Movie, Party, Night Lamp**|
| device/status_update | {<br>"UUID":"2c3ae82205b1"<br>"attribute":online",<br>"value":"1"<br>}<br>{<br>"UUID":"2c3ae82205b1"<br>"attribute":"onoff",<br>"value":"1"<br>}<br>{<br>"UUID":"2c3ae82205b1"<br>"attribute":"lightness",<br>:"value":50"<br>} <br> {<br>"UUID":"2c3ae82205b1"<br>"attribute":"color",<br>"value":"200"<br>} <br> {<br>"UUID":"2c3ae82205b1"<br>"attribute":"mode",<br>"value":"0"<br>} | pub: Device <br> sub:Cloud, APP | after device execute operation or device online **UUID is unique id for Device, same value with device MAC** |
| device/status_notify | {<br>"UUID":"2c3ae82205b1",<br>"online":"1",<br>"onoff":"1",<br>"lightness":"50", <br> "color":"200", <br> mode":"0"<br>}  | pub: Cloud <br> sub: APP | notify all devices one by one after APP open, **UUID is device unique ID, smame value with device MAC** |
| user/user_connect | {<br> "BSSID":"323besfe", </br>"UUID":"efdse238osdj23938"<br>} | pub: APP <br> sub: Cloud | when APP open, **UUID is user phone unique ID** |

