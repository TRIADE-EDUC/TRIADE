<template>
    <div v-if="filters.length && view && view.settings && view.settings.filters" class="dsi-form-group-filter dsi-form-group-flex">
        <div class="dsi-form-group">
            <label v-if="currentFilter.name" class="etiquette visually-hidden">{{ messages.get('dsi', 'view_form_current_filter') }}{{ currentFilter.name }}</label>
            <label v-else class="etiquette visually-hidden">{{ messages.get('dsi', 'view_form_select_another_filter') }}</label>
            <div class="dsi-form-group-content">
                <select v-model="filter.namespace" @change="$emit('reset', false)" class="dsi-select-filter dsi-select">
                    <option disabled value="">{{ messages.get('dsi', 'view_form_filter_default_value') }}</option>
                    <option v-for="(filter, i) in filteredFilters" :value="filter.namespace" :key="i">{{ filter.name }}</option>
                </select>
                <button v-if="filter.namespace != ''" type="button" class="bouton" :title="messages.get('dsi', 'view_form_remove_filter')" @click="$emit('reset', true)">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <button v-if="showPlus && filter.namespace != ''" type="button" class="bouton" :title="messages.get('dsi', 'view_form_add_filter')" @click.prevent="$emit('add-filter')">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="dsi-form-group" v-for="(field, i) in currentFilter.fields" :key="i">
            <div class="dsi-form-fields-filter">
                <label class="etiquette">{{ currentFilter.messages[i] }}</label>
                <div class="dsi-form-group-content" v-if="filter">
                    <select v-if="field.type == 'select'" v-model="filter.fields[i]" class="dsi-select-filter dsi-select">
                        <option v-for="(option, j) in field.options" :key="j" :value="option.value">{{option.label}}</option>
                    </select>
                    <input v-else :type="field.type" v-model="filter.fields[i]" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name : "formFilter",
    props : ['item', "view", "filter", "filters", "showPlus"],
    computed: {
        currentFilter: function() {
            if(this.filter && this.filter.namespace) {
                let currentFilter = this.filters.find(filter => filter.namespace == this.filter.namespace);
                if(currentFilter === undefined) {
                    return {fields: {}}
                }
                return currentFilter;
            }
            return {fields: {}};
        },
        filteredFilters : function() {
            let result = [];
            for(let filter of this.filters) {
                if(this.view.settings.filters.find(f => (f.namespace == filter.namespace) && (f.namespace != this.filter.namespace))) {
                    continue;
                }
                result.push(filter);
            }
            return result;
        }
    }
}
</script>