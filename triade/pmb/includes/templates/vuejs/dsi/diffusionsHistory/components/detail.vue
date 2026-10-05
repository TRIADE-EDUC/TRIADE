<template>
    <div>
        <h2 class="section-sub-title">{{ diffusion.name }}</h2>
        <div class="dsi-list">
            <p>{{ messages.get('dsi', 'diffusion_form_products') }}</p>
            <div v-if="diffusion.diffusionProducts" class="dsi-cards">
                <span v-if="diffusion.diffusionProducts.length == 0">{{ messages.get("dsi", "diffusion_form_empty_products") }}</span>
                <div class="dsi-card" v-for="(diffusionProduct, i) in diffusion.diffusionProducts" :key="i">
                    <a :href="$root.url_base + 'dsi.php?categ=products&action=edit&id=' + diffusionProduct.num_product">
                        <p>{{ getProductName(diffusionProduct.num_product) }}</p>
                    </a>
                </div>
            </div>
        </div>
        <filters
            :filters="filters"
            :entities="entities"
            :list="list"
            :contentHistoryTypes="contentHistoryTypes"
            classFilter="DiffusionHistoryFilters"
            @filter="($event) => filteredList = $event">
        </filters>
        <div class="row">
            <div class="right">
                <button type="button" class="bouton btnDelete" @click="del" :disabled="deleteList.length == 0">
                    {{ messages.get('dsi', 'del') }}
                </button>
            </div>
        </div>
        <pagination-list :list="sortedList" :perPage="10" :startPage="1" :nbPage="6" :nbResultDisplay="false">
            <template #content="{ list }">
                <table class="uk-table uk-table-small uk-table-striped uk-table-middle">
                    <thead>
                        <tr>
                            <th role="button" @click="sortTable('date')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_date') }}
								<i :class="sortColumn === 'date' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
                            </th>
                            <th role="button" @click="sortTable('totalRecipients')" style="cursor: pointer;">
                                {{ messages.get('dsi', 'diffusion_recipients') }}
								<i :class="sortColumn === 'totalRecipients' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
                            </th>
                            <th>{{ messages.get('dsi', 'actions') }}</th>
                            <th class="right dsi-inline">
                                <label for="delete_all">{{ messages.get('dsi', 'del') }}</label>
                                <input type="checkbox" id="delete_all" name="delete_all" value="1" v-model="deleteAll" @change="switchDelete" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(history, index) in list" :key="index">
                            <td>{{ history.date }}</td>
                            <td>{{ history.totalRecipients }}</td>
                            <td>
                                <report
                                    :history="history"
                                    :contentHistoryTypes="contentHistoryTypes">
                                </report>
                                <button type="button" class="bouton" @click="preview(history.id)">
                                    {{ messages.get('dsi', 'preview') }}
                                </button>
                            </td>
                            <td class="right dsi-inline" style="pointer:click;">
                                <input type="checkbox" name="delete_history_diffusion" :value="history.id" v-model="deleteList" @change="deleteAll = false"/>
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
    components : {
        paginationList,
        Filters,
        Report
    },
    props :  {
        list: {
            type: Array,
            default: () => { return []; }
        },
        products: {
            type: Array,
            default: () => { return []; }
        },
        diffusion: {
            type: Object,
            default: () => { return {} }
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
    data : function() {
        return {
            sortColumn: "",
            sortDirection: "asc",
            deleteAll: false,
            deleteList: [],
            filteredList: []
        }
    },
    created: function() {
        this.filteredList = this.list;
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
        }
    },
    methods : {
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
        getProductName: function(id) {
			const product = this.products.find((product) => product.id == id);
            return product ? product.name : "";
		},
        rapport: function (historyID) {
        },
        preview: function (historyID) {
            this.$root.preview(historyID);
        },
        switchDelete: function () {
            this.deleteList = [];
            if (!this.deleteAll) {
                return false;
            }

            const result = document.querySelectorAll("input[name='delete_history_diffusion']");
            if (result) {
                for (const node of result) {
                    this.deleteList.push(parseInt(node.value));
                }
            }
        },
        del: async function () {
            if (confirm(this.messages.get('dsi', 'confirm_del'))) {
				let response = await this.ws.post("DiffusionsHistory", 'delete', {
                    id: this.deleteList.join(',')
                });

				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else if (this.list.length == this.deleteList.length) {
					document.location = "./dsi.php?categ=diffusions_history";
				} else {
                    for (const idHistory of this.deleteList) {
                        const index = this.list.findIndex((history) => {
                            return history.id == idHistory
                        });
                        if (0 <= index) {
                            this.list.splice(index, 1);
                        }
                    }

                    this.deleteList = [];
                }
			}
        }
    }
}
</script>