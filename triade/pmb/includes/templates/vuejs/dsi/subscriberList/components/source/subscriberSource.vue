<template>
	<div>
		<div v-if="sources.length > 1" class="dsi-form-group">
			<label class="etiquette" for="name">{{ messages.get('dsi', 'subscriber_list_source') }}</label>
			<div class="dsi-form-group-content">
				<select @change.prevent="updateSource" v-model="subscriberList.settings.subscriberListSource.namespace" required>
					<option disabled value="">{{ messages.get('dsi', 'subscriber_list_source_default_value') }}</option>
					<option v-for="(source, index) in sources" :key='index' :value="source.namespace">{{source.name}}</option>
				</select>
			</div>
		</div>
		<selector :from="from" v-if="selectors.length && subscriberList.settings.subscriberListSource" :subscriber-list="subscriberList" :selectors="selectors"></selector>
	</div>
</template>

<script>
	import selector from "./selector.vue";
	export default {
		props : ["sources", "subscriberList", "from"],
		components : {
			selector
		},
		data : function() {
			return {
				selectors : []
			}
		},
		created : function() {
			this.init();
			this.initListeners();
		},
		methods : {
			init : function() {
				if(! this.subscriberList.settings.subscriberListSource) {
					if(this.sources.length == 1) {
						this.$set(this.subscriberList.settings, "subscriberListSource", this.sources[0]);
					} else {
						this.$set(this.subscriberList.settings, "subscriberListSource", { namespace : ""});
					}
				}
				if(this.subscriberList.settings.subscriberListSource.namespace) {
					this.updateSource();
				}
			},
			updateSource : async function() {
				let selectors = await this.ws.get('subscriberList', 'getSelectors/' + encodeURI(
					this.subscriberList.settings.subscriberListSource.namespace.replaceAll("\\", "-")
				));
				if (selectors.error) {
					this.notif.error(this.messages.get('dsi', selectors.errorMessage));
				} else {
					this.selectors = selectors;
				}
			},
			initListeners : function() {
				this.$root.$on("resetSource", () => {
					if(this.sources.length == 1) {
						this.$set(this.subscriberList.settings, "subscriberListSource", this.sources[0]);
					} else {
						this.$set(this.subscriberList.settings, "subscriberListSource", { namespace : ""});
					}
					this.$root.$emit("resetSelector");
				})
			}
		}
	}
</script>