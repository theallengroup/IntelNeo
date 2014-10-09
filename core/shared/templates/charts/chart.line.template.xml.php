<chart
	caption='<?= $t->title ?>' 
	subcaption=' ' 
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
	alternateHGridAlpha='20' 

	yAxisMinValue='0'  
	divLineColor='FCB541' 
	divLineAlpha='50' 
	canvasBorderColor='666666' 
	baseFontColor='666666' 
	lineColor='FCB541'

	> 


<? foreach($t->data as $d): ?>
	<set label='<?=$d["name"] ?>' value='<?=$d["value"] ?>' />
<? endforeach; ?>
 
	<styles> 
		<definition> 
			<style name='Anim1' type='animation' param='_xscale' start='0' duration='1' /> 
			<style name='Anim2' type='animation' param='_alpha' start='0' duration='0.6' /> 
			<style name='DataShadow' type='Shadow' alpha='40'/> 
		</definition> 
		<application> 
			<apply toObject='DIVLINES' styles='Anim1' /> 
			<apply toObject='HGRID' styles='Anim2' /> 
			<apply toObject='DATALABELS' styles='DataShadow,Anim2' /> 
		</application>	
	</styles> 
 
</chart> 
