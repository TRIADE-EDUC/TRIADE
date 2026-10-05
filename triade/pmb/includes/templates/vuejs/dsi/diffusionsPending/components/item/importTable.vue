<template>
    <div>
        <pagination-list :list="Object.values(items)" :perPage="5" :startPage="1" :nbPage="6" :nbResultDisplay="true">
            <template #content="{ list }">
                <table class="uk-table uk-table-small uk-table-striped uk-table-middle">
                    <thead>
                        <tr>
                            <th>{{messages.get('dsi', 'subscriber_list_name')}}</th>
                            <th class="right">
                                <span>{{ messages.get('dsi', 'subscriber_selected') }}</span>
                                <button class="dsi-button bouton" type="button" @click="selectAll(true)">
                                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                </button>
                                <button class='dsi-button bouton' type="button" @click="selectAll(false)">
                                    <i class="fa fa-square-o" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in list" :key="index">
                            <td>{{ item }}</td>
                            <td class="right dsi-inline" style="pointer:click;">
                                <input type="checkbox" v-model="selectedItems[index]" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </pagination-list>
        <input type="button" style="cursor:pointer;" :value="messages.get('dsi', 'subscribers_import')" @click.prevent="importItems" />
        <input type="button" style="cursor:pointer;" class="right" :value="messages.get('common', 'cancel')" @click.prevent="$emit('cancelImport')" />
    </div>
</template>
<script>
import paginationList from '../../../components/paginationList.vue';
export default {
    props: ["items", "idEntity"],
    components : {
        paginationList
    },
    data: function() {
        return {
            selectedItems : {}
        }
    },
    methods: {
        importItems: async function() {
            let items = {};
            for(let i in this.selectedItems) {
                const id = Object.keys(this.items)[i];
                items[id] = this.items[id];
            }
            if(! Object.keys(items).length) {
                this.notif.error(this.messages.get('dsi', 'subscribers_no_selected'));
                return;
            }

            for(const item in items) {
                this.$emit("addItem", {[item]: items[item]});
            }
            this.$emit('cancelImport');

        },
        selectAll: function(value) {
            for(let i in this.selectedItems) {
                this.$set(this.selectedItems, i, value);
            }
        }
    }
}
</script>