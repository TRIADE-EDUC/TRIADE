<template>
	<div id="dsi-form-source">
		<div class="dsi-form-group" v-show="type != 0 && sourceList.length >= 2">
			<label class="etiquette" for="sourceList">{{ messages.get('dsi', 'source_form_source') }}</label>
			<div class="dsi-form-group-content">
				<template>
					<select id="sourceList" name="sourceList" v-model="source.namespace" @change="updateSource(source)" required>
						<option value="" disabled>{{ messages.get('dsi', 'source_form_default_source') }}</option>
						<option v-for="(sourceItem, index) in sourceList" :key="index" :value="sourceItem.namespace">
							{{ sourceItem.name }}
						</option>
					</select>
				</template>
				<template v-if="sourceList.length == 0">
					<h3><b>{{ messages.get('dsi', 'source_not_available') }}</b></h3>
				</template>
			</div>
		</div>
		<div v-if="source.selector && source.namespace != '' && sourceList.length != 0">
			<selector :selector="source.selector" :child="source.namespace" @updateSettings="$emit('updateSettings', source)"></selector>
		</div>
	</div>
</template>

<script>
	import selector from "./selector.vue";
	export default {
		props : ["type", "settings"],
		components : {
			selector
		},
		data: function () {
			return {
				source: this.settings ? this.settings : {},
				sourceList: [],
			}
		},
		watch: {
			settings: function() {
				this.$set(this, "source", this.settings);
			}
		},
		created: async function() {
			await this.getSourceList();

			if(this.source.namespace === undefined) {
				this.$set(this.source, "namespace", "");
			}
			
			if(this.source.selector === undefined) {
				this.$set(this.source, "selector", {"namespace": ""});
			}

			if(this.sourceList.length == 1) {
				this.$set(this.source, "namespace", this.sourceList[0].namespace);
			}
		},
		methods: {
			getSourceList: async function() {
				if(this.type) {
					let response = await this.ws.get('items', 'getSourceList/' + this.type);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						this.sourceList = response;
					}
				}
			},
			updateSource : function(source) {
				this.$emit('updateSettings', source);
			}
		}
	}
</script>