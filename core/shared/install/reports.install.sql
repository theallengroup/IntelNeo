CREATE TABLE campo_de_agrupamiento( 
	id int(10) not null auto_increment ,
	nombre VARCHAR(100) ,
	informe_id int(10) ,
	posicion VARCHAR(100) ,
	es_filtrable VARCHAR(100) ,
	primary key(id)
);
CREATE TABLE chart_type( 
	id int(10) not null auto_increment ,
	name VARCHAR(100) ,
	primary key(id)
);
CREATE TABLE columna( 
	id int(10) not null auto_increment ,
	informe_id int(10) ,
	nombre VARCHAR(100) ,
	titulo VARCHAR(100) ,
	orden int(10) ,
	calculado VARCHAR(100) ,
	visible VARCHAR(100) ,
	primary key(id)
);
CREATE TABLE data_field( 
	id int(10) not null auto_increment ,
	drill_down_report_id int(10) ,
	label VARCHAR(100) ,
	expression VARCHAR(100) ,
	primary key(id)
);
CREATE TABLE drill_down_report( 
	id int(10) not null auto_increment ,
	name VARCHAR(100) ,
	description TEXT ,
	table_name VARCHAR(100) ,
	primary key(id)
);
CREATE TABLE fuente( 
	id int(10) not null auto_increment ,
	columna_id int(10) ,
	campo VARCHAR(100) ,
	valor VARCHAR(100) ,
	campo_fuente VARCHAR(100) ,
	primary key(id)
);
CREATE TABLE dimension_field( 
	id int(10) not null auto_increment ,
	drill_down_report_id int(10) ,
	name VARCHAR(100) ,
	label VARCHAR(100) ,
	chart_type_id int(10) ,
	primary key(id)
);
CREATE TABLE userfilter( 
	id int(10) not null auto_increment ,
	sql_filter VARCHAR(100) ,
	usr_id int(10) ,
	reporte VARCHAR(100) ,
	primary key(id)
);
CREATE TABLE userquery( 
	id int(10) not null auto_increment ,
	titulo VARCHAR(100) ,
	fecha_de_creacion datetime ,
	usr_id int(10) ,
	es_publica VARCHAR(100) ,
	fuente_de_datos VARCHAR(100) ,
	sql_text TEXT ,
	primary key(id)
);
CREATE TABLE informe( 
	id int(10) not null auto_increment ,
	nombre VARCHAR(100) ,
	titulo VARCHAR(100) ,
	fuente_de_datos VARCHAR(100) ,
	descripcion TEXT ,
	primary key(id)
);
