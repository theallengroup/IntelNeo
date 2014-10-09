CREATE TABLE `star_schema` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`key_name` VARCHAR (32)  NOT NULL default '',
`description` MEDIUMTEXT NOT NULL default '',
`source` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
);

CREATE TABLE `dimension_table` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`star_schema_id` INTEGER default '0',
`source` VARCHAR (32)  NOT NULL default '',
`label` VARCHAR (32)  NOT NULL default '',
`color` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
);

CREATE TABLE `fact_table` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`star_schema_id` INTEGER default '0',
`source` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
);

CREATE TABLE `dimension_table_field` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`dimension_table_id` INTEGER default '0',
`source` VARCHAR (32)  NOT NULL default '',
`label` VARCHAR (32)  NOT NULL default '',
`dimension_table_field_id` INTEGER default '0',
`chart_type_id` INTEGER default '0',
PRIMARY KEY (`id`)
);

CREATE TABLE `chart_type` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
);

CREATE TABLE `fact_table_field` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`fact_table_id` INTEGER default '0',
`label` VARCHAR (32)  NOT NULL default '',
`source` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
);

CREATE TABLE `cube` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`star_schema_id` INTEGER default '0',
`description` MEDIUMTEXT NOT NULL default '',
`label` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
);

CREATE TABLE `path` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`cube_id` INTEGER default '0',
`label` VARCHAR (32)  NOT NULL default '',
PRIMARY KEY (`id`)
);

CREATE TABLE `path_data_fields` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`path_id` INTEGER default '0',
PRIMARY KEY (`id`)
);

CREATE TABLE `path_hierarchy` (
`id` INTEGER auto_increment ,
`name` VARCHAR (32)  NOT NULL default '',
`path_id` INTEGER default '0',
`path_hierarchy_id` INTEGER default '0',
PRIMARY KEY (`id`)
);

CREATE TABLE `cube_dimension_field` (
`id` INTEGER auto_increment ,
`cube_id` INTEGER default '0',
`dimension_table_field_id` INTEGER default '0',
PRIMARY KEY (`id`)
);

CREATE TABLE `cube_fact_field` (
`id` INTEGER auto_increment ,
`cube_id` INTEGER default '0',
`fact_table_field_id` INTEGER default '0',
PRIMARY KEY (`id`)
);

CREATE TABLE `role2path` (
`id` INTEGER auto_increment ,
`role_id` INTEGER NOT NULL default '0',
`path_id` INTEGER default '0',
PRIMARY KEY (`id`)
);


