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
	use3DLighting='1'
	> 
	<categories>		
	<? foreach($t->get_headers() as $header): ?>
		<category name="<?=$header?>"/>
	<? endforeach; ?>
	</categories>
   <!--
 <trendlines>
   <line startValue='26000' color='91C728' displayValue='Target' showOnTop='1'/>
 </trendlines>
dataset.showValues="1"
 -->

	<? foreach($t->data as $data_row): ?>
		<dataset seriesname="<?=$data_row['name']?>" color="<?=$data_row['color']?>" >
		<? foreach($data_row['value'] as $data_cell): ?>
			<set value="<?=$data_cell?>"/>
		<? endforeach; ?>
		</dataset>
	<? endforeach; ?>
</graph>
