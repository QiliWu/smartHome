CREATE DATABASE db_arduino;
USE db_arduino;

CREATE TABLE `family_member`
(
	`openid` VARCHAR(30) CHARACTER SET utf8 NOT NULL,
	`name` VARCHAR(30) CHARACTER SET utf8 NOT NULL,
	`black` BOOLEAN DEFAULT FALSE,
	`pass` BOOLEAN DEFAULT FALSE,
	PRIMARY KEY(`openid`)
);

CREATE TABLE `control`
(
	`seq_num` INT AUTO_INCREMENT NOT NULL,
	`create_time` TIMESTAMP COMMENT '微信何时下达该命令,MySQL会自动为该字段赋值',
	`command` TINYINT COMMENT '形如10、11、20',
	`status` Enum('finished','overdue','ing') DEFAULT 'ing',
	PRIMARY KEY(`seq_num`)
);

CREATE TABLE `surround`
(
	`seq_num` INT AUTO_INCREMENT NOT NULL,
	`upload_time` TIMESTAMP COMMENT 'MySQL会自动为该字段赋值',
	`dht11_t` FLOAT(5,2) COMMENT '温度',
	`dht11_h` FLOAT(5,2) COMMENT '湿度',
	`mq2` FLOAT(5,3) COMMENT '烟雾',
	`bh1750` SMALLINT COMMENT '光',
	PRIMARY KEY(`seq_num`)
);

INSERT INTO control (`command`,`status`) VALUES (0,'finished');	--防止Workerman查询数据库时出错
INSERT INTO surround (`dht11_t`,`dht11_h`,`mq2`,`bh1750`) VALUES (0,0,0,0);	--防止Workerman查询数据库时出错

CREATE USER 'family'@'localhost','light'@'localhost','door'@'localhost';
flush PRIVILEGES;
GRANT SELECT,INSERT,UPDATE ON db_arduino.* TO 'family'@'localhost','light'@'localhost','door'@'localhost';
flush PRIVILEGES;
