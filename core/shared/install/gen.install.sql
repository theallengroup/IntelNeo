drop table if exists log\g
drop table if exists usr\g
drop table if exists role\g
drop table if exists usr2role\g
drop table if exists role2priv\g
drop table if exists privilege\g
create table if not exists usr (
	id int(10) not null auto_increment,
	name varchar(50),
	email varchar(80),
	password varchar(100),
	login_count int(10),
	last_login datetime,
	created_date datetime,
	last_ip varchar(20),
	primary key(id)
)\g
CREATE TABLE `log` (
`id` INTEGER auto_increment ,
`usr_id` INTEGER NOT NULL default '0',
`table_name` VARCHAR (32)  NOT NULL default '',
`record` VARCHAR (32)  NOT NULL default '',
`info` MEDIUMTEXT NOT NULL default '',
`log_date` DATETIME NOT NULL default '1900-01-01 00:00:00',
`log_level` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
)\g
INSERT INTO log VALUES(0,1,'none',-1,'db created',now(),'app')\g
create table if not exists role(
	id int(10) not null auto_increment,
	name varchar(50),
	primary key(id)
)\g
create table if not exists usr2role(
	id int(10) not null auto_increment,
	usr_id int(10),
	role_id int(10),
	primary key(id)
)\g
create table if not exists privilege(
	id int(10) not null auto_increment,

	name varchar(50),
	action varchar(200),
	primary key(id)
)\g
create table if not exists role2priv(
	id int(10) not null auto_increment,
	role_id int(10),
	privilege_id int(10),
	primary key(id)
)\g
insert into role values(0,'Administrator')\g
insert into usr2role values(0,1,1)\g
insert into privilege values(1,'Usuarios',		'usr/view:list_all')\g
insert into privilege values(2,'Roles',			'role/view:list_all')\g
insert into privilege values(3,'Privilegios',		'privilege/view:list_all')\g
insert into privilege values(4,'Roles por Usuario','usr2role/view:list_all')\g
insert into privilege values(5,'Privilegos por Rol',	'role2priv/view:list_all')\g
insert into privilege values(6,'Cadenas',	'edit')\g
insert into privilege values(7,'Configuraci&oacute;n',	'edit/cf')\g
insert into privilege values(8,'Agregar Campo',	'edit/add_field')\g
insert into privilege values(9,'Consola SQL',	'util/sql_console')\g
insert into privilege values(10,'Eventos',		'log/view:list_readonly')\g
insert into privilege values(11,'Agregar Vista',	'view/add')\g
insert into privilege values(12,'Copiar Vista',	'view/copy')\g
insert into role2priv values(0,1,1)\g
insert into role2priv values(0,1,2)\g
insert into role2priv values(0,1,3)\g
insert into role2priv values(0,1,4)\g
insert into role2priv values(0,1,5)\g
insert into role2priv values(0,1,6)\g
insert into role2priv values(0,1,7)\g
insert into role2priv values(0,1,8)\g
insert into role2priv values(0,1,9)\g
insert into role2priv values(0,1,10)\g
insert into role2priv values(0,1,11)\g
insert into role2priv values(0,1,12)\g
insert into usr values(0,'Administrator','root','0d107d09f5bbe40cade3de5c71e9e9b7',0,0,'2000-01-01 00:00:00','127.0.0.1')
