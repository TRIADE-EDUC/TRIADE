<template>
    <div class="widget-stat-table height-100">
        <div class="widget-stat-table-container height-100"> 
            <div v-if="chartOptions.plugins.title.display" class="widget-stat-table-caption" aria-hidden="true">{{ chartOptions.plugins.title.text }}</div>
            <table v-if="graphEnabled" >
                <caption v-if="chartOptions.plugins.title.display" class="visually-hidden">{{ chartOptions.plugins.title.text }}</caption>
                <thead>
                    <tr>
                        <th class="widget-stat-table-label"></th>
                        <th v-for="label, index in chartData.labels" :key='index' class="widget-stat-table-label">{{label}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row, index in chartData.datasets" :key="index">
                        <td class="widget-stat-table-label">{{ row.label }}</td>
                        <td v-for="td, index in row.data" :key='index'>{{ td }}</td>
                    </tr>
                </tbody>
            </table>

            <table v-else >
                <caption v-if="chartOptions.plugins.title.display" class="visually-hidden">{{ chartOptions.plugins.title.text }}</caption>
                <thead>
                    <tr>
                        <th v-for="label, index in chartData.labels" :key='index' class="widget-stat-table-label">{{label}}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="dataset in chartData.datasets">
                        <tr v-for="row, rowIndex in dataset.data" :key='rowIndex'>
                            <td v-for="column, colIndex in row" :key='colIndex'>
                                {{ column }}
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>


<script>

    export default {
        name: 'TableChart',
        props: ["datasets", "widget"],

        data: function () {
            return {
                graphEnabled: 0,
                chartData: {
                    labels: [],
                    datasets: []
                },
                chartOptions: {
                    plugins: {
                        title: {
                            display: false,
                            text: ''
                        },
                    }
                }
            }
        },
        mounted: function() {
            this.chartData.labels = this.datasets[0]["labels"];
            this.graphEnabled = this.datasets[0]["graphEnabled"];

            for (var i = 0; i < this.datasets.length; i++) {

                if(Array.isArray(this.datasets[i])) {
                    continue;
                }

                this.chartData.datasets.push({
                    label: this.datasets[i]["dataset_label"][0],
                    data: this.datasets[i]["dataset"],
                    
                });
            }
            this.$set(this.chartOptions.plugins, "title", this.widget.dashboardWidgetSettings.title);
        }       
    }


</script>