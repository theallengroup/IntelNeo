<?php
/*
 *   este script  se  encarga  de  crear los reportes segun la configuracion que recibe
 * 	 la idea es que sea lo mas general posible, que permita la creacion de los reportes
 *   desde cualquier script en php, siendo independiente de cualquier interfaz...
 *
 *   santiago valencia convers @ 2008
 */
require(dirname(__FILE__)."/charts.php");

function secureSQL($insecure)
{
	return db::remove_strange_chars($insecure);
}
global $global_sf;
$global_sf=0;
function drill_down_cmp($a, $b){
	global $global_sf;
	if ($a[$global_sf] == $b[$global_sf]) {
		return 0;
	}
	return ($a[$global_sf] < $b[$global_sf]) ? 1 : -1;	//REVERSE SORT
}


function Return_Substrings($text, $sopener, $scloser){
		$result = array();
	       
		$noresult = substr_count($text, $sopener);
		$ncresult = substr_count($text, $scloser);
	       
		if ($noresult < $ncresult){
			$nresult = $noresult;
		}else{
			$nresult = $ncresult;
       		}
		unset($noresult);
		unset($ncresult);
	       
		for ($i=0;$i<$nresult;$i++){
			$pos = strpos($text, $sopener) + strlen($sopener);
			$text = substr($text, $pos, strlen($text));
			$pos = strpos($text, $scloser);
			$result[] = substr($text, 0, $pos);
			$text = substr($text, $pos + strlen($scloser), strlen($text));
		}
	return $result;
}
class cReportSingle{
	var $title; // TITULO DEL REPORTE
	var $images; // IMAGENES
	var $tables; // GRID's DE INFORMACION
	var $sql;   // SQL DE ESTE REPORTE
	var $by_example; // QUE VARIABLES SON PERMITIDAS PARA HACER QUERY BY EXAMPLE
	var $dynamic; // VARIABLES DINAMICAS
	var $where; //  VARIABLE QUE INDICA DONDE ESTOY PARADO
	var $preserve; // untouched link elements
	function cReportSingle($title="",$sql="",$images=array(),$tables=array(),$by_example=array(),$dynamic=array(),$preserve=array()){
		$this->title=$title;
		$this->images=$images;
		$this->tables=$tables;
		$this->sql=$sql;
		$this->by_example=$by_example;
		$this->dynamic=$dynamic;
		$this->where=$_SERVER['PHP_SELF'];
		$this->preserve=$preserve;
	}
	function showReport(){
		global $global_sf;

		$d=new db();
		$d->cnn($dbname,$dbhost,$dbuser,$dbpassword);
		
		global $dbhost,$dbuser,$dbpassword,$dbname,$level;
		#$conn=mysql_connect($dbhost,$dbuser,$dbpassword);
		#mysql_select_db($dbname);
		#if(mysql_error($conn))
		#{
		#	$this->error=100; // MYSQL ERROR
		#	die("unable to connect to DB:".$dbname);
		#}
		$base_link=$this->where."?l=".($level+1)."&";
		#print_r($this->dynamic);
		foreach($this->dynamic as $var)
		{
			$base_link.="b[".$var."]=".secureSQL($_GET[$var])."&";
		}
		$start_link=array();
		foreach($this->preserve as $p6){
			$start_link[]=$p6.'='.$_GET[$p6];
		}
		//show title
		echo "<p class='reportTitle'><a href='?".implode('&',$start_link)."'>".$this->title."</a></p>";
		//show navigation bar
		$d2= $this->dynamic;
		array_pop($d2);
		echo("<center><table border=0 cellpadding=2>");
		foreach($d2 as $var1){
			echo("<tr><td>".humanize($var1)."</td><td>".$_GET[$var1]."</td></tr>");
		}
		echo("</table></center>");
		
		// GENERACION DEL CODIGO SQL DINAMICO...
		$from_get=Return_Substrings($this->sql,"[%","%]"); // REMPLAZAMOS LAS VARIABLES QUE QUEREMOS DE GET
		foreach($from_get as $f)
		{
			$this->sql=str_replace("[%".$f."%]",secureSQL($_GET[$f]),$this->sql);
			$this->title=str_replace("[%".$f."%]",secureSQL($_GET[$f]),$this->title);
		}
		// REMPLAZAMOS EL QUERY BY EXAMPLE...
		$sql_example="1";
		$this->sql=str_replace("[EXAMPLE]",$sql_example,$this->sql);
		// SQLOBJ ES UN ARREGLO CON TODAS LAS COLUMNAS...
		$sqlobj=array();
		$i=1;
		#dataf3l p2($this->sql);

		#$q=mysql_query($this->sql) or die(mysql_error()."<br>".$this->sql);
		$q=$d->sql($this->sql) or die($d->real_error()."<br>".$this->sql);
		$c=new std();
		#$c->log_event($this->sql,"DRILLDOWN");

		$HARDCODED_LIMIT = 25;
		//$HARDCODED_LIMIT = 600;
		//&& $xx<$HARDCODED_LIMIT
		$xx=0;
		$current_rowset_needs_trimming=0;
		while(($row=$d->fetch()) ){			//mysql_fetch_array
			foreach($row as $k=>$v)
			{
				$sqlobj[$i][$k]=str_replace(array('&'),array(' '),$v);
			}
			$i++;
			$xx++;
			if($xx > $HARDCODED_LIMIT){
				$current_rowset_needs_trimming=1;
			}
		}

		#dataf3l 
		#common::e_table($sqlobj);
		$old = $sqlobj;

		// GENERACION DE LAS GRAFICAS DEL REPORTE...
		#echo("<hr>");
		#print_r($this->images);
		#echo("<hr>");
		$once=1;
		$is_shown=array();	
		$label_field=null;
		$na=null;
		
		$first_sort_variable='NULERROR';
		foreach($this->images as $image)
		{
			$label_field = $image['labels_variable'];//we assume these are all the same, we use only the last one, it seems

			$sqlobj = $old;
			$graph=new cChartSNT($image['title'],15,"100%",500,$image['xlabel'],$image['ylabel'],$image['autosort']);
			if($once){
				$first_sort_variable=$image['values_variable'];
					
				echo($graph->init());
				$once=0;
			}
			$graph->xorientation=$image['xorientation'];
			$serie=array();

			//correct "X as y" to just "y"
			#echo("<font color=red>".$image['values_variable']."</font>");

			if($current_rowset_needs_trimming == 1){
				//some dataset treatment before we show it to the public
				$na=array();
				
				foreach($sqlobj as $row){
					#misguded. $na[ str_pad($row[$first_sort_variable], 18, "0", STR_PAD_LEFT).$df1]=$row;
					$na[]=$row;

				}
				$global_sf=$first_sort_variable;
				usort($na,"drill_down_cmp");

				//misguded: reverse sort 	#krsort($na);	#p2($na);
				$sqlobj=array();
				$h=0;
				$others=0;
				$need_others=0;
				$min=null;
				
				foreach($na as $item){
					if($h<$HARDCODED_LIMIT){
						$sqlobj[]=$item;
						$is_shown[$item[$label_field]]=1;
					}
					$h++;
					if ($h==$HARDCODED_LIMIT){
						$need_others=1;
						$min = $item[$image["values_variable"]];
					}
					if($h>$HARDCODED_LIMIT){
						//this is where OTHERS begin
						$others+=$item[$image["values_variable"]];
					}
				}
				if($need_others==1){
					$sqlobj[]=array(
						'__others'=>1,
						$image["values_variable"]=>$others,
						$image['labels_variable']=>'Otros ('. humanize($first_sort_variable) .' menor a  '.number_format($min,0,",",".").')',
					);
				}
				#echo($others." ".$min."<hr>");
				#print_r($na);
				#print_r($sqlobj);
				#die();
			}
			//end

			foreach($sqlobj as $row)
			{
				$aux_link=$base_link;
				if(isset($image['dynamic_link']))
				{
					foreach($image['dynamic_link'] as $column)
					{
						$aux_link.=$column."=".$row[$column]."&";
					}
					foreach($this->preserve as $item)
					{
						$aux_link.=$item."=".$_GET[$item]."&";
					}
				}
				$l8 = array(
					"label"=>$row[$image['labels_variable']],
					"value"=>$row[$image['values_variable']],
					
				);
				if(isset($image['dynamic_link'])){
					$l8["link"] = $aux_link;
				}
				if(isset($row["__others"]) && $row["__others"]==1){
					#echo($l8["link"]);
					unset($l8["link"]);
				}
				$serie[]=$l8;
			}
			$graph->addSerie($image['seriename'],$serie);
			if(isset($image['trendzone']))
			{
				foreach($image['trendzone'] as $trend)
				{
					$graph->addTrendZone($trend['label'],$trend['start'],$trend['end'],$trend['color']);	
				}				
			}			
			switch($image['type'])
			{
				case "pie":
					echo $graph->htmlPie($image['seriename']);							
				break;
				case "bar":
					echo $graph->htmlBar(array($image['seriename']));
				break;
				default:
				       echo $graph->htmlBar(array($image['seriename']));
				break;
			}
		}
		//mostrar TODOS
		$sqlobj=$old;
		//mostrar todos ordenados en la tabla
		if($na!=null){
			$sqlobj=$na;
		}

		// GENERACION DE LAS TABLAS O GRIDS...
		if(isset($this->tables)){
			$s = $_GET['search'];
			//formulario de búsqueda.
			echo("<center>
				<form method=POST action='?".$_SERVER['QUERY_STRING']."' />
				<input type=text name=search value='".$_POST['search']."' />
				<input type=submit value=Buscar />
				</form>
				</center>");
			echo '<script type="text/javascript" src="../lib/tables.js"></script>';
			foreach($this->tables as $table)
			{
				echo "<p class='tableTitle'>".$table['name']."</p>";
				echo '<table id="'.$table['name'].'" class="sortable striped" align="center">';
				echo '	<thead>';
				echo '	  <tr>';
				echo '	  <td class="sortable">#</td>';
				foreach($table['columns'] as $column)
				{
					if($column['sortable'])
					{
						echo '<td class="sortable">'.$column['label'].'</td>';	  
					}
					else{
					       echo '<td>'.$column['label'].'</td>';	  
					}
				      
				}
				echo '	  </tr>';
				echo '  </thead>';
				echo '  <tbody>';
				#print_r($is_shown);
				#die();
				$cx=0;
				foreach($sqlobj as $row)
				{
					//Search
					if(isset($_POST) && isset($_POST['search']) && $_POST['search']!=''){
						$st= preg_quote($_POST["search"]);
						if(!preg_match("/.*".$st.".*/i",implode('|',$row))){
							continue;
							$cx++;
						}
					}
					$class='normal_tr';
					if(isset($is_shown[$row[$label_field]]) && $is_shown[$row[$label_field]]==1){
						$class='special_tr';
					}else{
					
					}
					echo "\n".'	  <tr class="'.$class.'">';

					echo "<td class='".$class."'>".(++$cx)."</td>";
					foreach($table['columns'] as $column){
						$display_text=$row[$column['source']];

						//highlight search results.
						if(isset($_POST) && isset($_POST['search']) && $_POST['search']!=''){
							$st = strtoupper(preg_quote($_POST['search']));
							$display_text = preg_replace("/(".$st.")/i","<b style='background-color:gray'>".$st."</b>",$row[$column['source']]);
						}

						echo '<td class="'.$class.'" ';
						if($column['type']=="numeric"){
							echo " align='right'";
						}
						echo '>';
						if(isset($column['dynamic_link'])){
							$link=$base_link;
							foreach($column['dynamic_link'] as $c){
								$link.=$c."=".$row[$c]."&";
							}
							foreach($this->preserve as $item){
								$link.=$item."=".$_GET[$item]."&";
							}
							echo '<a href="'.$link.'">'.$column['link_label'].'</a>';
						}else{
							echo $column['pre'];
							if($column['numberformat']){
								// ESTE ES EL FORMATO DE COLOMBIA
								echo number_format($row[$column['source']],$column['decimals'],",",".");
							}else{
								echo $display_text;
							}
						}
						echo '</td>';
					}
					echo '	  </tr>';
				}
				echo '  </tbody>';
				echo '</table>';
			}
		}
		#DO NOT CLOSE THE STUPID CONNECTION DUDE!
		#mysql_close($conn);
	}
}
class cReportSNT{
	// CONFIGURACION
	var $name;  // NOMBRE DEL ANALISIS	
	// VARIAS
	var $reports; // TODOS LOS REPORTES
	var $n; // NUMERO DE REPORTES
	var $error; // ERROR GENERADO
	// FUNCIONES
	function cReportSNT($name=""){
		$this->name=$name;
		$this->n=1;
	}
	//badly named.
	function configMySQL($host,$user,$password,$name){
		global $dbhost,$dbuser,$dbpassword,$dbname;
		$dbhost=$host;
		$dbuser=$user;
		$dbpassword=$password;
		$dbname=$name;
	}
	function addReport($title="",$sql="",$images=array(),$tables=array(),$by_example=array(),$dynamic=array(),$preserve=array()){
		$this->reports[$this->n]=new cReportSingle($title,$sql,$images,$tables,$by_example,$dynamic,$preserve);
		$this->n=$this->n+1;
	}
	function showReport(){
		global $level;
		$c=count($this->reports);
		if($c>0){
			if(!isset($_GET['l']) or !is_numeric($_GET['l']) or $_GET['l']<1){//or $_GET['l']>$c 
				$level=1;
			}else{
				$level=$_GET['l'];
			}
			if($_GET['l']>$c){
				echo("No hay más niveles de profundidad.");
				$this->drill_up_link();
			}else{
				$this->reports[$level]->showReport();
			}
			if($level>1){
				#$needed="";
				#if(isset($_GET['b']))
				#{
				#	foreach($_GET['b'] as $k=>$v){
				#		$needed.=$k."=".$v."&";
				#	}
				#}				
				#foreach($this->reports[$level]->preserve as $item){
				#	$needed.=$item."=".$_GET[$item]."&";
				#}
				$this->drill_up_link();
				#echo "<br><center><a class='drillUpLink' href='".$this->where."?l=".($level-1)."&".$needed."'>Drill Up</a></center><br>";
			}
		}		
	}
	function drill_up_link(){
		echo "<br><center><a class='drillUpLink' href='javascript:history.back();'>Drill Up</a></center><br>";
	}
}
?>
