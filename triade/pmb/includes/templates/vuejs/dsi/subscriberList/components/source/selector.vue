<template>
	<div>
		<div class="dsi-form-group" v-if="selectors.length > 1">
			<label class="etiquette" for="name">{{ messages.get('dsi', 'subscriber_list_selector') }}</label>
			<div class="dsi-form-group-content">
				<select v-model="subscriberList.settings.subscriberListSource.subscriberListSelector.namespace" @change="clearDataSelector" required>
					<option disabled value="">{{ messages.get('dsi', 'subscriber_list_selector_default_value') }}</option>
					<option v-for="(selector, index) in filteredSelectors" :key='index' :value="selector.namespace">{{selector.name}}</option>
				</select>
			</div>
		</div>
		<component
			:is="selectorType"
			:allowImport="from == 'source'"
			rmc_type="emprunteurs"
			entity_type="empr"
            :data="subscriberList.settings.subscriberListSource.subscriberListSelector.data"
			@updateRMC="updateRMC"
			@startImport="$root.$emit('startImport', from)"
			:from="from">
		</component>
	</div>
</template>

<script>
	import EmprRMCSelector from "../../../components/RMCForm.vue";
	import EmprSimpleSearchSelector from "./selectorEmpr.vue";
	import SearchByIdSelector from "./selectorEmprId.vue";

	export default {
		props : ['subscriberList', 'selectors', 'from'],
		components : {
			EmprRMCSelector,
            EmprSimpleSearchSelector,
			SearchByIdSelector
		},
		computed : {
			selectorType : function() {
				if (this.subscriberList.settings.subscriberListSource.subscriberListSelector.namespace) {
                    let explodedName = this.subscriberList.settings.subscriberListSource.subscriberListSelector.namespace.split("\\");
                    let className = explodedName[explodedName.length-1];
                    if (this.$options.components[className]) {
                        return className;
                    }
				}
				return "";
			},
			filteredSelectors : function() {
				if(this.$root.categ != "subscriber_list") {
					return this.selectors;
				}
				return this.selectors.filter(s => s.allowedInModels == 1);
			}
		},
		created : function() {
			this.init();
		},
		methods : {
			init : function() {
				if(! this.subscriberList.settings.subscriberListSource.subscriberListSelector) {
					if(this.selectors.length == 1) {
						this.$set(this.subscriberList.settings.subscriberListSource, "subscriberListSelector", { namespace : this.selectors[0].namespace});
					} else {
						let i = this.selectors.findIndex(s => s.namespace == this.Const.subscriberlist.defaultImportSubscriberSelectorNamespace);
						if(i != -1 && this.from == this.Const.subscriberlist.from.import) {
							this.$set(this.subscriberList.settings.subscriberListSource, "subscriberListSelector", { namespace : this.selectors[i].namespace});
						} else {
							this.$set(this.subscriberList.settings.subscriberListSource, "subscriberListSelector", { namespace : ""});
						}
					}
				}
				if(! this.subscriberList.settings.subscriberListSource.subscriberListSelector.data) {
					this.$set(this.subscriberList.settings.subscriberListSource.subscriberListSelector, "data", undefined);
				}
			},
			updateRMC : async function(data) {
				this.$set(this.subscriberList, "subscribers", []);
				this.subscriberList.settings.subscriberListSource.subscriberListSelector.data = data;
			},
			clearDataSelector: function() {
                this.$set(this.subscriberList.settings.subscriberListSource.subscriberListSelector, "data", undefined);
			},
			initListeners : function() {
				this.$root.$on("resetSelector", () => {
					if(this.selectors.length == 1) {
						this.$set(this.subscriberList.settings.subscriberListSource, "subscriberListSelector", { namespace : this.selectors[0].namespace});
					} else {
						this.$set(this.subscriberList.settings.subscriberListSource, "subscriberListSelector", { namespace : ""});
					}
					this.$set(this.subscriberList.settings.subscriberListSource.subscriberListSelector, "data", undefined);
				})
			}
		}
	}
</script>