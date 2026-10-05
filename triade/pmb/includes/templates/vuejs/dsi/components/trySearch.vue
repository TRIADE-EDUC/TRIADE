<template>
    <div class="try-search">
        <div class="dsi-form-group">
            <label for="try-search-btn" class="etiquette">{{ messages.get('dsi', 'selector_form_test') + "&nbsp;" }}</label>
            <div class="dsi-form-group-content">
                <input type="button" :value="messages.get('common', 'search')" style="cursor: pointer;" @click="trySearch"/>
                <input type="button" :value="messages.get('common', 'remove_short')"
                    style="cursor: pointer;" @click="removeSearch"
                    :disabled="!hasResult" v-show="hasResult"/>
            </div>
        </div>
        <div v-if="hasResult" class="try-search-content">
            <hr>
            <pagination-list :list="Object.values(list)" :nbPage="6" :perPage="10" :startPage="1"
                :nbResultDisplay="true">
                <template #content="{ list }">
                    <ul class="dsi-entities-list try-search-content-list">
                        <li v-for="(element, index) in list" :key="index" v-html="element"></li>
                    </ul>
                </template>
            </pagination-list>
            <hr>
        </div>
        <div v-if="!hasResult && displayNoResult" class="try-search-content">
            <hr>
            <span>
                <b>{{ messages.get('dsi', "dsi_not_found_result") }}</b>
            </span>
            <hr>
        </div>
    </div>
</template>

<script>
import paginationList from "./paginationList.vue";

export default {
    props : {
        selector: {
            type: Object,
            default: function() {
                return {
                    namespace: "",
                    selector: [],
                    data: {}
                };
            }
        }
    },
    components: {
        paginationList
    },
    data: function () {
        return {
            list: {},
            displayNoResult: false
        }
    },
    computed: {
        hasResult: function() {
            return Object.keys(this.list).length > 0;
        }
    },
    methods: {
        trySearch: async function () {
            this.displayNoResult = false;

            let response = await this.ws.post('selector', 'search', { selector: this.selector });
            if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
            } else {
                this.list = response;

                if(response.length === 0) {
                    this.displayNoResult = true;
                }
            }
        },
        removeSearch: async function () {
            this.list = {};
        }
    }
}
</script>