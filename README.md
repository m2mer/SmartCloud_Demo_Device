Device manipulation for SmartCloud Demo


# database
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

# mqtt protocol
| Topic | Message | Direction | Note 
| - | :- | :- | - 
| device/device_register |{<br>"type":"lamp",<br>"vendor":"ht",<br>"MAC":"2c3ae82205b1"<br>}| pub: Device <br> sub: Cloud | esp8266 payload length limit
| device/registration_notify | {<br>"UUID":"2c3ae82205b1"<br>} | pub: cloud <br> sub: APP | UUID use MAC for now
| device/device_operate | {<br>"UUID":"2c3ae82205b1"<br>"action":"onoff"<br>"value":"1"<br>} | pub: APP <br> sub: Device |
| device/status_update | {<br>"UUID":"2c3ae82205b1"<br>"attribute":online",<br>"value":"1"<br>}<br>{<br>"UUID":"2c3ae82205b1"<br>"attribute":"onoff",<br>"value":"1"<br>}<br>{<br>"UUID":"2c3ae82205b1"<br>"attribute":"lightness",<br>:"value":50"<br>} <br> {<br>"UUID":"2c3ae82205b1"<br>"attribute":"color",<br>"value":"200"<br>} <br> {<br>"UUID":"2c3ae82205b1"<br>"attribute":"mode",<br>"value":"0"<br>} | pub: Device <br> sub:Cloud, APP | after device execute operation or device online
| device/status_notify | {<br>"UUID":"2c3ae82205b1",<br>"online":"1",<br>"onoff":"1",<br>"lightness":"50", <br> "color":"200", <br> mode":"0"<br>}  | pub: Cloud <br> sub: APP | notify all devices one by one after APP open
| user/user_connect | {<br> "UUID":""<br>} | pub: APP <br> sub: Cloud | when APP open
