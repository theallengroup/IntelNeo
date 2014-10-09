
--2014-07-31 14:38 1 = Administrator
ALTER TABLE activity_status DROP activity_value ;
--2014-07-31 14:40 1 = Administrator
ALTER TABLE activity_type ADD activity_value  int(100) DEFAULT '1';
--2014-07-31 14:42 1 = Administrator
;
--2014-07-31 14:44 1 = Administrator
CREATE TABLE category( 
	id int(10) not null auto_increment ,
	name VARCHAR(100) ,
	primary key(id)
);
--2014-07-31 14:44 1 = Administrator
INSERT INTO privilege VALUES(0,'categories','category/all*');
--2014-07-31 14:44 1 = Administrator
ALTER TABLE question ADD category_id  int(10) DEFAULT '1';
--2014-07-31 14:47 1 = Administrator
;
--2014-08-11 13:37 1 = Administrator
CREATE TABLE activity2session( 
	id int(10) not null auto_increment ,
	activity_id VARCHAR(100) ,
	session_id VARCHAR(100) ,
	primary key(id)
);
--2014-08-11 13:37 1 = Administrator
INSERT INTO privilege VALUES(0,'Activities por Session','activity2session/all*');
--2014-08-11 13:37 1 = Administrator
INSERT INTO role2priv VALUES(0,'2','25/all*');
--2014-08-13 02:25 1 = Administrator
CREATE TABLE completed_activity( 
	id int(10) not null auto_increment ,
	activity_date VARCHAR(100) ,
	primary key(id)
);
--2014-08-13 02:25 1 = Administrator
INSERT INTO privilege VALUES(0,'Completed Activities','completed_activity/all*');
--2014-08-13 02:27 1 = Administrator
ALTER TABLE completed_activity ADD usr_id  int(10) DEFAULT '1';
--2014-08-13 02:27 1 = Administrator
ALTER TABLE completed_activity ADD activity_id  int(10) DEFAULT '1';
--2014-08-13 02:27 1 = Administrator
;
--2014-08-13 02:28 1 = Administrator
-- drop session_id
alter table activity drop foreign key fk_activity_session1;
alter table activity drop column session_id; 

--2014-08-13 11:20 1 = Administrator
ALTER TABLE question_option ADD is_correct  ENUM('Y','N') DEFAULT 'N';
--2014-08-19 01:00 1 = Administrator
CREATE TABLE message( 
	id int(10) not null auto_increment ,
	name VARCHAR(100) ,
	primary key(id)
);
--2014-08-19 01:00 1 = Administrator
INSERT INTO privilege VALUES(0,'messages','message/all*');
--2014-08-19 01:00 1 = Administrator
INSERT INTO role2priv VALUES(0,'2','28/all*');
--2014-08-19 01:01 1 = Administrator
ALTER TABLE message ADD source  int(100) DEFAULT '1';
--2014-08-19 01:01 1 = Administrator
ALTER TABLE message ADD destination  int(100) DEFAULT '1';
--2014-08-19 01:02 1 = Administrator
ALTER TABLE message ADD body TEXT DEFAULT '1';
--2014-08-19 01:02 1 = Administrator
ALTER TABLE message ADD body TEXT DEFAULT '';
--2014-08-19 01:02 1 = Administrator
ALTER TABLE message ADD view_date datetime DEFAULT '2000-01-01';
--2014-08-19 01:05 1 = Administrator
ALTER TABLE message ADD creation_date datetime DEFAULT '2000-01-01';
--2014-08-19 01:13 1 = Administrator
ALTER TABLE message DROP name ;
--2014-08-24 00:02 1 = Administrator
CREATE TABLE team( 
	id int(10) not null auto_increment ,
	name VARCHAR(100) ,
	primary key(id)
);
--2014-08-24 00:02 1 = Administrator
INSERT INTO privilege VALUES(0,'Teams','team/all*');
--2014-08-24 00:02 1 = Administrator
INSERT INTO role2priv VALUES(0,'2','29/all*');
--2014-08-24 00:17 1 = Administrator
ALTER TABLE activity ADD is_team_activity  ENUM('Y','N') DEFAULT 'N';
--2014-08-24 00:19 1 = Administrator
ALTER TABLE activity ADD is_team_activity  ENUM('Y','N') DEFAULT '';
--2014-08-24 00:19 1 = Administrator
ALTER TABLE activity ADD is_team_activity  ENUM('Y','N') DEFAULT 'N';
--2014-08-24 00:19 1 = Administrator
ALTER TABLE activity ADD is_team_activity  ENUM('Y','N') DEFAULT 'N';
--2014-08-25 00:37 1 = Administrator
ALTER TABLE activity ADD timer  int(10) DEFAULT '120';
--2014-08-26 21:36 1 = Administrator
ALTER TABLE completed_activity ADD team  int(10) DEFAULT '0';
--2014-08-27 07:40 1 = Administrator
CREATE TABLE user_tutorial( 
	id int(10) not null auto_increment ,
	name VARCHAR(100) ,
	primary key(id)
);
--2014-08-27 07:40 1 = Administrator
INSERT INTO privilege VALUES(0,'User Tutorials','user_tutorial/all*');
--2014-08-27 07:40 1 = Administrator
INSERT INTO role2priv VALUES(0,'2','30/all*');
--2014-08-27 07:41 1 = Administrator
ALTER TABLE user_tutorial DROP name ;
--2014-08-27 07:41 1 = Administrator
ALTER TABLE user_tutorial ADD usr_id  int(10) DEFAULT '1';
--2014-08-27 07:44 1 = Administrator
ALTER TABLE user_tutorial ADD name  VARCHAR(100) DEFAULT '';