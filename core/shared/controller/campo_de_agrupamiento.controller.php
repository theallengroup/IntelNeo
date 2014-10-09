<?php #2009-05-15 15:38:07
class campo_de_agrupamiento_model extends std{
	function campo_de_agrupamiento_model(){
	$this->std();
	}
	var $ifield='nombre';
	var $id='id';
	var $table='campo_de_agrupamiento';
	var $mod_get_kids_fields=array('id','nombre','posicion','es_filtrable');
}
?>
