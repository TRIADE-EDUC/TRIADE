<?php


function graph_pie($container, $data)
{
echo "
		<script type='text/javascript'>
		$(document).ready(function() {
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: '" . $container . "'
				},
				title: {
					text: ''
				},
				plotArea: {
					shadow: null,
					borderWidth: null,
					backgroundColor: null
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.point.name +'</b> : '+ this.y +' %';
					}
				},
				plotOptions: {
					pie: {
            allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							formatter: function() {
								if (this.y > 5) return this.point.name;
							},
							color: '#000000',
							connectorColor: '#000000',
							style: {
								font: '13px Trebuchet MS, Verdana, sans-serif'
							}
						}
					}
				},
				legend: {enabled  : false,
					layout: 'vertical',
					style: {
						left: 'auto',
						bottom: 'auto',
						right: 'auto',
						top: 'auto'
					}
				},
				credits: {enabled : false
				},
			        series: [{
					type: 'pie',
					name: 'Graph',
					data: " . $data . "
				}]
			});
			
			
		});
		</script>

		";
}


function graph_area($container, $data, $title, $clic_drag_to_zoom)
{
  echo "

		<script type='text/javascript'>
		$(document).ready(function() {
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: '" . $container . "',
					zoomType: 'x',
					marginBottom: 25,
					marginRight: 25,
					marginLeft: 50
				},
			        title: {
					text: '" . $title . "'
				},
			        subtitle: {
					text: '" . $clic_drag_to_zoom . "'
				},
				xAxis: {
					type: 'datetime',
					maxZoom: 14 * 24 * 3600000, // fourteen days
					title: {
						text: null
					}
				},
				yAxis: {
					title: {
						text: ''
					},
					min: 0,
					showFirstLabel: false
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ (this.point.name || this.series.name) +'</b><br/>'+
							Highcharts.dateFormat('%A %B %e %Y', this.x) + ' : ' + this.y;
					}
				},
				legend: {
					enabled: false
				},
				credits: {
          enabled : false
				},
				plotOptions: {
					area: {
						fillColor: {
							linearGradient: [0, 0, 0, 300],
							stops: [
								[0, '#4572A7'],
								[1, 'rgba(0,0,0,0)']
							]
						},
						lineWidth: 1,
						marker: {
							enabled: false,
							states: {
								hover: {
									enabled: true
								}
							}
						},
						shadow: false,
						states: {
							hover: {
								lineWidth: 1						
							}
						}
					}
				},
			
				series: [{
					type: 'area',
					name: '" . $title . "',
					data: " . $data . "
				}]
			});
			
			
		});
		</script>

  ";
}


function graph_column($container, $periodes, $data, $title, $title_2, $clic_drag_to_zoom)
{
  echo "
  
		<script type='text/javascript'>
		$(document).ready(function() {
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: '" . $container . "',
					defaultSeriesType: 'column',
					zoomType: 'x'
				},
				title: {
					text: '" . $title . " - " . $title_2 . "'
				},
				subtitle: {
					text: '" . $clic_drag_to_zoom . "'
				},
				xAxis: {
					categories: " . $periodes . " ,
          labels: {
						rotation: -45,
						align: 'right',
					},
				},
				yAxis: {
					min: 0,
					title: {
						text: ''
					}
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					backgroundColor: '#FFFFFF',
					style: {
						left: '100px',
						top: '70px',
						bottom: 'auto'
					}
				},
				credits: {
          enabled : false
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
							this.x +' : '+ this.y +'';
					}
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
			        series: [{
					name: '" . $title . "',
					data: " . $data . "
			
				}
			
				]
			});
			
			
		});
		</script>

    ";
}



function graph_column_basic($container, $data, $title, $legende)
{
  echo "


		<script type='text/javascript'>
		$(document).ready(function() {
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: '" . $container . "',
					zoomType: 'x',
					defaultSeriesType: 'column'
				},
				title: {
					text: '" . $title . "'
				},
				subtitle: {
					text: ''
				},
				xAxis: {
					categories: [" . $legende . "],
          labels: {
						rotation: -45,
						align: 'right',
					},
				}, 
            yAxis: {
              min: 0,
              title: {
                text: ''
              }
            },
				legend: {
					layout: 'vertical',
					style: {
						position: 'absolute',
						bottom: 'auto',
						left: '75px',
						top: '10px'
					},
					borderWidth: 1,
					backgroundColor: '#FFFFFF'
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
							this.x +': '+ this.y +' ';
					}
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
			        series: [ " . $data . "]
			});
			
			
		});
		</script>

    ";
}




function graph_column_basic_2_axes($container, $data, $title, $legende, $title_axe_1, $title_axe_2)
{
  echo "


		<script type='text/javascript'>
		$(document).ready(function() {
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: '" . $container . "',
					margin: [40, 70, 60, 90],
					zoomType: 'x',
					defaultSeriesType: 'column'
				},
				title: {
					text: '" . $title . "'
				},
				subtitle: {
					text: ''
				},
				xAxis: {
					categories: [" . $legende . "],
          labels: {
						rotation: -45,
						align: 'right',
					},
				}, 
            yAxis: [
            {
              labels: {
                formatter: function() {
                  return this.value +'';
                }
                },
              title: {
                text: '" . $title_axe_1 . "',
              style: {
                color: '#000000'
                },
              },
              margin: 50,
              min: 0,
            },
            {
              labels: {
                formatter: function() {
                  return this.value +'';
                },
              style: {
                color: '#89A54E'
                }
              },
              title: {
                text: '" . $title_axe_2 . "',
              style: {
                color: '#89A54E'
                }
              },
              opposite: true
            }
            ],

				legend: {
					layout: 'vertical',
					style: {
						position: 'absolute',
						bottom: 'auto',
						left: '90px',
						top: '10px'
					},
					borderWidth: 1,
					backgroundColor: '#FFFFFF'
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.series.name +'</b><br/>'+
							this.x +': '+ this.y +' ';
					}
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
			        series: [ " . $data . "]
			});
			
			
		});
		</script>

    ";
}



function graph_areaspline($container, $data, $title, $subtitle, $legende)
{
  echo "

		<script type='text/javascript'>
		$(document).ready(function() {
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: '" . $container . "',
					defaultSeriesType: 'areaspline'
				},
				title: {
					text: '" . $title . "'
				},
				subtitle: {
					text: '" . $subtitle . "'
				},
				legend: {
					layout: 'horizontal',
					style: {
						position: 'absolute',
						bottom: 'auto'
					},
					borderWidth: 1,
					backgroundColor: '#FFFFFF'
				},
				xAxis: {
					categories: [ " . $legende . "
					],
					plotBands: [{ // visualize the weekend
						from: 4.5,
						to: 6.5,
						color: 'rgba(68, 170, 213, .2)'
					}]
				},
				yAxis: {
					title: {
						text: ''
					}
				},
				tooltip: {
					formatter: function() {
			                return '<b>'+ this.series.name +'</b><br/>'+
							this.x +': '+ this.y + '';
					}
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					areaspline: {
						fillOpacity: 0.5
					}
				},
				series: [ " . $data . "]
			});
			

	});
		</script>

    ";
}

  
?>