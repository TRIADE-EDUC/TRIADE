<template>
    <div v-if="filters.length && view && view.settings && view.settings.filters">
        <form-filter v-for="(filter, i) in view.settings.filters" :key="i"
            :item="item" :view="view" :filters="filters" :filter="filter"
            @reset="resetFilter($event, i)" :show-plus="i + 1 == view.settings.filters.length && i + i < filters.length"
            @add-filter="addFilter"></form-filter>
    </div>
    <span v-else>{{ messages.get("dsi", "form_filter_no_filters") }}</span>
</template>

<script>

import formFilter from "./formFilter.vue";

export default {
    name : "formFilters",
    props : ["item", "view"],
    components : {
        formFilter
    },
    data: function () {
        return {
            filters : []
        }
    },
    created: function() {
        if(! this.view.settings.filters) {
            this.$set(this.view.settings, "filters", []);
            this.addFilter();
        }

        this.getFilters();
    },
    methods : {
        getFilters : async function() {
            let response = await this.ws.get("items", "availableFilters/"+ this.item.type);
            if(! response.error) {
                this.filters = response;
            }
            for(let filter of this.filters) {
                for(let field in filter.fields) {
                    if(filter.fields[field].ajax) {
                        let options = await this.ws.get("items", "filters/options/" + field);
                        if(! options.error) {
                            filter.fields[field].options = options;
                        }
                    }
                }
            }
        },
        addFilter : function() {
            if(this.view.settings.filters){
                let length = this.view.settings.filters.length;
                this.$set(this.view.settings.filters, length, {});
                this.$set(this.view.settings.filters[length], "namespace", "");
            }
        },
        resetFilter: function(fullReset, index) {
            if(fullReset) {
                this.$delete(this.view.settings.filters, index);
                if(this.view.settings.filters.length == 0) {
                    this.addFilter();
                }
            }else {
                this.$set(this.view.settings.filters[index], "fields", {});
            }
        }
    },
    watch: {
        "item.type" : async function() {
            await this.getFilters();
            if(this.view && this.view.settings && this.view.settings.filters) {
                this.$set(this.view.settings, "filters", []);
                this.addFilter();
            }
        }
    }
}
</script>