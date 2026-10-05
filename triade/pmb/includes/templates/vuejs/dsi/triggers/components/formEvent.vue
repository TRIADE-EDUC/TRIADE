<template>
	<div id="form">
		<form action="#" method="POST" class="dsi-form-view" @submit.prevent="submit">
			<modelSelector entity="triggers" :idSelected="event.numModel" :id="event.id" :restricted-fields="restrictedFields" @updateSelectedModel="addModel" ref="modelSelector" :item="event" :showLock="true"></modelSelector>
			<lockable :locked="event.settings.locked">
				<div class="dsi-form-group">
					<label class="etiquette" for="trigger-name">{{ messages.get('dsi', 'event_form_name') }}</label>
					<div class="dsi-form-group-content">
						<input type="text" id="trigger-name" name="trigger-name" class="dsi-model-name" v-model="event.name" required>
					</div>
				</div>

				<fieldset v-if="!is_model && !is_product && view.id && item.id" class="dsi-form-diffusion-event-conditions">
					<legend>{{ messages.get('dsi', 'event_condition_trigger') }}</legend>
					<conditions :settings="event.settings" :context="'trigger'"></conditions>
				</fieldset>


				<div class="dsi-form-group">
					<label class="etiquette" for="eventTypeList">{{ messages.get('dsi', 'event_form_type') }}</label>
					<div class="dsi-form-group-content">
						<select id="eventTypeList" name="eventTypeList" v-model="event.type" required>
							<option value="" disabled>{{ messages.get('dsi', 'event_form_default_type') }}</option>
							<option v-for="(type, index) in types" :key="index" :value="type.id">
								{{ type.name }}
							</option>
						</select>
					</div>
				</div>
				<tags
					v-if="event.id"
					:tags="event.tags"
					entity="triggers"
					:entity-id="event.id"
					@newTagList="$set(event, 'tags', $event)"></tags>
				<component :is="selectorType" :event="event"></component>
			</lockable>
			<div class='row dsi-form-action'>
				<div class="left">
					<template v-if="is_model">
						<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
						<input name="submit_model" type="submit" class="bouton" :value="$root.action == 'edit' ? messages.get('common', 'submit') : messages.get('dsi', 'submit_model')">
					</template>

					<template v-if="!is_model">
						<input name="submit" type="submit" class="bouton" :value="messages.get('common', 'submit')">
						<input @click="showModal = true;" type="button" class="bouton" :value="messages.get('dsi', 'submit_model')">
					</template>
				</div>
				<div v-if="is_model && event.id" class="right">
					<input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')"/>
				</div>
			</div>
			<modalModelSelector :showModal="showModal" :entity="event" @close="showModal = false"></modalModelSelector>
		</form>
	</div>
</template>

<script>
	import periodicalEvent from "./events/periodicalEvent.vue";
	import modelSelector from "@dsi/components/modelSelector.vue";
	import modalModelSelector from "@dsi/components/modalModelSelector.vue";
	import tags from "@dsi/components/tags.vue";
	import lockable from '@dsi/components/lockable.vue';
	import conditions from '@dsi/components/Conditions/conditions.vue';

	export default {
		props : ["event", "types", "item", "view", "is_model", "is_product"],
		components: {
			periodicalEvent,
			modelSelector,
			modalModelSelector,
			tags,
			lockable,
			conditions
        },
		data: function () {
			return {
				model: null,
				showModal: false,
				restrictedFields : ["model", "numModel", "id", "name", "idEvent", "eventModel"]
			}
		},
		created: function() {
			if(this.event.settings === undefined || this.event.settings === "") {
				this.$set(this.event, "settings", {});
			}
			this.event.type = this.event.type != 0 ? this.event.type : "";
		},
		updated: function() {
			this.event.type = this.event.type != 0 ? this.event.type : "";
			this.model = this.event.numModel != 0 ? this.model : null;
		},
		computed : {
			selectorType : function() {
				let type = this.types.find(elem => elem.id == this.event.type);

				if(type != undefined) {
					switch(type.namespace) {
						case "Pmb\\DSI\\Models\\Event\\Periodical\\PeriodicalEvent":
							return "periodicalEvent";
						default:
							return "";
					}
				}
				return "";
			},
		},
		methods: {
			cancel: function() {
				document.location = "./dsi.php?categ=triggers";
			},
			submit: async function(e) {
				var lastId = 0;

				if(e.submitter && (e.submitter.name === "submit_model" || e.submitter.name === "submit_model_from_modal") || e.name === "submit_model_from_modal") {
					lastId = this.event.id;

					this.event.id = e.name === "submit_model_from_modal" || e.submitter.name === "submit_model_from_modal" ? 0 : this.event.id;
					this.event.model = true;
				}

				let response = await this.ws.post('triggers', 'save', this.event);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				// model form
				} else if (this.is_model){
					document.location = `./dsi.php?categ=triggers&action=edit&id=${response.id}`;

				// model form diffusion
				} else if (this.event.model && this.event.id == 0) {
                    this.$set(this.event, "model", false);
                    this.$set(this.event, "id", lastId);

                    this.showModal = false;

                    this.$refs.modelSelector.getList();
                    this.notif.info(this.messages.get('common', 'success_save'));
				// form diffusion
				} else {
					this.event.id = response.id;
					this.$emit("addEvent", this.event);
				}
			},
			del : async function() {
				if (confirm(this.messages.get('dsi', 'confirm_del'))) {
					let response = await this.ws.post("triggers", 'delete', this.event);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						document.location = "./dsi.php?categ=triggers";
                    }
                }
			},
			addModel: function(model) {
				if(model != 0) {
					let clone = JSON.parse(JSON.stringify(model));
					const ignoreKeys = this.Const.eventIgnoredKeys;
					for(let property in clone) {
						if(ignoreKeys.indexOf(property) == -1) {
							this.$set(this.event, property, clone[property]);
						}
					}
					this.$set(this.event, "numModel", clone["id"]);
				}else {
					this.$set(this.event, "numModel", 0);
					this.$set(this.event, "type", "");
					this.$set(this.event, "settings", {});

				}
				this.$set(this.event, "model", 0);
			}
		}
	}
</script>