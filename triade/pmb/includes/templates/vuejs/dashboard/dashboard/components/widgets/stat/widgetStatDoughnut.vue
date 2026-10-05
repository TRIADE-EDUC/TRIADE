<template>

    <Doughnut
        :width="width"
        :height="height"
        :chart-data="chartData"
        :chart-options="chartOptions" />

</template>

<script>

    import { Doughnut } from 'vue-chartjs/legacy'
    import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale} from 'chart.js'
    ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale)

    export default {
        name: 'DoughnutChart',
        props: ["datasets", "widget"],
        components: { Doughnut },
        data: function () {
            return {
                width: 50,
                height: 50,
                chartData: {
                    labels: [],
                    datasets: []
                },
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        title: {
                            display: false,
                            text: ''
                        },
                        tooltip: {
                            enabled: true
                        },
                        legend: {
                            display: false
                        }
                    }
                },
            }
        },
        mounted: function() {
            this.chartData.labels = this.datasets[0]["labels"];
            for (var i = 0; i < this.datasets.length; i++) {
                if(Array.isArray(this.datasets[i])) {
                    continue;
                }

                let backgroundColors = [];
                let borderColors = [];
                
                for(let label of this.datasets[0]["labels"]) {
                    backgroundColors.push(this.Const.main.backgroundColors[
                        this.helper.getColorIndex(label, this.Const.main.backgroundColors)
                    ]);
                    borderColors.push(this.helper.darkenColor(this.Const.main.backgroundColors[
                        this.helper.getColorIndex(label, this.Const.main.backgroundColors)
                    ], 0.1));
                }

                this.chartData.datasets.push({
                    label: this.datasets[i]["dataset_label"][0],
                    data: this.datasets[i]["dataset"],
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 2
                });
            }

            this.$set(this.chartOptions.plugins, "title", this.widget.dashboardWidgetSettings.title);
            this.$set(this.chartOptions.plugins, "legend", this.widget.dashboardWidgetSettings.legend);
            this.$set(this.chartOptions.plugins, "tooltip", this.widget.dashboardWidgetSettings.tooltip);
        }
    }
</script>