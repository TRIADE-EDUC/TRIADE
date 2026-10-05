<template>
    <div>
        <h2 class="section-sub-title">{{ history.diffusion.name }}</h2>
        <div class="dsi-form-diffusion-edit">
            <div class="dsi-diffusion-view">
                <pagination-list :list="Object.values(subscribers)" :nbPage="4" :perPage="5" :startPage="1" :nbResultDisplay="false">
                    <template #content="{ list }">
                        <ul>
                            <li v-for="subscriber in list">
                                {{ subscriber.name }}
                                <button v-if="!isDeleted(subscriber)" class="bouton" @click="remove(subscriber)">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                                <button v-else class="bouton" @click="remove(subscriber)">
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                </button>
                            </li>
                        </ul>
                    </template>
                </pagination-list>
            </div>
            <div class="dsi-diffusion-aside">
                <div class="dsi-diffusion-subscriber">
                    <manual-add 
                        :subscribers="{'subscribers': this.subscribers }"
                        :types="types"
                        :id-entity="history.numDiffusion"
                        :channel-type="channel.type" 
                        :fromPending="true" />
                </div>
            </div>
        </div>
        <div class='row dsi-form-action dsi-diffusion-pending-actions'>
            <div class="dsi-diffusion-pending-actions-left">
                <input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
                <input name="submit" type="submit" class="bouton" :value="messages.get('common', 'submit')" @click="save">
            </div>
            <div class="dsi-diffusion-pending-actions-right">
                <input @click="reset" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'diffusion_pending_action_reset')"/>
            </div>
        </div>
    </div>
</template>
<script>
import manualAdd from "../../subscriberList/components/subscribers/manualAdd.vue";
import paginationList from "../../components/paginationList.vue";

export default {
    name: "subscriber",
    props: {
        history: {
            type: Object,
            required: true
        },
        types: {
            type: Object,
            required: true
        }
    },
    components: {
        paginationList,
        manualAdd
    },
    data: function() {
        return {
            type: 1,
            channelType: 5
        }
    },
    mounted: function () {
        if (typeof domUpdated === "function") {
            domUpdated();
        }

        this.$root.$on("importSubscriber", this.importSubscribers);
    },
    computed: {
        subscribers: function() {
            return this.history.contentBuffer[this.type][0]["content"];
        },
        channel: function() {
            return this.history.contentBuffer[this.channelType][0]["content"];
        }
    },
    methods: {
        importSubscribers: function(subscribers) {
            subscribers = this.helper.cloneObject(subscribers);
            subscribers.map(subscriber => {
                subscriber.idSubscriberDiffusion = "#" + Date.now()
                subscriber.__class = "Pmb\\DSI\\Models\\SubscriberList\\Subscribers\\SubscriberEmpr"
            });
            this.history.contentBuffer[this.type][0]["content"] = [...new Set([...this.subscribers, ...subscribers])];
        },
        isDeleted: function(subscriber) {
            return this.subscribers.find(sub => sub.idSubscriberDiffusion == subscriber.idSubscriberDiffusion).updateType == 1;
        },
        remove: function(subscriber) {
            if(this.isDeleted(subscriber)) {
                this.subscribers.find(sub => sub.idSubscriberDiffusion == subscriber.idSubscriberDiffusion).updateType = 0;
            } else {
                this.subscribers.find(sub => sub.idSubscriberDiffusion == subscriber.idSubscriberDiffusion).updateType = 1;      
            }
        },
        save: async function() {
            await this.$root.saveContent(this.history.id, this.type, {"data": this.history.contentBuffer[this.type]});
        },
        cancel: function() {
            this.$root.close();
        },
        reset: async function() {
            this.$set(this.history.contentBuffer, this.type, await this.$root.resetContent(this.history.id, this.type));
        }
    }
}
</script>