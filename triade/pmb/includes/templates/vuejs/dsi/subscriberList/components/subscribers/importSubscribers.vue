<template>
    <import-table v-if="subscribers.length" :subscribers="subscribers" :id-entity="idEntity" :fromPending="fromPending" @cancelImport="resetComponent"></import-table>
    <div v-else class="dsi-import-subscribers">
        <div class="dsi-form-group">
            <label class="etiquette" for="name">{{ messages.get('dsi', 'subscriber_list_type') }}</label>
            <div class="dsi-form-group-content" v-if="Object.keys(subscriberList).length">
                <select name="select-subscriber-type" id="select-subscriber-type" @change.prevent="updateType" v-model="subscriberList.settings.subscriberListType" required>
                    <option disabled value="">{{ messages.get('dsi', 'subscriber_list_type_default_value') }}</option>
                    <option v-for="(type, id) in types" :key='id' :value="id">{{ getLabelType(type) }}</option>
                </select>
            </div>
        </div>
        <subscriber-source :from="Const.subscriberlist.from.import" v-if="sources.length" :subscriber-list="subscriberList" :sources="sources"></subscriber-source>
        <div class="right">
            <input v-if="sourceFilled" type="button" style="cursor:pointer;" :value="messages.get('common', 'search')" @click.prevent="importSubscribers" />
        </div>
    </div>
</template>

<script>
import subscriberSource from '../source/subscriberSource.vue';
import importTable from './importTable.vue';

export default {
    props : ["types", "idEntity", "fromPending"],
    created : async function()
    {
        await this.init();
    },
    components : {
        subscriberSource,
        importTable
    },
    computed : {
        sourceFilled : function()
        {
            if(this.subscriberList.settings && this.subscriberList.settings.subscriberListSource) {
                if(this.subscriberList.settings.subscriberListSource.subscriberListSelector) {
                    if(this.subscriberList.settings.subscriberListSource.subscriberListSelector.data != "") {
                        return true;
                    }
                }
            }
            return false;
        }
    },
    data : function()
    {
        return {
            subscriberList : {},
            sources : [],
            subscribers : []
        }
    },
    methods : {
        init : async function()
        {
            let subscriberList = await this.ws.get("subscriberList", "getEntity");
            if(!subscriberList.error) {
                this.subscriberList = subscriberList.source;
            }
            if(! this.subscriberList.settings.subscriberListType) {
                let typesIndexes = Object.keys(this.types);
                if(typesIndexes.length == 1) {
                    this.$set(this.subscriberList.settings, "subscriberListType", typesIndexes[0]);
                    this.updateType();
                } else {
                    this.$set(this.subscriberList.settings, "subscriberListType", "");
                }
            }
            this.initListners();
        },
        updateType : async function() {
			let sources = await this.ws.get('subscriberList', 'getSources/'+ this.subscriberList.settings.subscriberListType);
			if (sources.error) {
				this.notif.error(this.messages.get('dsi', sources.errorMessage));
			} else {
				// this.sources = sources;
                // On clone pour éviter des problèmes de réactivité liés au cache du webservice
                this.sources = JSON.parse(JSON.stringify(sources));
			}
		},
        importSubscribers : async function()
        {
            let response = await this.ws.post("subscriberList", "getSubscribersFromList", this.subscriberList);
            if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
				this.subscribers = response;
			}
        },
        resetComponent : async function()
        {
            this.$set(this, "subscribers", []);
            this.$set(this, "sources", []);
            await this.init();
        },
        getLabelType: function(type) {
            let msg = this.messages.get("dsi", "subscriber_label_" + type.toLowerCase());
            return msg ? msg : type;
        },
        initListners : function() {
            this.$root.$on("startImport", (from) => {
                if(from == "import") {
                    this.importSubscribers();
                }
            });
        }
    }
}
</script>