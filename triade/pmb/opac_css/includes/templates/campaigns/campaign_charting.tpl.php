<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaign_charting.tpl.php,v 1.3 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $campaign_charting_commons, $campaign_charting_axis;

$campaign_charting_commons = "
<div id='!!nodeId!!'></div>
<div id='!!legendNodeId!!'></div>
<script>
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
	        markers: true,
			gap: 5
	    });

		!!chartAxis!!
		
		seriesData.forEach(function(serieData) {
			chart.addSeries(serieData.label,serieData.values);
		});
		
		new Tooltip(chart,'default');
		new MoveSlice(chart,'default');

	    chart.render();
		
 		if(('!!chartType!!' == 'Pie') || (seriesData.length > 1)) {
			var legend = new Legend({ chart: chart }, '!!legendNodeId!!');
 		}
});
</script>
";

$campaign_charting_axis = "
	    chart.addAxis('x', {labels: !!xAxisLabels!!, microTickStep: 1, majorTickStep: 1, majorLabels: true, minorTicks: false});
	    chart.addAxis('y', { min: !!min!!, vertical: true, fixLower: 'major', fixUpper: 'major', majorLabels: true, minorTicks: false });
";
