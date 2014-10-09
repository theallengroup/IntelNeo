<?php
	global $config;

echo('<link rel="stylesheet" href="'.MEDIA_DIR.'rlib/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="'.MEDIA_DIR.'rlib/elitezebras-2-0.css" type="text/css" media="screen" />
<script src="'.MEDIA_DIR.'rlib/elitezebras-2-0.js" type="text/javascript"></script>');

	/** busca valor (p.e. valor_ventas)*/
	function is_money($field){
		if(preg_match('/valor/',$field)){
			return(true);
		}else{
			return(false);
		}
	}
	function is_amount($field){
		if(preg_match('/(cantidad|volumen)/',$field)){
			return(true);
		}else{
			return(false);
		}
	}
	function humanize($field){
		global $labels;
		if(isset($labels[$field])){
			return($labels[$field]);
		}
		$field = strtolower($field);
		$field = str_replace('sum(','',$field);
		$field = str_replace('count(','',$field);
		$field = str_replace(')','',$field);
		$field = preg_replace('/_id$/','',$field);
		$field = str_replace('_',' ',$field);
		$field = str_replace('nn','ñ',$field);
		$field = strtoupper($field[0]).substr($field,1);
		return($field);
	}
	$startTime = array_sum(explode(" ",microtime()));
	include(dirname(__FILE__)."/report.php");

	/**
	 *
	 * report
	 * @param $report Estructura_Reporte
	 * report.title=Yopur Title Goes Here
	 * report.table=A Valid SQL Table Name
	 * report.grouped=[1|0] whether or not to use group by (use 0 on query400 breaklvl reports)
	 * report.restrict=A Valid SQL WHERE CLAUSE (optional)
	 * report.preserve=array()
	 * report.preserve.0=mod
	 * report.preserve.1=ac
	 * report.preserve.2=report_name
	 * report.preserve.3=other_GET_parameters
	 * report.dimension_fields=array()
	 * report.dimension_fields.YYY=YYY // a Valid Field Name
	 * report.dimension_fields.XXX=array()
	 * report.dimension_fields.XXX.chart_type=[pie|bar|table] (use table when no chart is desired)
	 * report.dimension_fields.XXX.0=sum(Data_field)
	 * report.dimension_fields.XXX.1=sum(Data_field2))
	 * report.data_fields=array()
	 * report.data_fields.0=sum(Data_field0)
	 * report.data_fields.1=sum(Data_field1)
	 * report.data_fields.2=sum(Data_field2)
	 * report.data_fields.3=array()
	 * report.data_fields.3.label=label
	 * report.data_fields.3.value=data_field_name
	 * report.data_fields.3.from=3
	 * report.data_fields.3.until=6
	 * report.data_fields.3.graph=[1|0]	whether or not to create a graph out of this particular field (for cartera.pizano)
	 * */
	function show_report($report){
		global $config,$labels,$main;
		$max_elements=19;//hey
		$rep=new cReportSNT("SNT");
		
		$rep->configMySQL($config['database_host'],$config['database_user'],$config['database_password'],$config['database_name']);

		$stack_dimension_fields_array=array();

		//parse new syntax
		$from=array();
		$is_money=array();
		$until=array();
		$graph=array();
		$labels=array();
		foreach($report['data_fields'] as $k1=>$a){
			if(is_array($a)){
				if(isset($a["label"])){
					$labels[$a["value"]]=$a["label"];	
				}else{
					$labels[$a["value"]]=$a["value"];
				}
				$from[$a["value"]]=$a["from"];	//appear from THIS level of depth
				$until[$a["value"]]=$a["until"];	//appear from THIS level of depth
				$is_money[$a["value"]]=$a["is_money"];	//appear from THIS level of depth
				$graph[$a["graph"]]=$a["graph"];	//appear from THIS level of depth
				$report['data_fields'][$k1]=$a["value"];
			}else{
				$from[$a]=0;	//appear in ALL levels
				#$labels[$a]=$a;	
			}
		}

		$x=0;
		foreach($report['dimension_fields'] as $dimension_field=>$dimension_field_properties){
			$show_chart_in_this_level = 1;
			//dimension_data_fields
			$tables=array();
			$imagenes=array();
			$stack_dimension_fields_array[$dimension_field] = $dimension_field;
			if(is_array($dimension_field_properties)){
				if(isset($dimension_field_properties["label"])){
					$labels[$dimension_field] = $dimension_field_properties["label"];
				}
				
				if(isset($dimension_field_properties["chart_type"])){
					$ctype = $dimension_field_properties["chart_type"];
					if($dimension_field_properties["chart_type"] == 'table'){
						//NO IMAGE
						$show_chart_in_this_level = 0;
					}
				}else{
					$ctype='bar';
				}
			}else{
				$ctype='bar';
			}


			$cols=array();
			$cols[]=array(
				"source"=>$dimension_field,
				"label"=>humanize($dimension_field),
				"sortable"=>true,
			);


			//obsolete			
			/*
			if(is_array($dimension_data_fields)){
				$all = array_merge($report['data_fields'],$dimension_data_fields);
			}else{
				$all = $report['data_fields'];
			}
			*/

			$all = $report['data_fields'];

			foreach($all as $data_field){
				if(substr($data_field,0,strlen('valor_'))=='valor_'){
					$type='numeric';
					$prefix='$';
					$number_format=true;
				}else{
					$type='not_numeric';
					$number_format=false;
					$prefix='';
				}
				$a1=array(
					"source"=>$data_field,
					"label"=>humanize($data_field),
					"type"=> $type,
					"numberformat"=> $number_format,
					"pre"=> $prefix,                                                        
					"sortable"=>true,
				);
				$a2=array(//MONEY
						"pre"=>"$",
						"type"=>"numeric",
						"numberformat"=>true,
						"sortable"=>true,
					);
				$amounts=array(//AMOUNTS
						"type"=>"numeric",
						"numberformat"=>true,
						"sortable"=>true,
					);
				if(is_money($data_field) || $is_money[$data_field]){
					foreach($a2 as $a2k=>$a2v){
						$a1[$a2k]=$a2v;
					}
				}
				if(is_amount($data_field)){
					foreach($amounts as $a2k=>$a2v){
						$a1[$a2k]=$a2v;
					}
				}
				$cols[]=$a1;
				$i1 = array(
					"title" => humanize($data_field) . " por ".humanize($dimension_field),
					"seriename" => "Total ".humanize($data_field),
					"type" => $ctype,
					"labels_variable" => $dimension_field,
					"values_variable" => $data_field,
					"xlabel" => humanize($dimension_field),
					"xorientation" => "vertical",				
					"ylabel" => "Total ".humanize($data_field),
					"autosort"=> false,//No Ordenar los valores
				/*
				"trendzone" =>  array(
						    array(
							"label"=>"Mas Barato",
							"start"=>0,
							"end"=>2000000000,
							"color"=>"999933"),
						    array(
							"label"=>"Medio",
							"start"=>2000000000,
							"end"=>4000000000,
							"color"=>"339933"),                                                    
					    ),
				 */
				);

				//only place links if we are not in the LAST level
				if(count($report['dimension_fields']) - 1 != $x){
					$i1["dynamic_link"]=$stack_dimension_fields_array;
					#echo("<br>$x NOT last level");
				}else{
					#echo("<br>$x last level");
				}
				#dbg echo("<xmp>".print_r($from,true)."</xmp><hr>");
				if(!isset($until[$data_field])){
					$until[$data_field]=999;
				}
				if(isset($from[$data_field]) && $from[$data_field]<=$x){//only add from this level ON
					if($until[$data_field] > $x) {
						if($show_chart_in_this_level==1){
							$imagenes[] = $i1;
						}
					}

					#echo("<br>$data_field GETS OP1 added in level:$x since:".$from[$data_field]);
				}elseif(!isset($from[$data_field])){
					//no FROM is given, assume OK
					
					if($until[$data_field] > $x) {
						if($show_chart_in_this_level==1){
							$imagenes[] = $i1;
						}
					}
					#echo("<br>$data_field GETS OP2  added in level:$x since:".$from[$data_field]);
				}else{
					#echo("<br>$data_field not added in level:$x since:".$from[$data_field]);
				}
				

			}// datafield

			if(count($report['dimension_fields']) - 1 != $x){
				//The Final Link				
				$cols[]=array(
					"label"=>"&nbsp;",
					"sortable"=>false,
					"dynamic_link"=>$stack_dimension_fields_array,
					"link_label"=>"drill down",
				);
			}
			$tables[]=array( // UNA TABLA
				"name"  => "",//Resumen de Datos
				"columns"=>$cols,
			);
			
			if(!isset($report["restrict"])){
				$report["restrict"]='1=1';
			}


			#FETCH FIELD INFO FROM REPOSITORY
			$m = $main->load_file($report['table']);


			$conditions=array($report["restrict"]);
			foreach($stack_dimension_fields_array as $df){
				if($df!=$dimension_field){

					//almost always the same as the field name, except when the field is a_id, then
					//lookup changes from a_id to a.name

					$lookup_field_name = $df;
					if(isset($m->fields[$df])){
					
						if($m->field_is_foreign($m->fields[$df])){
							$d3 = $m->field_deport($m->fields[$df]);
							$lookup_field_name = $d3["foreign"];
						}
					}else{
						$m->error("Field NOT FOUND:".$df);

					}
					$conditions[]=''.$lookup_field_name." = '[%".$df."%]'";
				}
			}
			$s2 = $stack_dimension_fields_array;
			unset($s2[count($s2)-1]);//remove last element
			#echo("<hr>");
			#print_r($imagenes);

			foreach(array('stack_dimension_fields_array','all') as $v){
				foreach($$v as $f7){
					if($v=='all'){
						$real_field = str_replace(array("count(",'sum(',')'),'',strtolower($f7));
						$ff = $m->fields[$real_field];
						$ff["sql_expression"]=$f7;
						//backthicks are necessary for aliasing, since mysql won't take select 1 as sum(a) lightly
						$ff["sql_alias"]=$m->sql_quote($f7);
						$f4[$real_field]=$ff;

					}else{
						$ff = $m->fields[$f7];
						$ff["sql_alias"]=$f7;
						$f4[$f7]=$ff;

					}
				}
			}
			
			$where1 = implode(' AND ',$conditions);
			$q=$m->foreign_select(array('fields'=>$f4,'restrict'=>$where1,'get_fid'=>0));
			$query_sql = $q["sql"];
			$main->log(gp2($query_sql),'CHART');
			#OBSOLETE, foreign_select took over buddy.

			/*
			 * $query_sql = "SELECT 
				".implode(',',$stack_dimension_fields_array).",
					".implode(',',$all).
					" FROM ".$report['table'].
					" WHERE ".implode(' AND ',$conditions)
					;
			 */

			if(isset($report['grouped']) && $report['grouped']==0){
			
			}else{
				$query_sql.=" GROUP BY $dimension_field ";
			}
			#dbg echo($query_sql);
			#dbg echo("<hr/>");

			$rep->addReport(
				humanize($report['title']),$query_sql
					#won't work in the host's mysql 
					# . " ORDER BY ".$all[0]." DESC LIMIT $max_elements"
					,
					$imagenes,
					$tables,
					array( // Parametros permitidos en el BY_EXAMPLE
						"maybe",
					),
					$s2,// Parametros dinamicos que necesita
					$report['preserve'] // preserve
				);
			if(count($report['dimension_fields']) - 1== $x){
				//remove link
				unset($tables[count($tables)-1]['columns'][count($tables[count($tables)-1]['columns'])-1]);
				foreach($all as $k1=>$a){
					$new=str_replace(array('sum(',')'),'',$a);
					foreach($tables as $kt=>$table){
						foreach($table['columns'] as $ck=>$tc){
							if($tc['source']==$all[$k1]){
								$tables[$kt]['columns'][$ck]['source']=$new;
							}
						}
					}
					$all[$k1]=$new;
				}
			}
			$x++;
		}
		$rep->showReport();
	}

?>
