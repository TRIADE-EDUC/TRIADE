<template>
    <div>
        <filters :filters="filters" :entities="entities" :list="list" :contentHistoryTypes="contentHistoryTypes"
            classFilter="DiffusionPendingFilters" @filter="($event) => filteredList = $event">
        </filters>
        <pagination-list :list="sortedList" format="table" :perPage="10" :startPage="1" :nbPage="6"
            :nbResultDisplay="false">
            <template #content="{ list }">
                <table>
                    <thead>
                        <tr>
                            <th role="button" @click="sortTable('diffusion.name')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_name') }}
                                <i :class="sortColumn === 'diffusion.name' ? carretClass : 'fa fa-sort'"
                                    aria-hidden="true"></i>
                            </th>
                            <th role="button" @click="sortTable('date')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_date') }}
                                <i :class="sortColumn === 'date' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
                            </th>
                            <th role="button" @click="sortTable('totalRecipients')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_recipients') }}
                                <i :class="sortColumn === 'totalRecipients' ? carretClass : 'fa fa-sort'"
                                    aria-hidden="true"></i>
                            </th>
                            <!-- <th>
                                {{ messages.get('dsi', 'diffusion_pending_modify') }}
                                <i class="fa fa-info-circle" aria-hidden="true"
                                    :title="messages.get('dsi', 'diffusion_edit_desc')">
                                </i>
                            </th> -->
                            <th role="button" @click="sortTable('state')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_pending_status') }}
                                <i :class="sortColumn === 'state' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
                            </th>
                            <th class="dsi-table-right">{{ messages.get('dsi', 'actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(history, index) in list" :key="index">
                            <td>
                                <span v-if="history.state == 0">{{ history.diffusion.name }}</span>
                                <a v-else href="#" @click="edit(history.id)" style="cursor: pointer;">
                                    {{ history.diffusion.name }}
                                </a>
                            </td>
                            <td>{{ history.formatedDate }}</td>
                            <td v-if="history.state === 0">{{ messages.get('dsi', 'diffusion_pending_no_init') }}</td>
                            <td v-else>{{ history.totalRecipients }}</td>
                            <!-- <td>
                                <i class="fa fa-eye"
                                    :title="messages.get('dsi', 'dsi_views')"
                                    :style="getStyleModified(isModified(index, 3))" aria-hidden="true">
                                </i>
                                <i class="fa fa-database"
                                    :title="messages.get('dsi', 'dsi_items')"
                                    :style="getStyleModified(isModified(index, 2))" aria-hidden="true">
                                </i>
                                <i class="fa fa-address-book-o"
                                    :title="messages.get('dsi', 'diffusion_recipients')"
                                    :style="getStyleModified(isModified(index, 1))" aria-hidden="true">
                                </i>
                            </td> -->
                            <td>
                                <span :style="'color: ' + validateStatus[history.state].color">
                                    {{ messages.get('dsi', validateStatus[history.state].label) }}
                                </span>
                            </td>
                            <td v-if="dataInProgress.list" class="dsi-table-right">
                                <template v-for="action in validateStatus[history.state].actions">
                                    <button type="button" :class="actions[action].class"
                                        :style="'background: ' + actions[action].color"
                                        @click="sendAction(actions[action].eventState, history)"
                                        :title="getTitleButtons(action, history.id)">

                                        {{ getLabelButtons(action, history.id) }}
                                    </button>
                                    <!-- Hack pour avoir un espace entre les tabs -->
                                    {{ &#32; }}
                                </template>
                                <button class="bouton btnDelete" @click="deleteHistoryPending(history)">
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

export default {
    name: "list",
    props: {
        list: {
            type: Array,
            default: () => { return []; }
        },
        filters: {
            type: Array,
            default: () => { return []; }
        },
        entities: {
            type: Array,
            default: () => { return []; }
        },
        contentHistoryTypes: {
            type: Object,
            default: () => { return {}; }
        }
    },
    components: {
        Filters,
        paginationList
    },
    data: function () {
        return {
            validateStatus: {
                0: {
                    label: "diffusion_pending_to_init",
                    color: "#39f", //BLUE
                    actions: ["init"]
                },
                1: {
                    label: "diffusion_pending_to_validate",
                    color: "#fe3a3a", //RED
                    actions: ["validate", "reset"]
                },
                2: {
                    label: "diffusion_pending_validate",
                    color: "#39f", //BLUE
                    actions: ["send", "to_validate"]
                }
            },
            actions: {
                "send": {
                    label: "diffusion_pending_action_send",
                    color: "#00c400", //GREEN
                    eventState: 3,
                    class: "bouton"
                },
                "to_validate": {
                    label: "diffusion_pending_action_to_validate",
                    color: "#fe3a3a", //RED
                    eventState: 1,
                    class: "bouton"
                },
                "validate": {
                    label: "diffusion_pending_action_validate",
                    color: "#00c400", //GREEN
                    eventState: 2,
                    class: "bouton"
                },
                "reset": {
                    label: "diffusion_pending_action_reset",
                    color: "", //RED
                    eventState: 4,
                    class: "bouton btnDelete"
                },
                "init": {
                    label: "diffusion_pending_action_init",
                    color: "#00c400", //GREEN
                    eventState: 1,
                    class: "bouton"
                },
            },
            sortColumn: "",
            sortDirection: "asc",
            filteredList: [],
            dataInProgress: [],
            sendInProgress: []
        }
    },
    created: async function () {
        this.filteredList = this.list;
        await this.fetchDataInProgressAllDiffusions();
    },
    computed: {
        sortedList: function () {
            return this.filteredList.sort((a, b) => {
                const direction = this.sortDirection === 'asc' ? 1 : -1;
                const valueA = this.getPropFromPath(this.sortColumn, a);
                const valueB = this.getPropFromPath(this.sortColumn, b);

                if (valueA < valueB) return -1 * direction;
                if (valueA > valueB) return 1 * direction;

                return 0;
            });
        },
        carretClass: function () {
            if (this.sortDirection) {
                return 'fa fa-sort-' + this.sortDirection;
            }
            return 'fa fa-sort';
        },
    },
    methods: {
        sendAction: async function (state, history) {
            if (state == 4) {
                if (!confirm(this.messages.get('dsi', 'confirm_reset'))) {
                    return;
                }
            }

            if (state == 3) {
                await this.progressSend(history.id);
            }

            let response = await this.ws.post("DiffusionsPending", "updateHistoryState/" + state + "/" + history.id, {});
            if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
            } else {
                const index = this.list.findIndex(e => e.id == response.id);

                if (state == 3) {
                    this.sendInProgress.splice(this.sendInProgress.indexOf(history.id), 1);
                }

                if (response.state == 3) {
                    this.$root.historySent(history.id);
                    this.$delete(this.list, index);

                } else {
                    this.$set(this.list, index, response);
                    setTimeout(async () => {
                        await this.fetchDataInProgressDiffusion(history.id);
                    }, 200);
                }
            }
        },
        progressSend: async function (historyId) {
            this.sendInProgress.push(historyId);

            let options = {
                method: "POST",
                cache: 'no-cache',
            };

            let url = `DiffusionsPending/updateHistoryState/3/${historyId}`;
            const totalPackets = Math.ceil(this.dataInProgress.list[historyId].remainingElements / this.dataInProgress.nbPerPass);

            for (let i = 0; i < totalPackets - 1; i++) {
                let response = await fetch(this.$root.url_webservice + url, options);
                let result = await response.json();
                if (result.error) {
                    this.notif.error(this.messages.get('dsi', result.errorMessage));
                } else {
                    const index = this.list.findIndex(e => e.id == result.id);
                    this.$set(this.list, index, result);
                    this.$set(this.dataInProgress.list[historyId], "remainingElements", this.dataInProgress.list[historyId].remainingElements - this.dataInProgress.nbPerPass);

                    await new Promise(r => setTimeout(r, 100));
                }
            }
        },
        edit: function (historyId) {
            this.$root.edit(historyId);
        },
        getPropFromPath: function (path, object) {
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
        deleteHistoryPending: async function (history) {
            if (confirm(this.messages.get('dsi', 'confirm_del'))) {
                let response = await this.ws.post("DiffusionsHistory", 'delete', { id: history.id });
                if (response.error) {
                    this.notif.error(this.messages.get('dsi', response.errorMessage));
                } else {
                    let i = this.list.findIndex((h) => h.id == history.id);
                    this.$delete(this.list, i);
                }
            }
        },
        fetchDataInProgressDiffusion: async function (idHistory) {
            this.dataInProgress["list"][idHistory] = await this.ws.get('diffusionsPending', `getDataInProgressDiffusion/${idHistory}`);
        },
        fetchDataInProgressAllDiffusions: async function () {
            this.dataInProgress = await this.ws.get('diffusionsPending', 'getDataInProgressAllDiffusions');
        },
        getLabelButtons(action, historyId) {
            if (action == "send") {
                if (this.dataInProgress.list[historyId] && this.dataInProgress.list[historyId].inProgress) {
                    return this.messages.get('dsi', "dsi_send_diffusion_manually_in_progress_pending").replace('%s', this.getProgressPercentage(historyId));
                }

                if (this.sendInProgress.includes(historyId)) {
                    return this.messages.get('dsi', "dsi_send_diffusion_manually_in_progress").replace('%s', this.getProgressPercentage(historyId));
                }

                return this.messages.get('dsi', "diffusion_pending_action_send");
            }

            return this.messages.get('dsi', this.actions[action].label);
        },
        getTitleButtons(action, historyId) {
            if (action == "send") {
                if (this.dataInProgress.list[historyId] && this.dataInProgress.list[historyId].inProgress) {
                    return this.messages.get('dsi', 'dsi_send_diffusion_manually_in_progress_title').replace('%s', this.getProgressPercentage(historyId));
                }
            }

            return "";
        },
        getProgressPercentage(historyId) {
            if (this.dataInProgress.list[historyId]) {
                return Math.round(((this.dataInProgress.list[historyId].totalElements - this.dataInProgress.list[historyId].remainingElements) / this.dataInProgress.list[historyId].totalElements) * 100);
            }
        }
    }
}
</script>