<template>
	<div>
		<form action="#" method="POST" id="form-subscriberlist" name="form-subscriberlist"
			class="dsi-form-subscriberlist" @submit.prevent="submitSubscriberList">
			<modelSelector v-if="models.length" :model-list="models" entity="subscriberList"
				:id="subscriberList.source.id" :restricted-fields="restrictedFields" @updateSelectedModel="addModel"
				ref="modelSelector" :item="subscriberList.source" :showLock="true"></modelSelector>
			<lockable :locked="subscriberList.source.settings.locked">
				<div style="display:grid;">
					<div id="dsi-diffusion-aside"
						style="grid-column-start : 1; grid-column-end:2; padding-right: 1rem;">
						<div v-if="modelForm" class="dsi-form-group">
							<label class="etiquette"
								for="name">{{ messages.get('dsi', 'subscriber_list_form_name') }}</label>
							<div class="dsi-form-group-content">
								<input type="text" name="name-subsriber-list" id="name-subsriber-list"
									v-model="subscriberList.source.name" required />
							</div>
						</div>
						<div v-if="Object.keys(types).length > 1" class="dsi-form-group">
							<label class="etiquette"
								for="name">{{ messages.get('dsi', 'subscriber_list_type') }}</label>
							<div class="dsi-form-group-content">
								<select @change.prevent="updateType"
									v-model="subscriberList.source.settings.subscriberListType" required>
									<option disabled value="">
										{{ messages.get('dsi', 'subscriber_list_type_default_value') }}</option>
									<option v-for="(type, id) in types" :key='id' :value="id">{{ type }}</option>
								</select>
							</div>
						</div>
						<tags v-if="subscriberList.source.id" :tags="subscriberList.source.tags" entity="subscriberList"
							:entity-id="subscriberList.source.id"
							@newTagList="$set(subscriberList.source, 'tags', $event)"></tags>
						<subscriber-source :key="sourceKey" v-if="sources.length"
							:from="Const.subscriberlist.from.source" :subscriber-list="subscriberList.source"
							:sources="sources"></subscriber-source>
					</div>
					<subscribers v-if="subscriberList.source.subscribers" :types="types"
						:subscriber-list="subscriberList.source" style="grid-column-start: 3; grid-column-end: 2;"
						:lists="subscriberList.lists" :id-entity="idEntity" :channel-type="channelType"
						@removeSubscriber="removeSubscriber">
					</subscribers>
				</div>
			</lockable>
			<div class='row dsi-form-action'>
				<div class="left">
					<template v-if="modelForm">
						<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
						<input name="submit_model" type="button" class="bouton"
							@click.prevent="submitSubscriberList($event.target)"
							:value="$root.action == 'edit' ? messages.get('common', 'submit') : messages.get('dsi', 'submit_model')">
					</template>

					<template v-if="!modelForm">
						<input name="submit" type="button" @click.prevent="submitSubscriberList" class="bouton"
							:value="messages.get('common', 'submit')">
						<input @click="showModal = true" type="button" class="bouton"
							:value="messages.get('dsi', 'submit_model')">
						<input type="button" class="bouton" :value="messages.get('dsi', 'dsi_subscriber_list_reset')"
							@click="reset">
					</template>
				</div>
				<div v-if="modelForm && subscriberList.source.id" class="right">
					<input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')" />
				</div>
			</div>
		</form>
		<modalModelSelector :showModal="showModal" :entity="subscriberList.source" @close="showModal = false"
			@submit="submitSubscriberList" idForm="form-subscriberlist"></modalModelSelector>
	</div>
</template>

