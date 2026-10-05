<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_charting.tpl.php,v 1.5 2019/05/27 10:48:09 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $campaign_charting_commons, $campaign_charting_axis;

$campaign_charting_commons = "
<div id='!!nodeId!!'></div>
<div id='!!legendNodeId!!'></div>
<script type='text/javascript'>
	require([
		'dojox/charting/Chart',
		'dojox/charting/plot2d/!!chartType!!',
		'dojox/charting/widget/Legend',
		'dojox/charting/action2d/Tooltip',
		'dojox/charting/action2d/MoveSlice',
		'dojox/charting/themes/Claro',
		'dojox/charting/axis2d/Default'
	], function(Chart, ChartType, Legend, Tooltip, MoveSlice, Claro) {
		// Define the data
		var seriesData = !!seriesData!!;

    	var chart = new Chart('!!nodeId!!', {title : '!!title!!'});
		
		// Set the theme
		chart.setTheme(Claro);

	    chart.addPlot('default', {
	        type: ChartType,
			gap: 5,
			maxBarSize: 50
	    });

		!!chartAxis!!
		
		seriesData.forEach(function(serieData) {
			chart.addSeries(serieData.label,serieData.values,serieData.styles);
		});
		
		new Tooltip(chart,'default');
		new MoveSlice(chart,'default');

	    chart.render();
		
		var legend = new Legend({ chart: chart }, '!!legendNodeId!!');
});
</script>
";

$campaign_charting_axis = "
	    chart.addAxis('x', !!xAxis!!);
		chart.addAxis('y', !!yAxis!!);
";