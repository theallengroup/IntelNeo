<graph 
	caption='<?= $t->title ?>' 
	xAxisName='<?= $t->xtitle ?>' 
	yAxisName='<?= $t->Ytitle ?>' 
	showValues='0' 
	numberPrefix='' 
	decimalPrecision='0' 
	bgcolor='<?= $t->background_color ?>' 
	bgAlpha='70' 
	showColumnShadow='1'
	divlinecolor='<?= $t->divlinecolor ?>' 
	divLineAlpha='60' 
	rotateNames='1'
	showAlternateHGridColor='1' 
	alternateHGridColor='<?= $t->alternate_hgrid_color ?>'
	alternateHGridAlpha='60' 
	> 
<? foreach($t->data as $d): ?>
  <set name='<?=$d["name"] ?>' value='<?=$d["value"] ?>' color='<?=$d["color"] ?>'/>
<? endforeach; ?>

   <!--
 <trendlines>
   <line startValue='26000' color='91C728' displayValue='Target' showOnTop='1'/>
 </trendlines>
 -->

</graph>
