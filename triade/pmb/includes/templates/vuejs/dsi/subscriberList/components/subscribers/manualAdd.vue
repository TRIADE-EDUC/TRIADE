<template>
    <div class="dsi-tabs">
        <div class="dsi-tab-registers">
            <button v-if="subscribers.length" @click.prevent="switchTab(1)"
                :class="[tabActive == 1 ? 'active-tab bouton' : 'bouton']">
                {{ messages.get('dsi', 'subscriber_tabs_list') }}
            </button>
            <button v-if="editForm" @click.prevent="switchTab(2)"
                :class="[tabActive == 2 ? 'active-tab bouton' : 'bouton']">
                {{ messages.get('dsi', 'subscriber_tabs_edit') }}
            </button>
            <button @click.prevent="switchTab(3)" :class="[tabActive == 3 ? 'active-tab bouton' : 'bouton']">
                {{ messages.get('dsi', 'subscriber_tabs_import') }}
            </button>
            <button @click.prevent="switchTab(4)" v-if="channelType || $root.categ == 'products'"
                :class="[tabActive == 4 ? 'active-tab bouton' : 'bouton']">
                {{ messages.get('dsi', 'subscriber_tabs_add') }}
            </button>
        </div>
        <div class="dsi-tab-bodies">
            <div class="dsi-content" v-show="tabActive == 2">
                <formSubscriber v-if="editForm && Object.keys(requirements).length" :id-entity="idEntity"
                    :subscriber="currentSubscriber" :requirements="requirements"></formSubscriber>
            </div>
            <div class="dsi-content" v-show="tabActive == 3">
                <importSubscribers :types="types" :id-entity="idEntity" :fromPending="fromPending"></importSubscribers>
            </div>
            <div class="dsi-content" v-show="tabActive == 4">
                <formSubscriber v-if="Object.keys(subscriber).length && Object.keys(requirements).length"
                    :id-entity="idEntity" :subscriber="subscriber" :requirements="requirements"
                    :fromPending="fromPending"></formSubscriber>
            </div>
        </div>
    </div>
</template>

<script>
import PaginationList from '../../../components/paginationList.vue';
import formSubscriber from './formSubscriber.vue';
import importSubscribers from './importSubscribers.vue';

export default {
    props: ["subscribers", "types", "idEntity", "channelType", "fromPending"],
    components: {
        formSubscriber,
        importSubscribers,
        PaginationList
    },
    data: function () {
        return {
            subscriber: {},
            currentSubscriber: {},
            tabActive: 0,
            editForm: false,
            fields: [
                {
                    name: "name",
                    label: "subscriber_list_name",
                    type: "text"
                }
            ],
            editIndex: 0,
            requirements: {}
        }
    },
    created: function () {
        this.getSubscriber();
        this.initListners();
        this.getRequirements();
        this.getFirstTabActive();
    },
    watch: {
        subscribers: function () {
            if (this.subscribers.length == 0) {
                this.tabActive = 3;
            }
        },
        channelType: async function () {
            await this.getRequirements();
        }
    },
    methods: {
        removeSubscriberFromList: async function (idSubscriber) {
            let response = await this.ws.post("subscriberList", "removeSubscriberFromList/" + this.idSubscriberList, { "id": idSubscriber });
            if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
            } else {
                this.$emit("removeSubscriber", idSubscriber);
            }
        },

        getFirstTabActive: function () {
            if (this.subscribers.length) {
                this.tabActive = 1;
            } else if (!this.channelType) {
                this.tabActive = 4;
            } else {
                this.tabActive = 3;
            }
        },

        editSubscriber: function (subscriber, i) {
            if (!this.showEdit) {
                this.currentSubscriber = subscriber;
                this.editIndex = i;
            }
            this.editForm = true;
            this.tabActive = 3;
        },
        getSubscriber: async function () {
            let subscriber = await this.ws.get("subscribers", this.$root.categ + "/getEntity");
            this.$set(this, "subscriber", subscriber);
        },
        initListners: function () {
            this.$root.$on("addSubscriber", () => {
                this.getSubscriber();
                this.$set(this, "currentSubscriber", {});
                this.tabActive = 3;
            });
            this.$root.$on("subscriberToEdit", (subscriber) => {
                this.editForm = false;
                this.$set(this, "currentSubscriber", subscriber);
                this.editForm = true;
                this.tabActive = 2;
            })
            this.$root.$on("editSubscriber", (subscriber) => {
                this.$set(this.subscribers, this.editIndex, subscriber);
                this.editForm = false;
                this.$set(this, "currentSubscriber", {});
                this.tabActive = 3;
            });

            this.$root.$on("deleteSubscriber", (subscriber) => {
                this.removeSubscriber(subscriber);
            })
        },
        switchTab: function (tab) {
            this.tabActive = tab;
        },
        getRequirements: async function () {
            let path = "requirements";
            if (this.channelType) {
                path += "/" + this.channelType;
            }
            let requiredFields = await this.ws.get("channels", path);

            if (!requiredFields.subscribers) {
                return;
            }
            this.$set(this, "requirements", requiredFields.subscribers);

            this.$set(this, "fields", [{
                name: "name",
                label: "subscriber_list_name",
                type: "text"
            }]
            );
            for (let requirement in this.requirements) {
                let filter = {
                    name: requirement,
                    label: this.requirements[requirement].input_label,
                    type: this.requirements[requirement].input_type,
                };
                this.$set(this.fields, this.fields.length, filter)
            }
        }
    }
}
</script>