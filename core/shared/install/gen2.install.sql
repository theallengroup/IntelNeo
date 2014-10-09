drop table if exists [#_prefix]log\g
drop table if exists [#_prefix]usr\g
drop table if exists [#_prefix]role\g
drop table if exists [#_prefix]usr2role\g
drop table if exists [#_prefix]role2priv\g
drop table if exists [#_prefix]privilege\g
create table if not exists [#_prefix]usr (
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
CREATE TABLE [#_prefix]log (
`id` INTEGER auto_increment ,
`usr_id` INTEGER NOT NULL default '0',
`table_name` VARCHAR (32)  NOT NULL default '',
`record` VARCHAR (32)  NOT NULL default '',
`info` MEDIUMTEXT NOT NULL default '',
`log_date` DATETIME NOT NULL default '1900-01-01 00:00:00',
`log_level` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
)\g
INSERT INTO [#_prefix]log VALUES(0,1,'none',-1,'db created',now(),'app')\g
create table if not exists [#_prefix]role(
	id int(10) not null auto_increment,
	name varchar(50),
	primary key(id)
)\g
create table if not exists [#_prefix]usr2role(
	id int(10) not null auto_increment,
	usr_id int(10),
	role_id int(10),
	primary key(id)
)\g
create table if not exists [#_prefix]privilege(
	id int(10) not null auto_increment,

	name varchar(50),
	action varchar(200),
	primary key(id)
)\g
create table if not exists [#_prefix]role2priv(
	id int(10) not null auto_increment,
	role_id int(10),
	privilege_id int(10),
	primary key(id)
)\g
insert into [#_prefix]role values(0,'Administrator')\g
insert into [#_prefix]usr2role values(0,1,1)\g
insert into [#_prefix]privilege values(1,'[##usr]',			'usr/all*')\g
insert into [#_prefix]privilege values(2,'[##role]',			'role/all*')\g
insert into [#_prefix]privilege values(3,'[##privilege]',		'privilege/all*')\g
insert into [#_prefix]privilege values(4,'[##usr2role]',		'usr2role/all*')\g
insert into [#_prefix]privilege values(5,'[##role2priv]',		'role2priv/all*')\g
insert into [#_prefix]privilege values(6,'[##event]',			'log/read_only*')\g
insert into [#_prefix]privilege values(7,'Ayuda',			'help/help')\g
insert into [#_prefix]privilege values(8,'Editar Perfil',		'usr/edit_profile*')\g
insert into [#_prefix]privilege values(9,'DBAdmin',			'edit/db_manager')\g
insert into [#_prefix]role2priv values(0,1,1)\g
insert into [#_prefix]role2priv values(0,1,2)\g
insert into [#_prefix]role2priv values(0,1,3)\g
insert into [#_prefix]role2priv values(0,1,4)\g
insert into [#_prefix]role2priv values(0,1,5)\g
insert into [#_prefix]role2priv values(0,1,6)\g
insert into [#_prefix]role2priv values(0,1,7)\g
insert into [#_prefix]role2priv values(0,1,8)\g
insert into [#_prefix]role2priv values(0,1,9)\g
insert into [#_prefix]usr values(0,'Administrator','root','31c23a230051702de27d954c3f4d25f9',0,0,'2000-01-01 00:00:00','127.0.0.1')
