<?php
$std_loc = STD_LOCATION.'shared/';
?>
	<link rel="stylesheet" href="<?= $std_loc?>/lib/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?= $std_loc?>/lib/elitezebras-2-0.css" type="text/css" media="screen" />
<script src='<?= $std_loc?>/lib/elitezebras-2-0.js' type='text/javascript'></script>
<?php
	global $config;
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
		$field = str_replace('sum_','',$field);
		$field = str_replace('count_','',$field);
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
	require(dirname(__FILE__)."/report.php");


	if(file_exists("config/".($_SERVER['HTTP_HOST']).".config.php")){
		include("config/".($_SERVER['HTTP_HOST']).".config.php");
	}else{
		include("config/default.config.php");
	}
	
	/**
	 *
	 * report
	 * @param $report Estructura_Reporte
	 * report.title=Yopur Title Goes Here
	 * report.denormalize=attempts to convert abc_id into abc.name (requires STD)
	 * report.table=A Valid SQL Table Name
	 * report.grouped=[1|0] whether or not to use group by (use 0 on query400 breaklvl reports)
	 * report.filter=A Valid SQL WHERE CLAUSE (optional)
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
	 * report.data_fields.3.alias		=problemas con sql server no nos permiten usar count(*) debemos usar "count(*) as count___", gracias, sqlserver!
	 * report.data_fields.3.use_alias	=1 o 0
	 * report.data_fields.3.label=label
	 * report.data_fields.3.value=data_field_name
	 * report.data_fields.3.from=3
	 * report.data_fields.3.until=6
	 * report.data_fields.3.graph=[1|0]	whether or not to create a graph out of this particular field (for cartera.pizano)
	 * */
	function show_report($report){
		global $config,$labels;
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
		$aliases=array();
		$reverse_aliases=array();
		foreach($report['data_fields'] as $k1=>$a){
			if(is_array($a)){
				if(isset($a["use_alias"]) && $a['use_alias']==1){
					$aliases[$a["value"]] = $a["alias"];
					$reverse_aliases[$a["alias"]] = $a['value'];
				}
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
			$sql_aliased_field_list=array();

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

				//BUGFIX abril 2010
				$data_field_source=$data_field;
				if(isset($aliases[$data_field])){
					//se usa un alias
					$data_field_source = $aliases[$data_field];
					$sql_aliased_field_list[$data_field]=$data_field.' as '.$data_field_source;//
				}else{
					$sql_aliased_field_list[$data_field]=$data_field;
				}
				//END 

				$a1=array(
					"source"=>$data_field_source,
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
					"values_variable" => $data_field_source,//$data_field sqlserver
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
			
			if(!isset($report["filter"])){
				$report["filter"]='1=1';
			}
			$conditions=array($report["filter"]);
			foreach($stack_dimension_fields_array as $df){
				if($df!=$dimension_field){
					//if the field requests `, we are fucked :(
					$conditions[]=$df." = '[%".$df."%]'";
				}
			}
			$s2 = $stack_dimension_fields_array;
			unset($s2[count($s2)-1]);//remove last element
			#echo("<hr>");
			#print_r($imagenes);

			$query_sql = "SELECT 
				".implode(',',$stack_dimension_fields_array).",
					".implode(',',$sql_aliased_field_list).
					" FROM ".$report['table'].
					" WHERE ".implode(' AND ',$conditions)
					;
			
			if(isset($report['grouped']) && $report['grouped']==0){
			
			}else{
				$query_sql.=" GROUP BY $dimension_field ";
			}
			echo("<!--ERROR LOG-->\n\n\n\n<!-- ".$query_sql."-->\n\n\n\n<!--ERROR LOG-->");
			#echo("<hr/>");

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
					$new=str_replace(array('sum(',')','count('),'',$a);	//para que es esto???
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

		echo("<a name=\"start\"> </a>");
		$rep->showReport();
		echo "<!--".round((array_sum(explode(" ",microtime())) - $startTime),4).' sec'."-->";
		echo("<script>;location.href='#start';</script>");
		#echo("<pre>");
		#print_r($_GET);
		#echo("</pre>");
	}

?>