<script>
import subscriberSource from "./source/subscriberSource.vue";
import subscribers from "./subscribers/subscribers.vue";
import modelSelector from "@dsi/components/modelSelector.vue";
import modalModelSelector from "@dsi/components/modalModelSelector.vue";
import tags from "@dsi/components/tags.vue";
import lockable from "@dsi/components/lockable.vue";
export default {
	props: ['subscriberList', "types", "modelForm", "channelType", "idEntity"],
	components: {
		subscriberSource,
		subscribers,
		modelSelector,
		modalModelSelector,
		tags,
		lockable
	},
	created: function () {
		this.init();
		this.initListners();
	},
	watch: {
		"subscriberList.source.settings.subscriberListType": function () {
			if (this.subscriberList.source.settings.subscriberListType) {
				this.updateType();
			}
		}
	},
	data: function () {
		return {
			sources: [],
			showModal: false,
			restrictedFields: ["model", "numModel", "id", "name", "idSubscriberList", "subscribers", "subscriberListSource"],
			models: [],
			sourceKey: 0
		}
	},
	methods: {
		init: function () {
			//Aucune foutue idee de comment on peut passer dans ce if mais ca peut arriver
			if(!this.subscriberList.source.settings) {
				this.$set(this.subscriberList.source, "settings", {});
			}
			if (!this.subscriberList.source.settings.subscriberListType) {
				//Si on n'a qu'une option on la selectionne et on masque le selecteur
				if (Object.keys(this.types).length == 1) {
					this.$set(this.subscriberList.source.settings, "subscriberListType", Object.keys(this.types)[0]);
				} else {
					this.$set(this.subscriberList.source.settings, "subscriberListType", "");
				}
			}
			if (this.subscriberList.source.idSubscriberList) {
				this.updateType();
			}
			this.getModels();
		},
		removeSubscriber: function (idSubscriber) {
			let i = this.subscriberList.lists.subscribers.findIndex((s) => s.id == idSubscriber);
			if (i != -1) {
				this.$delete(this.subscriberList.lists.subscribers, i);
			}
		},
		updateType: async function () {
			let sources = await this.ws.get('subscriberList', 'getSources/' + this.subscriberList.source.settings.subscriberListType);
			if (sources.error) {
				this.notif.error(this.messages.get('dsi', sources.errorMessage));
			} else {
				this.sources = sources;
			}
		},
		submitSubscriberList: async function (e) {
			let lastId = 0;
			if (e.submitter && (e.submitter.name === "submit_model" || e.submitter.name === "submit_model_from_modal") || e.name === "submit_model_from_modal" || e.name === "submit_model") {
				lastId = this.subscriberList.source.id;
				this.$set(this.subscriberList.source, "model", true);
				if (e.name === "submit_model_from_modal" || (e.submitter && e.submitter.name === "submit_model_from_modal")) {
					this.$set(this.subscriberList.source, "id", 0);
					this.$set(this.subscriberList.source, "idSubscriberList", 0);
				} else {
					this.$set(this.subscriberList.source, "id", this.subscriberList.source.id);
					this.$set(this.subscriberList.source, "idSubscriberList", this.subscriberList.source.idSubscriberList);
				}
			}

			let response = await this.ws.post('subscriberList', 'save', this.subscriberList);
			if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else if (this.modelForm) {
				document.location = `./dsi.php?categ=subscriber_list&action=edit&id=${response.id}`;

				// model form diffusion
			} else if (this.subscriberList.source.model && this.subscriberList.source.id == 0) {
				this.$set(this.subscriberList.source, "model", false);
				this.$set(this.subscriberList.source, "id", lastId);
				this.$set(this.subscriberList.source, "idSubscriberList", lastId);
				this.$set(this.subscriberList.source, "name", "");

				this.showModal = false;

				this.getModels(true);
				this.notif.info(this.messages.get('common', 'success_save'));

				// form diffusion
			} else {
				this.$root.$emit("updateSubscriberList", response.idSubscriberList);
			}
		},
		del: async function () {
			if (confirm(this.messages.get('dsi', 'confirm_del'))) {
				let response = await this.ws.post("subscriberList", 'delete', this.subscriberList);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					document.location = "./dsi.php?categ=subscriber_list";
				}
			}
		},
		cancel: function () {
			document.location = "./dsi.php?categ=subscriber_list";
		},
		initListners: function () {
			this.$root.$on("addSubscriber", (subscriber) => {
				this.$set(this.subscriberList.lists.subscribers, this.subscriberList.lists.subscribers.length, subscriber);
			});
			this.$root.$on("editSubscriber", (subscriber) => {
				let i = this.subscriberList.lists.subscribers.findIndex((s) => s.id == subscriber.id);
				if (i != -1) {
					this.$set(this.subscriberList.lists.subscribers, i, subscriber);
				}
			});
			this.$root.$on("startImport", (from) => {
				if (from == "source") {
					this.importSubscribers();
				}
			});

			this.$root.$on("locked", async (e) => {
				if (e && e.model.subscribers) {
					let response = await this.ws.post('subscriberList', 'save', this.subscriberList.source);
					if (!response.error) {
						if (e.lock) {
							let list = JSON.parse(JSON.stringify(e.model.subscribers));
							let subscribers = await this.ws.post("subscriberList", "updateLockedLists", { entityType: this.$root.categ, idModel: this.subscriberList.source.numModel });
							if (!subscribers.error) {
								this.$set(this.subscriberList.lists, "subscribers", subscribers);
							}
							this.$set(this.subscriberList.source, "subscribers", list);
						}
					}
				}
			})
		},
		addModel: async function (model) {
			if (model != 0) {
				let clone = JSON.parse(JSON.stringify(model));
				const ignoreKeys = this.Const.subscriberlist.ignoredKeys;
				for (let property in clone) {
					if (ignoreKeys.indexOf(property) == -1) {
						this.$set(this.subscriberList.source, property, clone[property]);
					}
				}
				this.$set(this.subscriberList.source, "numModel", clone["idSubscriberList"]);
				//On supprime tous les abonnes en base avant d'ajouter ceux du modele
				let resetSubscribers = await this.ws.post("subscribers", "empty", { "entityType": this.$root.categ, "entityId": this.idEntity });
				if (resetSubscribers.error) {
					return;
				}
				this.$set(this.subscriberList.source, "subscribers", clone.subscribers);
				let data = await this.ws.get("subscriberList", "subscribers/" + clone.id);
				if (!data.error && data.subscribers) {
					const unsubscriberType = this.Const.subscriberlist.subscriberStatus.unsubscriber;
					const selfUnsubscriberType = this.Const.subscriberlist.subscriberStatus.selfUnsubscriber;
					const unsubscribers = [unsubscriberType, selfUnsubscriberType];
					let result = [];
					for (let subscriber of data.subscribers) {
						if (unsubscribers.includes(subscriber.updateType)) {
							subscriber.id = 0;
							subscriber.idSubscriber = 0;
							// let newSubscriber = await this.ws.post("subscribers", this.$root.categ + '/unsubscribe/' + this.idEntity, subscriber);
							// if (!newSubscriber.error) {
							result.push(subscriber);
							// }
						}
					}
					let addSubscribers = await this.ws.post("subscribers", this.$root.categ + '/unsubscribe/' + this.idEntity, { "subscribers": result });
					this.$set(this.subscriberList.lists, "subscribers", result);
					//On sauvegarde pour eviter d'avoir fait tout ca pour rien
					let response = await this.ws.post('subscriberList', 'save', this.subscriberList.source);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						this.$root.$emit("updateSubscriberList", response.idSubscriberList);
					}
				}
			} else {
				this.$set(this.subscriberList.source, "numModel", 0);
				//On reset les subscribers
				let resetSubscribers = await this.ws.post("subscribers", "empty", { "entityType": this.$root.categ, "entityId": this.idEntity });
				if (resetSubscribers.error) {
					return;
				}
				this.resetForm();
			}

			this.$set(this.subscriberList.source, "model", 0);
		},
		importSubscribers: async function () {
			let response = await this.ws.post("subscriberList", "getSubscribersFromList", this.subscriberList.source);
			if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
				this.subscriberList.source.subscribers = response;
			}
		},
		getModels: async function (refresh = false) {
			let result = [];
			let list = [];

			if (this.$root.models && this.$root.models["subscriberList"] && !refresh) {
				list = JSON.parse(JSON.stringify(this.$root.models["subscriberList"]));
			} else {
				list = await this.ws.get("subscriberList", "getModels");
			}

			for (let i in list) {
				if (list[i].source) {
					result.push(list[i].source);
				}
			}
			this.$set(this, "models", result);
		},
		resetForm: async function () {
			let resetForm = await this.ws.get("subscriberList", "getEntity");
			if (!resetForm.error) {
				await this.$set(this.subscriberList, "source", resetForm.source);
				this.$set(this.subscriberList, "lists", resetForm.lists);
				this.$set(this.subscriberList, "name", "");
				this.$set(this.subscriberList, "nbSubscribers", 0);
				this.$root.$emit("resetSource");

				let resetSubscribers = await this.ws.post("subscribers", "empty", { "entityType": this.$root.categ, "entityId": this.idEntity });
				if (!resetSubscribers.error) {
					let response = await this.ws.post('subscriberList', 'save', this.subscriberList.source);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						this.$root.$emit("updateSubscriberList", response.idSubscriberList);
					}
				}
			}
		},
		reset: async function () {
			if (confirm(this.messages.get('dsi', 'dsi_subscriber_list_confirm_reset'))) {
				//On reset la source
				let subscriberlist = await this.ws.post("subscriberList", "empty", { "entityType": this.$root.categ, "entityId": this.idEntity, "idSubscriberList": this.subscriberList.source.id });
				if (subscriberlist.error) {
					this.notif.error(this.messages.get('dsi', subscriberlist.errorMessage));
					return;
				}
				this.$root.$emit("replaceSubscriberList", subscriberlist);
				this.sourceKey++;
			}
		}
	}
}
</script>