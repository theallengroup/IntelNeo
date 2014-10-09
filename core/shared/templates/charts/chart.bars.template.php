<?= std::get_jsc('chart');?>


<!--CHART:START-->

<div id="<?=$t->name ?>" align="center">[Chart Loading ... ]</div>
<script type="text/javascript">
var myChart = new FusionCharts("<?=STD_LOCATION ?>shared/templates/charts/<?=$t->get_swf_file()?>.swf", "<?=$t->name ?>", "<?=$t->width ?>", "<?=$t->height ?>");
myChart.setDataXML("<?=$t->get_xml() ?>");
myChart.render("<?=$t->name ?>");
</script>
<!--CHART:END-->
