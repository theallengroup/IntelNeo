<?
	global $report_div_count;
/*
 *
 *  LA IDEA DE ESTA CLASE ES RECIBIR INFORMACION EN ARREGLOS Y PRODUCIR CIERTO TIPO DE IMAGENES
 *  SE DESEA CREAR EL CODIGO LO MAS LIMPIO POSIBLE, DE ESTA FORMA PODER MANTENERLO Y USARLO
 *  MAS ADELANTE. ESTA CLASE ESTA BASADA EN EL COMPONENTE FUSION CHARTS...
 *
 *  SANTIAGO VALENCIA CONVERS @ 2007
 *
*/
class cChartSNT
{
	// CONFIGURACION
	var $name;    // La grafica debe tener un nombre
	var $series;  // Cada serie es un conjunto de datos y un nombre, un pie solo puede tener una serie
	var $max_per_serie; // Hay un maximo de elementos permitidos para cada serie, no se puede graficar 1000 elementos...
	var $width; 	// La grafica tiene un ancho por defecto
	var $height;	// La grafica tiene un alto por defecto
	var $trends;	// Aca estaran las zonas y sus colores...
	var $xlabel;	// El label de la coordenada X
	var $xorientation='horizontal';	// orientacion del eje X (horizontal, vertical)
	var $ylabel;	// El label de la coordenada Y
	var $autosort;  // Si se ordena de mayor a menor...
	// VARIAS
	var $error; // Cada error tiene un codigo especifico...
	
