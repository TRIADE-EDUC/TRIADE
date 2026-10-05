<template>

    <Bar
        :width="width"
        :height="height"
        :chart-data="chartData"
        :chart-options="chartOptions"/>

</template>

<script>
    import { Bar } from 'vue-chartjs/legacy'
    import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'
    ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

    export default {
        name: "BarChart",
        props: ["datasets", "widget"],
        components: { Bar },
        data: function () {
            return {
                height: 100,
                width: 200,
                chartData: {
                    labels: [],
                    datasets: []
                },
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'x',
                    scales: {
                        x: {
                            title: {
                                text: "",
                                color: "black",
                                display: false,
                            },
                            display: true,
                            beginAtZero: false,
                            reverse: false
                        },
                        y: {
                            title: {
                                text: "",
                                color: "black",
                                display: false,
                            },
                            display: true,
                            beginAtZero: true,
                            reverse: false
                        }
                    },
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
                }
            }
        },
        mounted: function() {
            this.chartData.labels = this.datasets[0]["labels"];
            for (var i = 0; i < this.datasets.length; i++) {

                if(Array.isArray(this.datasets[i])) {
                    continue;
                }
                this.chartData.datasets.push({
                    label: this.datasets[i]["dataset_label"][0],
                    data: this.datasets[i]["dataset"],
                    backgroundColor: this.Const.main.backgroundColors[
                        this.helper.getColorIndex(this.datasets[0]["labels"][i], this.Const.main.backgroundColors)
                    ],
                    borderColor: this.helper.darkenColor(this.Const.main.backgroundColors[
                        this.helper.getColorIndex(this.datasets[0]["labels"][i], this.Const.main.backgroundColors)
                    ], 0.1),
                    borderWidth: 2
                });
            }

            this.$set(this.chartOptions.plugins, "title", this.widget.dashboardWidgetSettings.title);
            this.$set(this.chartOptions.plugins, "legend", this.widget.dashboardWidgetSettings.legend);
            this.$set(this.chartOptions.plugins, "tooltip", this.widget.dashboardWidgetSettings.tooltip);
        }
    }

</script>