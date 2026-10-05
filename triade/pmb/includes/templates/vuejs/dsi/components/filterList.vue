<template>
    <div>
        <div class="dsi-space-between">
            <h3 :id="titleId">{{ messages.get('dsi', 'dsi_filter_field') }}</h3>
            <button type="button"
                :aria-expanded="showFilter ? 'true' : 'false'"
                :aria-controls="sectionId"
                :title="btnShowFilter"
                class="bouton dsi-button"
                @click="showFilter = ! showFilter">
                <i v-if="showFilter" class="fa fa-arrow-up" aria-hidden="true"></i>
                <i v-else class="fa fa-arrow-down" aria-hidden="true"></i>
            </button>
        </div>
        <div style="margin-bottom : 20px;" :id="sectionId" role="region" :aria-labelledby="titleId" v-show="showFilter">
            <select v-model="fieldToFilter" @change="resetFilter">
                <option value="" disabled>{{messages.get('dsi', 'dsi_filter_select_field')}}</option>
                <option v-for="field in fields" :key="field.name" :value="field.name">{{messages.get('dsi', field.label)}}</option>
            </select>
            <span v-if="fieldToFilter != ''">
                <filter-tags ref="filterTags" v-if="fieldToFilter == 'tags'" :diffusions="list" @filter="$emit('filter', $event)"></filter-tags>
                <input v-else v-model.trim="filterValue" :type="selectedField.type" @input="filter" />
                <button type="button" class="bouton dsi-button" @click="resetFilter">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </span>
        </div>
    </div>
</template>

<script>
let uid = 0;
import filterTags from './filterTags.vue';
export default {
    props : ['list', 'fields'],
    components: {
        filterTags
    },
    data : function() {
        return {
            id : 0,
            fieldToFilter : "",
            filterValue : "",
            showFilter : false
        }
    },
    created: function() {
        uid++;
        this.id = uid;
    },
    computed : {
        titleId: function(){
            return `dsi-filter-title-${this.id}`;
        },
        sectionId: function(){
            return `dsi-filter-${this.id}`;
        },
        selectedField : function() {
            if(! this.fieldToFilter) {
                return {}
            }
            return this.fields.find((f) => f.name == this.fieldToFilter);
        },
        btnShowFilter: function() {
            if (this.showFilter) {
                return this.messages.get('dsi', 'dsi_filter_hidden');
            }
            return this.messages.get('dsi', 'dsi_filter_show');
        }
    },
    methods : {
        filter : function() {
            let filtered = this.list.filter((e) => {
                if(typeof e[this.fieldToFilter] === 'undefined') {
                    if(typeof e.settings[this.fieldToFilter] === 'undefined') {
                        return;
                    }

                    if(typeof e.settings[this.fieldToFilter] === 'number') {
                        return e.settings[this.fieldToFilter] == this.filterValue;
                    }

                    return e.settings[this.fieldToFilter].toLowerCase().includes(this.filterValue.toLowerCase());
                }

                if(typeof e[this.fieldToFilter] === 'number') {
                    return e[this.fieldToFilter] == this.filterValue;
                }

                return e[this.fieldToFilter].toLowerCase().includes(this.filterValue.toLowerCase());
            });
            this.$emit("filter", filtered);
        },
        resetFilter : function() {
            this.filterValue = "";
            this.$emit("filter", this.list);
            if(this.$refs.filterTags) {
                this.$refs.filterTags.reset();
            }
        }
    }
}
</script>