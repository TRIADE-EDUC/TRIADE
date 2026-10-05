<template>
    <div>
        <div class="paginator-space-between">
            <h3>{{ messages.get('paginator', 'paginator_filter_field') }}</h3>
            <button type="button" class="bouton paginator-button" @click="showFilter = ! showFilter">
                <i v-if="showFilter" class="fa fa-arrow-up" aria-hidden="true"></i>
                <i v-else class="fa fa-arrow-down" aria-hidden="true"></i>
            </button>
        </div>
        <div style="margin-bottom : 20px;" id="paginator-filter" v-show="showFilter">
            <select v-model="fieldToFilter" @change="resetFilter">
                <option value="" disabled>{{ messages.get('paginator', 'paginator_filter_select_field') }}</option>
                <!-- items_form_agregator_name -->
                <option v-for="field in fields" :key="field.name" :value="field.name">{{ messages.get('paginator', field.label) }}</option>
            </select>
            <span v-if="fieldToFilter != ''">
                <input v-model.trim="filterValue" :type="selectedField.type" @input="filter" />
                <button type="button" class="bouton paginator-button" @click="resetFilter">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </span>
        </div>
    </div>
</template>

<script>
import messages from "../helper/Messages.js";

export default {
    name: "filters",
    props : {
        list: {
            type: Array,
            required: true,
        },
        fields: {
            type: Array,
            required: true,
        },
        url: {
            type: String,
            required: false,
            default: () => ''
        },
    },
    data : function() {
        return {
            fieldToFilter : "",
            filterValue : "",
            showFilter : false,
            messages: messages
        }
    },
    computed : {
        selectedField : function() {
            if (!this.fieldToFilter) {
                return {}
            }
            return this.fields.find((f) => f.name == this.fieldToFilter);
        }
    },
    watch: {
        list: function () {
            this.$nextTick(() => this.filter());
        }
    },
    methods : {
        filter : function() {
            if (this.fieldToFilter) {
                let filtered = this.list.filter((e) => {
                    if (typeof e[this.fieldToFilter] === 'undefined') {
                        if (typeof e.settings === 'undefined' || e.settings[this.fieldToFilter] === 'undefined') {
                            return;
                        }
                        return e.settings[this.fieldToFilter].toLowerCase().includes(this.filterValue.toLowerCase());
                    }
                    return e[this.fieldToFilter].toLowerCase().includes(this.filterValue.toLowerCase());
                });
                this.$emit("filter", filtered);
            } else {
                this.$emit("filter", this.list);
            }
        },
        resetFilter : function() {
            this.filterValue = "";
            this.$emit("filter", this.list);
        }
    }
}
</script>