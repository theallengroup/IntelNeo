drop table if exists [#_prefix]usr\g
drop table if exists [#_prefix]role\g
drop table if exists [#_prefix]usr2role\g
drop table if exists [#_prefix]role2priv\g
drop table if exists [#_prefix]privilege\g
drop table if exists [#_prefix]log\g
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
INSERT INTO log VALUES(0,1,'none',-1,'db created',now(),'app');
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
insert into [#_prefix]role	values(0,'[#_admin_name]')\g
insert into [#_prefix]usr2role 	values(0,1,1)\g
insert into [#_prefix]privilege values(1,'[#_users]',		'usr/view:list_all')\g
insert into [#_prefix]privilege values(2,'[#_roles]',		'role/view:list_all')\g
insert into [#_prefix]privilege values(3,'[#_privileges]',	'privilege/view:list_all')\g
insert into [#_prefix]privilege values(4,'[#_usr2role]',		'usr2role/view:list_all')\g
insert into [#_prefix]privilege values(5,'[#_role2priv]',		'role2priv/view:list_all')\g
insert into [#_prefix]privilege values(6,'[#_strings]',		'edit')\g
insert into [#_prefix]privilege values(7,'[#_config]',		'edit/cf')\g
insert into [#_prefix]privilege values(7,'[#_add_field]',	'edit/add_field')\g
insert into [#_prefix]privilege values(7,'[#_sql_console]',	'util/sql_console')\g
insert into [#_prefix]privilege values(7,'[#_logs]',		'log/view:list_readonly')\g
insert into [#_prefix]privilege values(7,'[#_view_add]',	'view/add')\g
insert into [#_prefix]privilege values(7,'[#_view_copy]',	'view/copy')\g
insert into [#_prefix]role2priv values(0,1,1)\g
insert into [#_prefix]role2priv values(0,1,2)\g
insert into [#_prefix]role2priv values(0,1,3)\g
insert into [#_prefix]role2priv values(0,1,4)\g
insert into [#_prefix]role2priv values(0,1,5)\g
insert into [#_prefix]role2priv values(0,1,6)\g
insert into [#_prefix]role2priv values(0,1,7)\g
insert into [#_prefix]usr 	values(0,'[#_admin_name]','[#_admin_email]','[#_admin_password]',0,0,'[#_now]','[#_ip]')
