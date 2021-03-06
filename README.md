Device manipulation for SmartCloud Demo

## Test steps:
0.run mqtt_msg_handler.php to receive mqtt message;  

1.use below commands to register and operate a device.  

```
mosquitto_pub -h "www.futureSmart.top" -t 'device/device_register' -m '{"type":"lamp","vendor":"ht","MAC":"2c3ae82205b1"}'  
```


```
mosquitto_pub -h "www.futureSmart.top" -t 'device/status_update' -m '{"UUID":"2c3ae82205b1","attribute":"onoff","value":"1"}'

```

2.use below command to simulate an APP open;

```
mosquitto_pub -h "www.futureSmart.top" -t 'user/user_connect' -m ""
```

3.access database in each step to check result. 

```
use db_device;  
select * from Device_info;  
select * from Lamp_status;
```

## Database

##### TABLE `Device_info`
```
(
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`type` varchar(255) NOT NULL COMMENT 'type',
`vendor` varchar(255) NOT NULL COMMENT 'vendor',
`MAC` char(12) NOT NULL COMMENT 'MAC',<br>
`BSSID` char(12) NOT NULL COMMENT 'BSSID',
`UUID` varchar(28) NOT NULL COMMENT 'UUID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

```

##### TABLE `Lamp_status`

```
(
`UUID` varchar(28) NOT NULL COMMENT 'UUID',
`online` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'onoff_line',
`onoff` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'onoff',
`lightness` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'lightness',
`color` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'color',
`mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'mode',
PRIMARY KEY (`UUID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
```

## MQTT protocol

Note: data in table are for reference

|ID| Topic | Message | Direction | Version | Note 
|-| - | :- | :- | - | - |
|1| device/device_register |{<br>"BSSID"："323besfe",</br>"type":"lamp",<br>"vendor":"ht",<br>"MAC":"2c3ae82205b1"<br>}| pub: Device <br> sub: Cloud |  v1.0, v1.1 |esp8266 payload length limit， **BSSID and UUID is important** |
|2| device/registration_notify | {<br>"UUID":"2c3ae82205b1"<br>} | pub: cloud <br> sub: APP | v1.0, v1.1 | UUID use MAC for now
|3| ***device/device_operate*** | {<br>"UUID":"2c3ae82205b1"<br>"action":"onoff"<br>"value":"1"<br>} | pub: APP <br> sub: Device | v1.0, v1.1 | 
|4| ***device/device_operate*** | {</br> "UUID":"2c3ae82205b1",</br> "action":"lightness",</br>"value":"2"</br>} | | v1.0, v1.1 | lightness operate	|
|5| ***device/device_operate*** | {</br> "UUID":"2c3ae82205b1",</br> "action":"color",</br>"value":"200, 50, 50"</br>} | |v1.0, v1.1 |color operate **HSB** or **HSV** type	|
|6| ***device/device_operate*** | {</br> "UUID":"2c3ae82205b1",</br> "action":"mode",</br>"value":"Lightning"</br>} | | v1.0, v1.1 |light mode operate	value is **Lightning, Reading, Meal, Movie, Party, Night Lamp**|
|7| device/status_update | {<br>"UUID":"2c3ae82205b1"<br>"attribute":online",<br>"value":"1"<br>}<br>{<br>"UUID":"2c3ae82205b1"<br>"attribute":"onoff",<br>"value":"1"<br>}<br>{<br>"UUID":"2c3ae82205b1"<br>"attribute":"lightness",<br>:"value":50"<br>} <br> {<br>"UUID":"2c3ae82205b1"<br>"attribute":"color",<br>"value":<br>{"h":300,"s":100,"v":80}<br>} <br> {<br>"UUID":"2c3ae82205b1"<br>"attribute":"mode",<br>"value":"0"<br>} | pub: Device <br> sub:Cloud, APP | v1.0, v1.1 | after device execute operation or device online | 
|8| device/status_notify | {<br>"UUID":"2c3ae82205b1",<br>"online":"1",<br>"onoff":"1",<br>"lightness":"50", <br> "color":"200", <br> mode":"0"<br>}  | pub: Cloud <br> sub: APP | v1.0 | notify all devices one by one after APP open<br>(deprecated in v1.1, use https)
|9|~~*user/user_connect*~~| ~~{<br> "UUID":""<br>}~~ | ~~pub: APP <br> sub: Cloud~~|~~v1.0~~| ~~when APP open<br>(deprecated in v1.1, use https)~~
|10 |device/get_status | {<br>"UUID":"2c3ae82205b1"<br>"action":"get_status"<br>} | pub: APP <br> sub: Device | v1.1 | APP want to get overall status of device
|11|device/status_reply | {<br>"UUID":"2c3ae82205b1", <br>"onoff":1,<br>"lightness":80,<br>"temperature":3000,<br>"color":{"h":300,"s":100,"v":80},<br>"mode":"Reading",<br>"timer_on":0,<br>"timer_off":0 <br>} | pub: Device <br> sub:Cloud, APP | v1.1 | overall status 
|12| device/update_brief | {<br>"UUID":"2c3ae82205b1", <br>"onoff":1,<br>"lightness":80,<br>"temperature":3000,<br>"mode":"Reading",<br>"online":1 <br>} | pub: Device <br> sub:Cloud, APP | v1.1 | device notify brief status every second

## Light Protocol

|ID| Function | Direction | Status | Description | Note |
|-| - | - | - | - | - |
|1| **onoff** | updown | brief | on:1, off:0 |
|2| **lightness**| updown | brief | 1-100 |
|3| **temperature** | updwon | brief | 2700K - 6000K |
|4| **color** | updown | overall | {h: 1-360, s: 1-100, v: 1-100} | hsv color model
|5| **mode** | updown | brief | Lightning, Reading, Meal, Movie, Party, Night Lamp |
|6| **timer_on** | updown | overall | 1-1488 | minutes(24 hours) |
|7| **timer_off** | updown | overall | 1-1488 | minutes(24 hours) |
|8| **online** | up | brief | online:1, off_line:0 |


## Cloud interface for APP

### Discovery

* Request

```json
{
    "header": {
        "namespace": "FutureSmart.Light.Discovery",
        "name": "DiscoveryLight",
        "userId": "1bd5d003",
        "username": "testName",
        "phone": "18923654231"
    },
    "payload": {
        "groupId": "1",
        "groupName":"testGroupName"
    }
}
```

* Response

```json
{
    "header": {
        "namespace": "FutureSmart.Light.Discovery",
        "name": "DiscoveryLightResponse",
        "userId": "1bd5d003",
        "username": "testName",
        "phone": "18923654231"
    },
    "payload": {
        "devices": [{
            "UUID": "2c3ae82205b1",
            "deviceName": "light",
            "deviceType": "light",
            "groupId": "1",
            "groupName": "testGroupName",
            "groupType": "testGroupType",
            "zone": "Bedroom",
            "vendor": "heelight",
            "icon": "https://www.futuresmart.top/static/deviceIcon/light.png",
            "attribute":[
                "onoff",
                "lightness",
                "temperature",
                "color",
                "mode",
                "timer",
                "online"
            ],
            "actions": [
                "On",
                "Off",
                "lightness",
                "color",
                "mode",
                "timer"
            ],
            "extensions": {
                "extension1": "",
                "extension2": ""
            }
        }]
    }
}
```

### Control

* Request

```json
{
    "header": {
        "namespace": "FutureSmart.Light.Control",
        "name": "ControlLight",
        "userId": "1bd5d003",
        "username": "testName",
        "phone": "18923654231"
    },
    "payload": {
        "UUID": "2c3ae82205b1",
        "deviceName": "testDeviceName",
        "deviceType": "Light",
        "attribute":[
          {
            "name": "zone",
            "value": "Kitchen"
          }
        ], 
        "action": [
            {
                "name": "on",
                "value": "1"
            },
            {
                "name": "lightness",
                "value": "110"
            },
            {
                "name": "color",
                "value": "100, 20, 130"
            }
        ]
    }
}

```

* Response

```json
{
  "header":{
      "namespace":"FutureSmart.Light.Control",
      "name":"ControlLightResponse",
      "userId": "1bd5d003",
      "username": "testName",
      "phone": "18923654231"
   },
   "payload":{
       "UUID":"2c3ae82205b1",
       "deviceName": "testDeviceName",
       "deviceType":"Light"
    }
}
```

### Group Control

* Request

```json
{
    "header": {
        "namespace": "FutureSmart.Light.Control",
        "name": "ControlLightGroup",
        "userId": "1bd5d003",
        "username": "testName",
        "phone": "18923654231"
    },
    "payload": {
        "groups": [
            {
                "groupId": "2c3ae82205b1",
                "groupName": "testDeviceName",
                "groupType": "Light",
                "action": [
                    {
                        "name": "on",
                        "value": "1"
                    },
                    {
                        "name": "lightness",
                        "value": "110"
                    },
                    {
                        "name": "color",
                        "value": "100, 20, 130"
                    }
                ]
            }
        ]
    }
}
```

* Response

```json
{
    "header": {
        "namespace": "FutureSmart.Light.Control",
        "name": "ControlLightGroupResponse",
        "userId": "1bd5d003",
        "username": "testName",
        "phone": "18923654231"
    },
    "payload": {
        "groups": [
            {
                "groupId": "2c3ae82205b1",
                "groupName": "testDeviceName",
                "groupType": "Light"
            }
        ]
    }
}
```

### Query

* Request

```json
{
  "header":{
      "namespace":"FutureSmart.Light.Query",
      "name":"QueryLight",
      "userId": "1bd5d003",
      "username": "testName",
      "phone": "18923654231"
   },
   "payload":{
       "UUID":"2c3ae82205b1",
       "deviceName": "testDeviceName",
       "deviceType":"Light",
       "attribute":[
              "onoff",
              "lightness"
      ]
    }
}

```

* Response

```json
{
  "header":{
      "namespace":"FutureSmart.Light.Query",
      "name":"QueryLightResponse",
      "userId": "1bd5d003",
      "username": "testName",
      "phone": "18923654231"
   },
   "payload":{
       "UUID":"2c3ae82205b1",
       "deviceName": "testDeviceName",
       "deviceType":"Light",
       "attribute":[
             {
              "name": "onoff",
              "value": "1"
             },
             {
              "name": "lightness",
              "value": "120"
             }
      ]
    }
}

```

### Group Query

* Request

```json
{
    "header": {
        "namespace": "FutureSmart.Light.Query",
        "name": "QueryLightGroup",
        "userId": "1bd5d003",
        "username": "testName",
        "phone": "18923654231"
    },
    "payload": {
        "groups": [
            {
                "groupId": "2c3ae82205b1",
                "groupName": "testDeviceName",
                "groupType": "Light",
                "attribute": [
                    "onoff",
                    "lightness"
                ]
            }
        ]
    }
}
```

* Response

```json
{
    "header": {
        "namespace": "FutureSmart.Light.Query",
        "name": "QueryLightGroupResponse",
        "userId": "1bd5d003",
        "username": "testName",
        "phone": "18923654231"
    },
    "payload": {
        "groups": [
            {
                "groupId": "2c3ae82205b1",
                "groupName": "testDeviceName",
                "groupType": "Light",
                "devices": [
                    {
                        "UUID": "2c3ae82205b1",
                        "deviceName": "testDeviceName",
                        "deviceType": "Light",
                        "attribute": [
                            {
                                "name": "onoff",
                                "value": "1"
                            },
                            {
                                "name": "lightness",
                                "value": "120"
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```