<template>
	<div id="edit">
		<form action="#" method="POST" @submit.prevent="submit" class="dsi-form-diffusion">
			<modelSelector entity="channels" :idSelected="channel.numModel" :id="channel.id" :restricted-fields="restrictedFields" @updateSelectedModel="addModel" ref="modelSelector" :item="channel" :showLock="true"></modelSelector>
			<lockable :locked="channel.settings.locked">
				<div v-if="is_model" class="dsi-form-group">
					<label class="etiquette" for="channel-name">{{ messages.get('dsi', 'channel_form_name') }}</label>
					<div class="dsi-form-group-content">
						<input type="text" class="dsi-model-name" id="channel-name" name="channel-name" v-model="channel.name">
					</div>
				</div>

				<div class="dsi-form-group">
					<label class="etiquette" for="channelTypeList">{{ messages.get('dsi', 'channel_form_type') }}</label>
					<div class="dsi-form-group-content">
						<select id="channelTypeList" name="channelTypeList" v-model="channel.type" required @change="resetChannel">
							<option value="" disabled>{{ messages.get('dsi', 'channel_form_default_type') }}</option>
							<option v-for="(type, index) in filteredChannelTypes" :key="index" :value="type.id">
								{{ type.name }}
							</option>
						</select>
					</div>
				</div>
				<tags :tags="channel.tags"
					entity="channels"
					:entity-id="channel.id"
					@newTagList="$set(channel, 'tags', $event)"></tags>
				<template v-if="getTypeNameFromNamespace != '' && getTypeNameFromNamespace != 0">
					<component :is="getTypeNameFromNamespace" :channel="channel" :channelType="channelType"></component>
				</template>
			</lockable>
			<div class='row dsi-form-action'>
				<div class="left">
					<template v-if="is_model">
						<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
						<input name="submit_model" type="submit" class="bouton" :value="$root.action == 'edit' ? messages.get('common', 'submit') : messages.get('dsi', 'submit_model')">
					</template>

					<template v-if="!is_model">
						<input name="submit" type="submit" class="bouton" :value="messages.get('common', 'submit')">
						<input @click="showModal = true" type="button" class="bouton" :value="messages.get('dsi', 'submit_model')">
					</template>
				</div>
				<div v-if="is_model && channel.id" class="right">
					<input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')"/>
				</div>
			</div>
			<modalModelSelector :showModal="showModal" :entity="channel" @close="showModal = false"></modalModelSelector>
		</form>
	</div>
</template>

<script>
	import mailChannel from "./channels/mailChannel.vue";
	import smsChannel from "./channels/smsChannel.vue";
	import portalChannel from "./channels/portalChannel.vue";
	import humHubChannel from "./channels/humHubChannel.vue";
	import rssChannel from "./channels/rssChannel.vue";
	import exportChannel from "./channels/exportChannel.vue";
	import cartChannel from "./channels/cartChannel.vue";

	import modelSelector from "@dsi/components/modelSelector.vue";
	import modalModelSelector from "@dsi/components/modalModelSelector.vue";
	import tags from "@dsi/components/tags.vue";
	import lockable from "@dsi/components/lockable.vue";

	export default {
		props : ["channel", "channel_type_list", "is_model"],
		name: "formChannel",
		data: function () {
			return {
				model: null,
				showModal: false,
				restrictedFields : ["model", "numModel", "id", "name", "idChannel"]
			}
		},
		components: {
			tags,
			lockable,
			modelSelector,
			modalModelSelector,
			// channels :
			mailChannel,
			smsChannel,
			portalChannel,
			humHubChannel,
			rssChannel,
			exportChannel,
			cartChannel
		},
		created: function() {
			this.channel.type = this.channel.type != 0 ? this.channel.type : "";
		},
		computed: {
			getTypeNameFromNamespace: function() {
				var selectedType = this.channel_type_list.find(channel => channel.id == this.channel.type);
				if(selectedType === undefined) {
					return "";
				}
				var namespace = selectedType.namespace.split("\\");
				namespace = namespace[namespace.length-1];

				var typeName = "";
				for(var i=0; i<3; i++) {
					typeName += namespace.charAt(i).toLowerCase();
				}
				typeName += namespace.slice(3);

				return typeName;
			},
			filteredChannelTypes: function() {
				return this.channel_type_list.filter((c) => {
					//pas de modeles pour les canaux sans diff manuelle
					if((parseInt(c.manually) == 0) && (this.$root.categ == 'channels')) {
						return false;
					}
					return true;
				});
			},
			channelType: function() {
				return this.channel_type_list.find(channel => channel.id == this.channel.type);
			}
		},
		methods: {
			submit: async function(e) {
				var lastId = 0;

				if(e.submitter && (e.submitter.name === "submit_model" || e.submitter.name === "submit_model_from_modal") || e.name === "submit_model_from_modal") {
					lastId = this.channel.id;

					this.channel.id = e.name === "submit_model_from_modal" || e.submitter.name === "submit_model_from_modal" ? 0 : this.channel.id;
					this.channel.model = true;
				}

				let response = await this.ws.post('Channels', 'save', this.channel);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));

				// model form
				} else if (this.is_model){
					document.location = `./dsi.php?categ=channels&action=edit&id=${response.id}`;

				// model form diffusion
				} else if (this.channel.model && this.channel.id == 0) {
					this.$set(this.channel, "model", false);
					this.$set(this.channel, "id", lastId);
					this.$set(this.channel, "name", "");

					this.showModal = false;

					this.$refs.modelSelector.getList();
					this.notif.info(this.messages.get('common', 'success_save'));

				// form diffusion
				} else {
					this.channel.id = response.id;
					this.$emit("saveDiffusionChannel", response.id);
				}
				this.$root.$emit('channelSaved');
			},
			del: async function() {
				if (confirm(this.messages.get('dsi', 'confirm_del'))) {
					let response = await this.ws.post("Channels", 'delete', this.channel);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						document.location = "./dsi.php?categ=channels";
                    }
                }
			},
			cancel: function() {
		        document.location = "./dsi.php?categ=channels";
		    },
			addModel: function(model) {
				if(model != 0) {
					let clone = JSON.parse(JSON.stringify(model));
					const ignoreKeys = ["id", "idChannel", "numModel", "name"];
					for(let property in clone) {
						if(ignoreKeys.indexOf(property) == -1) {
							this.$set(this.channel, property, clone[property]);
						}
					}
					this.$set(this.channel, "numModel", clone["id"]);
				}else {
					this.$set(this.channel, "numModel", 0);

					this.$set(this.channel, "type", "");
					this.$set(this.channel, "settings", {});
				}

				this.$set(this.channel, "model", 0);
			},
			resetChannel: function() {
				this.$set(this.channel, "settings", {});
			},
		}
	}
</script>