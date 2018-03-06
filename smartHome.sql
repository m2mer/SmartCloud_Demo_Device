
DROP DATABASE db_device;

CREATE DATABASE db_device;

USE db_device;


CREATE TABLE Device_info(
    id int(8) unsigned NOT NULL AUTO_INCREMENT,
    type varchar(255) NOT NULL COMMENT 'type',
    vendor varchar(255) NOT NULL COMMENT 'vendor',
    MAC char(12) NOT NULL COMMENT 'MAC',
    BSSID char(12) NOT NULL COMMENT 'BSSID',
    UUID char(28) NOT NULL COMMENT 'UUID',
    PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE Lamp_status(
    UUID varchar(28) NOT NULL COMMENT 'UUID',
    online tinyint(1) NOT NULL DEFAULT '0' COMMENT 'onoff_line',
    onoff tinyint(1) NOT NULL DEFAULT '0' COMMENT 'onoff',
    lightness tinyint(1) NOT NULL DEFAULT '0' COMMENT 'lightness',
    color tinyint(1) NOT NULL DEFAULT '0' COMMENT 'color',
    mode tinyint(1) NOT NULL DEFAULT '0' COMMENT 'mode',
    PRIMARY KEY (UUID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
