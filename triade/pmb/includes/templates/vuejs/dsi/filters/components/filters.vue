<template>
    <form class="form-dsi" action="" method="POST" @submit.prevent="">
        <div class="form-contenu">

            <div class="dsi-filters">
                <selectFilter v-for="(filter, index) in filters" :key="index"
                    :label="messages.get('dsi', filter.label)" :options="filter.options" :multiple="true"
                    @update="updateValue(filter.type, $event)">
                </selectFilter>
            </div>

            <div class="dsi-filters">
                <dateFilter @update="updateValue('date', $event)"></dateFilter>
            </div>

            <div class="dsi-filters">
                <inputFilter :label="messages.get('dsi', 'filter_diffusion')" @update="updateValue('diffusions', $event)"
                    :placeholder="messages.get('dsi', 'filter_diffusion_placeholder')">
                </inputFilter>
                <inputFilter :label="messages.get('dsi', 'filter_destinataire')" @update="updateValue('subscribers', $event)"
                    :placeholder="messages.get('dsi', 'filter_destinataire_placeholder')">
                </inputFilter>
            </div>
        </div>
    </form>
</template>

<script>
// Component
import selectFilter from "./filters/selectFilter.vue";
import dateFilter from "./filters/dateFilter.vue";
import inputFilter from "./filters/inputFilter.vue";
import contentFilter from "./filters/contentFilter.vue";
// Filters
import DiffusionHistoryFilters from "../DiffusionHistoryFilters.js";
import DiffusionPendingFilters from "../DiffusionPendingFilters.js";

export default {
    components: {
        selectFilter,
        dateFilter,
        inputFilter,
        contentFilter
    },
    props : {
        filters: {
            type: Array,
            default: () => { return {}; }
        },
        entities: {
            type: Array,
            default: () => { return []; }
        },
        list: {
            type: Array,
            default: () => { return []; }
        },
        contentHistoryTypes: {
            type: Object,
            default: () => { return {}; }
        },
        classFilter: {
            type: String,
            required: true
        }
    },
    data: function() {
        return {
            filtersApply: {
                tags: [],
                entities: [],
                channel: [],
                date: {
                    start: "",
                    end: ""
                },
                diffusions: "",
                subscribers: "",
                contentEntities: {
                    contentType: 0,
                    search: ""
                },
                products: []
            },
            filtersClasses: {
                DiffusionHistoryFilters: new DiffusionHistoryFilters(),
                DiffusionPendingFilters: new DiffusionPendingFilters()
            }
        }
    },
    computed : {
        filtredList: function() {
            let list = this.list;
            for (const filterName in this.filtersApply) {
                if (!Object.hasOwnProperty.call(this.filtersApply, filterName)) {
                    continue;
                }

                const filter = this.filtersApply[filterName];
                list = list.filter((diffusionHistory => this.match(diffusionHistory, {
                    filterName,
                    filter,
                    contentHistoryTypes: this.contentHistoryTypes
                })));
            }
            return list;
        }
    },
    watch: {
        filtredList: function (newValue, oldValue) {
            this.$emit("filter", newValue);
        }
    },
    methods : {
        match: function (diffusionHistory, filter) {
            const filterInstance = this.filtersClasses[this.classFilter] || null;
            if (filterInstance && typeof filterInstance[filter.filterName] === "function") {
                return filterInstance[filter.filterName](diffusionHistory, filter);
            }
            return true;
        },
        updateValue: async function (filter, values) {
            if (!Object.hasOwnProperty.call(this.filtersApply, filter)) {
                console.error('Unknown filter !');
                return false;
            }

            await this.$set(this.filtersApply, filter, values);
        }
    }
}
</script>