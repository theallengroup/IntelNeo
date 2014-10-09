CREATE TABLE `std_chunk` (
`id` INTEGER auto_increment ,
`chunk_category_id` INTEGER default '0',
`name` VARCHAR (32)  NOT NULL default '',
`clause` VARCHAR (32)  NOT NULL default '',
`rtable` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
)\g

CREATE TABLE `std_report` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`rtype` VARCHAR (32)  NOT NULL default '',
`title` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
)\g

CREATE TABLE `std_report2table` (
`id` INTEGER auto_increment ,
`report_id` INTEGER default '0',
`table_name` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
)\g

CREATE TABLE `std_chunk2report` (
`id` INTEGER auto_increment ,
`chunk_id` INTEGER default '0',
`report_id` INTEGER default '0',
PRIMARY KEY (`id`)
)\g

CREATE TABLE `std_chunk_category` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
)
