<template>
	<div class="subscriber-list-container">
		<formSubscriberList v-if="Object.keys(types).length" 
			:subscriber-list="subscriberList"  
			:types="types" 
			:modelForm="false" 
			:channel-type="channelType"
			:id-entity="idEntity"></formSubscriberList>
	</div>
</template>

<script>
import formSubscriberList from "./formSubscriberList.vue";

	export default {
		props : ["subscriberList", "channelType", "idEntity"],
		components : {
			formSubscriberList
		},
		watch : {
			channelType : async function() {
				if(! this.subscriberList.source.id) {
					return;
				}
				let filteredSubscribers = await this.ws.get("subscriberList", "filterSubscribers/" + this.subscriberList.source.id + "/" + this.channelType);
				if(! filteredSubscribers.error) {
					this.$set(this.subscriberList.source, "subscribers", filteredSubscribers);
				}
			}
		},
		created : function() {
			this.fetchData();
			this.initListners();
		},
		data : function() {
			return {
				types : [],
				key : 0
			}
		},
		methods : {
			fetchData : async function() {
				this.types = await this.ws.get("subscriberList", "getTypes");
			},
			initListners : function() {
				this.$root.$on('updateSubscriberList', (subscriberListId) => this.$set(this.subscriberList.source, 'id', subscriberListId));
			}
		}
	}
</script>