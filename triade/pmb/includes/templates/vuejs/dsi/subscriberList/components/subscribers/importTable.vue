<template>
    <div>
        <label>{{ messages.get('dsi', 'import_subscribers_detail') }}</label>
        <pagination-list :filter-fields="fields" :list="subscribers" format="table" :perPage="10" :startPage="1" :nbPage="6" :nbResultDisplay="true">
            <template #content="{ list }">
                <table class="uk-table uk-table-small uk-table-striped uk-table-middle">
                    <thead>
                        <tr>
                            <th >{{messages.get('dsi', 'subscriber_list_name')}}</th>
                            <th >{{messages.get('dsi', 'subscriber_list_id_empr')}}</th>
                            <th >{{messages.get('dsi', 'subscriber_list_cb')}}</th>
                            <th >{{messages.get('dsi', 'subscriber_list_email')}}</th>
                            <th>
                                <span>{{ messages.get('dsi', 'subscriber_selected') }}</span>
                                <span class="right">
                                    <button class="dsi-button bouton" type="button" @click="selectAll(true)">
                                        <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                    </button>
                                    <button class='dsi-button bouton' type="button" @click="selectAll(false)">
                                        <i class="fa fa-square-o" aria-hidden="true"></i>
                                    </button>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(empr, index) in list" :key="index">
                            <td>{{ empr.name }}</td>
                            <td>{{ empr.settings.idEmpr }}</td>
                            <td>{{ empr.settings.cb }}</td>
                            <td>{{ empr.settings.email }}</td>
                            <td class="dsi-table-right dsi-inline" style="pointer:click;">
                                <input v-if="typeof empr[id] === 'undefined'" type="checkbox" v-model="selectedSubscribers[empr.settings[id]]" />
                                <input v-else type="checkbox" v-model="selectedSubscribers[empr[id]]" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </pagination-list>
        <input type="button" style="cursor:pointer;" :value="messages.get('dsi', 'subscribers_import')" @click.prevent="importSubscribers" />
        <input type="button" style="cursor:pointer;" class="right" :value="messages.get('common', 'cancel')" @click.prevent="$emit('cancelImport')" />
    </div>
</template>

<script>
import paginationList from '../../../components/paginationList.vue';

export default {
    props : ["subscribers", "idEntity", "fromPending"],
    components : {
        paginationList
    },
    data : function() {
        return {
            selectedSubscribers : {},
            fields : [
                {
					name : "name",
					label : "subscriber_list_name",
					type : "text"
				},
				{
					name : "email",
					label : "subscriber_list_email",
					type : "text"
				},
				{
					name : "idEmpr",
					label : "subscriber_list_id_empr",
					type : "number"
				}
            ],
            id : ""
        }
    },
    created : function()
    {
        this.init();
    },
    methods : {
        init : function()
        {
            this.id = this.Const.subscriberlist.subscriberPmbId;
            for(let i in this.subscribers) {
                if(typeof this.subscribers[i][this.id] === 'undefined') {
                    this.$set(this.selectedSubscribers, this.subscribers[i].settings[this.id], true);
                    continue;
                }
                this.$set(this.selectedSubscribers, this.subscribers[i][this.id], true);
            }
        },
        importSubscribers : async function()
        {
            let subscribers = [];
            for(let i in this.selectedSubscribers) {
                if(this.selectedSubscribers[i]) {
                    let subscriber = this.subscribers.find((s) => {
                        if(typeof s[this.id] === 'undefined') {
                            if(typeof s.settings[this.id] === 'undefined') {
                                return false;
                            }
                            return s.settings[this.id] == i;
                        }
                        return s[this.id] == i;
                    });
                    subscriber.type = this.Const.subscriberlist.subscriberTypes.import;
                    subscribers.push(subscriber);
                }
            }
            if(! subscribers.length) {
                this.notif.error(this.messages.get('dsi', 'subscribers_no_selected'));
                return;
            }

            if(this.fromPending) {
                this.$root.$emit("importSubscriber", subscribers);
                this.$emit('cancelImport');
                return;
            }

            let response = await this.ws.post("subscribers", this.$root.categ + "/importSubscribers/"+ this.idEntity, {"subscribers": subscribers});
            if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
                for(let subscriber of response) {
                    this.$root.$emit("addSubscriber", subscriber);
                }
                this.$emit('cancelImport');
            }
        },
        selectAll : function(value)
        {
            for(let i in this.selectedSubscribers) {
                this.$set(this.selectedSubscribers, i, value);
            }
        }
    }
}
</script>