CREATE TABLE `event` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`table_name` VARCHAR (32)  NOT NULL default '',
`function_name` VARCHAR (32)  NOT NULL default '',
`function_parameters` VARCHAR (32)  NOT NULL default '',
`enabled` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
)\g

CREATE TABLE `event2tag` (
`id` INTEGER auto_increment ,
`tag_id` INTEGER NOT NULL default '0',
`event_id` INTEGER default '0',
PRIMARY KEY (`id`)
)
