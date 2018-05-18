
# mesh agent message
Note: data in table are for reference

| Topic | Message | Direction | Binary | Note 
| - | - | - | :- | - |
| device/deviceId/device_register | Gateway:<br> {"type":"00010000", "Vendor":"AISmart", "MAC":"18fe34d4795e", "meshId":"f2140d51ffff"}<br> Node:<br> {"type":"00020003", "Vendor":"AISmart", "MAC":"3400ce0daf75ffff",  "gatewayId":"2c3ae82205b1"}| pub: Gateway <br> sub: Cloud | NA |
| device/deviceId/device_deleted | {"UUID":"f2140d51ffff"} | pub: Gateway <br> sub: Cloud | NA | mesh node deleted notify
| device/deviceId/reset_factory | {"UUID":"f2140d51ffff"} | pub: Gateway <br> sub: APP, Cloud | NA | Gateway/mesh node reset factory notify
| device/deviceId/heartbeat | {"UUID":"18fe34d4795e"} | pub: Gateway <br> sub: APP, Cloud | NA | heartbeat of Gateway
| device/deviceId/get_status | {<br>"UUID":"18fe34d4795e",<br>"attribute":"mesh_agent",<br>"value":"3400ce0daf75ffff"<br>}| pub: APP <br> sub: Gateway | struct {<br>  &nbsp;uint8_t command; <br>&nbsp; uint8_t reserved; <br>&nbsp; uint8_t mac[6]; <br> } |
| device/deviceId/status_reply | {<br>"UUID":"18fe34d4795e",<br>"attribute":"mesh_agent",<br>"value":"3800ce0daf75ffff<br>00020001640007d0012c646400000000"<br>}| pub: Gateway <br> sub: APP, cloud | struct {<br>  &nbsp;uint8_t command; <br>&nbsp; uint8_t sequence; <br>&nbsp; uint8_t mac[6]; <br>&nbsp; uint8_t firstType; <br>&nbsp; uint8_t secondType; <br>&nbsp; uint8_t group; <br>&nbsp; uint8_t onoff; <br>&nbsp; uint8_t lightness; <br>&nbsp; uint8_t mode; <br>&nbsp; uint16_t temperature; <br>&nbsp; uint16_t h; <br>&nbsp; uint16_t s; <br>&nbsp; uint16_t v; <br> } |
| device/deviceId/device_operate | {<br>"UUID":"18fe34d4795e",<br>"attribute":"mesh_agent",<br>"value":"3200ce0daf75ffff000100000000"<br>}| pub: APP <br> sub: Gateway | struct {<br>  &nbsp;uint8_t command; <br>&nbsp; uint8_t reserved; <br>&nbsp; uint8_t mac[6]; <br>&nbsp; uint8_t funcType; <br>&nbsp; uint8_t funcPara[5]; <br> } |
| device/deviceId/status_update | {<br>"UUID":"18fe34d4795e",<br>"attribute":"mesh_agent",<br>"value":"3600ce0daf75ffff01000100000000"<br>}| pub: Gateway <br> sub: APP, cloud | struct {<br>  &nbsp;uint8_t command; <br>&nbsp; uint8_t reserved; <br>&nbsp; uint8_t mac[6]; <br>&nbsp; uint8_t sequence; <br>&nbsp; uint8_t funcType; <br>&nbsp; DEVICE_FUNCTION_PARA status; <br>&nbsp; }<br> union {<br> &nbsp;uint8_t offline; <br>&nbsp; uint8_t onoff; <br>&nbsp; uint8_t lightness; <br>&nbsp; uint8_t mode; <br>&nbsp; uint16_t temperature; <br>&nbsp; DEVICE_COLOR color; <br>&nbsp; } <br> struct {<br> &nbsp;uint16_t h;<br> &nbsp;uint8_t s; <br>&nbsp; uint8_t v;<br>}|