	// El constructor... por defecto fijaremos estos valores
	function cChartSNT($name="",$max_per_serie=15,$width=600,$height=400,$xlabel,$ylabel,$autosort=true)
	{
		$this->name=$name;
		$this->max_per_serie=$max_per_serie;	
		$this->width=$width;
		$this->height=$height;		
		$this->xlabel=$xlabel;
		$this->ylabel=$ylabel;
		$this->autosort=$autosort;
		
	}
	// Esta funcion agregara una serie, cada serie es un tipo de informacion
	function addSerie($name,$data) 
	{
		if(count($data)>0)
		{
			if($this->autosort)
			{
				$value=array();
				foreach($data as $k=>$row)
				{
					$value[$k]=$row['value'];
				}
				array_multisort($value,SORT_DESC,$data);
			}
			$this->series[$name]=$data;
		}
		else
		{
			$error=1;		
		}
	}
	// Tendencias, Colores y Target
	function addTrendZone($name,$start,$end,$color) 
	{
		$this->trends[$name]=array("start"=>$start,"end"=>$end,"color"=>$color);		
	}	
	// Generar el XML de los trends
	function xmlTrends()
	{
		$xml="";
		if(count($this->trends)>0)
		{
			$xml ="<trendLines >";		
			foreach($this->trends as $k=>$v)
			{
				if(is_numeric($v["end"]))
				{
					$xml.="<line isTrendZone='1' startValue='".$v["start"]."' endValue='".$v["end"]."' color='".$v["color"]."' displayvalue='".$k."' valueOnRight ='1'/>";
				}
				else
				{
					$xml.="<line isTrendZone='1' startValue='".$v["start"]."' color='".$v["color"]."' displayvalue='".$k."' valueOnRight ='1'/>";
				}
				
			}
			$xml.="</trendLines>";
		}
		return $xml;
	}
	// Generamos el XML necesario, esta funcion es medio generica...
	function xmlSingleSerie($serie)
	{
		$xml="";
		if(!isset($this->series[$serie]))
		{
			foreach($this->series as $k=>$v)
			{
				$s=$v;
				break;
			}		
		}
		else
		{
			$s=$this->series[$serie];
		}
		if(count($s)>0)
		{
			foreach($s as $k=>$v){
				$xml.="<set label='".$v["label"]."' value='".$v["value"]."' link='".urlencode($v["link"])."'/>";
			}
		}
		return $xml;
	}
	// Generamos el XML necesario, de una categoria
	function xmlDataSerie($serie,$renderAs="COLUMN")	
	{
		$xml="<dataset seriesName='".$serie."' renderAs='".$renderAs."'>";
		$s=$this->series[$serie];
		if(count($s)>0)
		{
			foreach($s as $k=>$v){
				$xml.="<set value='".$v["value"]."' link='".urlencode($v["link"])."'/>";
			}
		}
		$xml.="</dataset>";
		return $xml;
	}	
	// Generamos el XML necesario, de una categoria
	function xmlCategorySerie($serie)
	{
		$xml="<categories>";
		$s=$this->series[$serie];
		if(count($s)>0)
		{
			foreach($s as $k=>$v){
				$xml.="<category label='".$v["label"]."'/>";
			}
		}
		$xml.="</categories>";
		return $xml;
	}
	function init(){
		return("<script language=\"JavaScript\" src=\"".MEDIA_DIR."rlib/charts.js\"></script>");
	}	
	// Codigo HTML de la grafica PIE
	function htmlPie($serie=null)
	{
		global $report_div_count;
	    $xml ="\n<div id=\"chartdiv\" align=\"center\"></div>\n";
	    $xml.="<script type=\"text/javascript\">";
	    $xml.="var myChart = new FusionCharts(\"".MEDIA_DIR."rlib/pie3D.swf\", \"myChartId".time()."\", \"".$this->width."\", \"".$this->height."\", \"0\", \"0\");";
        $xml.="myChart.setDataXML(\"";         
		$xml.="<chart caption='".$this->name."' subCaption='".$serie."' >";
		$xml.=$this->xmlSingleSerie($serie);
		$xml.="</chart>";
		$xml.="\");";   
		$xml.="myChart.render(\"chartdiv\");";
		$xml.="</script>";
		return $xml;

	}
	function htmlBar($series)
	{		
		
	    $xml ="\n<div id=\"chartdiv\" align=\"center\"></div>\n";
	    $xml.="<script type=\"text/javascript\">";
	    $xml.="var myChart = new FusionCharts(\"".MEDIA_DIR."rlib/bar3D.swf\", \"myChartId".time()."\", \"".$this->width."\", \"".$this->height."\", \"0\", \"0\");";
        $xml.="myChart.setDataXML(\"";         
		$xml.="<chart caption='".$this->name."' xAxisName='".$this->xlabel."' yAxisName='".$this->ylabel."' rotateNames='".(($this->xorientation=='horizontal')?0:1)."'>";				
		$xml.=$this->xmlCategorySerie($series[0]);
		$xml.=$this->xmlDataSerie($series[0]);
		if(($t=count($series))>1)
		{
			for($i=1;$i<$t;$i++)
			{
				$xml.=$this->xmlDataSerie($series[$i]);
			}
		}
		$xml.=$this->xmlTrends();
		$xml.="</chart>";
		$xml.="\");";   
		$xml.="myChart.render(\"chartdiv\");";
		$xml.="</script>";
		return $xml;

	}	
	function htmlLineAndBar($seriesLine,$seriesBar)
	{		
		
	    $xml ="<script language=\"JavaScript\" src=\"".MEDIA_DIR."rlib/charts.js\"></script>\n<div id=\"chartdiv\" align=\"center\"></div>\n";
	    $xml.="<script type=\"text/javascript\">";
	    $xml.="var myChart = new FusionCharts(\"".MEDIA_DIR."rlib/linebar3D.swf\", \"myChartId".time()."\", \"".$this->width."\", \"".$this->height."\", \"0\", \"0\");";
        $xml.="myChart.setDataXML(\"";         
		$xml.="<chart caption='".$this->name."' xAxisName='".$this->xlabel."' yAxisName='".$this->ylabel."'>";				
		$xml.=$this->xmlCategorySerie($seriesLine[0]);
		$xml.=$this->xmlDataSerie($seriesLine[0],"LINE");
		if(($t=count($seriesLine))>1)
		{
			for($i=1;$i<$t;$i++)
			{
				$xml.=$this->xmlDataSerie($seriesLine[$i],"LINE");
			}
		}
		if(($t=count($seriesBar))>0)
		{
			for($i=0;$i<$t;$i++)
			{
				$xml.=$this->xmlDataSerie($seriesBar[$i],"COLUMN");
			}
		}		
		$xml.=$this->xmlTrends();
		$xml.="</chart>";
		$xml.="\");";   
		$xml.="myChart.render(\"chartdiv\");";
		$xml.="</script>";
		return $xml;

	}	
}
?>
