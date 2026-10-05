<template>
    <div>
        <filters
            :filters="filters"
            :entities="entities"
            :list="list"
            :contentHistoryTypes="contentHistoryTypes"
            classFilter="DiffusionHistoryFilters"
            @filter="($event) => filteredList = $event">
        </filters>
        <pagination-list :list="sortedList" format="table" :perPage="10" :startPage="1" :nbPage="6" :nbResultDisplay="false">
            <template #content="{ list }">
                <table>
                    <thead>
                        <tr>
                            <th role="button" @click="sortTable('diffusion.name')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_name') }}
								<i :class="sortColumn === 'diffusion.name' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
                            </th>
                            <th role="button" @click="sortTable('date')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_date') }}
								<i :class="sortColumn === 'date' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
                            </th>
                            <th role="button" @click="sortTable('totalRecipients')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_recipients') }}
								<i :class="sortColumn === 'totalRecipients' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
                            </th>
                            <th :colspan="maxStats">{{ messages.get('dsi', 'statistique') }}</th>
                            <th class="dsi-table-right"><span>{{ messages.get('dsi', 'actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(history, index) in list" :key="index">
                            <td>
                                <a role="button" href="#" @click.prevent="show(history.id)">
                                    {{ history.diffusion.name }}
                                </a>
                            </td>
                            <td>{{ history.date }}</td>
                            <td>{{ history.totalRecipients }}</td>
                            <td v-for="(stat, index) in getStatsList(history)" :key="index">
                                {{ stat }}
                            </td>
                            <td class="dsi-table-right">
                                <report
                                    :history="history"
                                    :contentHistoryTypes="contentHistoryTypes">
                                </report>
                                <button type="button" class="bouton" @click="preview(history.id)">
                                    {{ messages.get('dsi', 'preview') }}
                                </button>
                                <button role="link" type="button" class="bouton" @click="linkDiffusion(history.numDiffusion)">
                                    {{ messages.get('dsi', 'edite') }}
                                </button>
                                <button type="button" class="bouton btnDelete" @click="del(history.id)">
                                    {{ messages.get('dsi', 'del') }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </pagination-list>
    </div>
</template>

<script>
import paginationList from '../../components/paginationList.vue';
import Filters from '../../filters/components/filters.vue';
import Report from './report.vue';

export default {
    name: "list",
    components: {
        paginationList,
        Filters,
        Report
    },
    props: {
        list: {
            type: Array,
            default: () => { return []; }
        },
        contentHistoryTypes: {
            type: Object,
            default: () => { return {}; }
        },
        filters: {
            type: Array,
            default: () => { return []; }
        },
        entities: {
            type: Array,
            default: () => { return []; }
        }
    },
    data: function() {
        return {
            sortColumn: "",
            sortDirection: "asc",
            filteredList : [],
            maxStats: 3
        }
    },
    computed: {
        sortedList() {
            return this.filteredList.sort((a, b) => {
                const direction = this.sortDirection === 'asc' ? 1 : -1;
                const valueA = this.getPropFromPath(this.sortColumn, a);
                const valueB = this.getPropFromPath(this.sortColumn, b);

                if (valueA < valueB) return -1 * direction;
                if (valueA > valueB) return 1 * direction;

                return 0;
            });
        },
        carretClass: function() {
            if (this.sortDirection) {
                return 'fa fa-sort-' + this.sortDirection;
            }
            return 'fa fa-sort';
        },
        defaultStats: function () {
            return new Array(this.maxStats).fill('--');
        }
    },
    created: async function() {
        this.filteredList = this.list;
    },
    methods: {
        getStats: function(history) {
            if (
                history.contentHistory[this.contentHistoryTypes['channel']] &&
                history.contentHistory[this.contentHistoryTypes['channel']][0]
            ) {
                const contentHistory = history.contentHistory[this.contentHistoryTypes['channel']][0];
                return contentHistory.content.settings.stats || null;
            }
            return null;
        },
        getStatsList: function(history) {
            const contentHistoryStats = this.getStats(history);
            let statsList = contentHistoryStats ? contentHistoryStats.stats : this.defaultStats;

            statsList = Object.values(statsList);
            statsList = [...statsList, ...this.defaultStats];
            statsList = statsList.splice(0, this.maxStats);

            return statsList;
        },
        getPropFromPath: function(path, object) {
            if (typeof path == "string") {
                path = path.split('.')
            }
            const key = path[0];
            path.splice(0, 1);

            if (path.length >= 1) {
                return this.getPropFromPath(path, object[key]);
            }
            return object[key];
        },
        sortTable(column) {
            if (column === this.sortColumn) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
        },
        linkDiffusion: function (diffusionId) {
            window.location = `./dsi.php?categ=diffusions&action=edit&id=${diffusionId}`;
        },
        preview: function (historyID) {
            this.$root.preview(historyID);
        },
        show : function(historyID) {
            this.$root.detail(historyID);
        },
        del: async function (id) {
            if (confirm(this.messages.get('dsi', 'confirm_del'))) {
				let response = await this.ws.post("DiffusionsHistory", 'delete', { id });
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
                    const index = this.list.findIndex((history) => {
                        return history.id == id
                    });
                    if (0 <= index) {
                        this.list.splice(index, 1);
                    }
                }
			}
        }
    }
}
</script>