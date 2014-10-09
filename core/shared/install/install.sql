drop table if exists usr;
drop table if exists role;
drop table if exists usr2role;
drop table if exists role2priv;
drop table if exists privilege;
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
);
create table if not exists role(
	id int(10) not null auto_increment,
	name varchar(50),
	primary key(id)
);
create table if not exists usr2role(
	id int(10) not null auto_increment,
	usr_id int(10),
	role_id int(10),
	primary key(id)
);
create table if not exists privilege(
	id int(10) not null auto_increment,

	name varchar(50),
	action varchar(200),
	primary key(id)
);
create table if not exists role2priv(
	id int(10) not null auto_increment,
	role_id int(10),
	privilege_id int(10),
	primary key(id)
);
insert into role values(0,'Administrator');
insert into usr2role values(0,1,1);
insert into privilege values(1,'Usuarios',		'usr/view:list_all');
insert into privilege values(2,'Roles',			'role/view:list_all');
insert into privilege values(3,'Privilegios',		'privilege/view:list_all');
insert into privilege values(4,'Roles por Usuario','usr2role/view:list_all');
insert into privilege values(5,'Privilegos por Rol',	'role2priv/view:list_all');
insert into privilege values(6,'Cadenas',	'edit');
insert into privilege values(7,'Configuraci&oacute;n',	'edit/cf');
insert into role2priv values(0,1,1);
insert into role2priv values(0,1,2);
insert into role2priv values(0,1,3);
insert into role2priv values(0,1,4);
insert into role2priv values(0,1,5);
insert into role2priv values(0,1,6);
insert into role2priv values(0,1,7);
insert into usr values(0,'Administrator','root','0d107d09f5bbe40cade3de5c71e9e9b7',0,0,'2000-01-01 00:00:00','127.0.0.1')
