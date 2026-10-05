<template>
	<div id="add">
		<div class="dsi-tabs">
		    <div class="dsi-tab-registers">
		        <button type="button"
                        @click="switchTab(1)"
                        :class="[tabActive == 1 ? 'active-tab bouton' : 'bouton']">

					{{ messages.get('dsi', 'diffusion') }}
                    <b class="not-saved">{{ diffusion.id != 0 ? "" : "*"}}</b>
				</button>
				<template v-if="diffusion.id != 0">
					<button type="button"
                            @click="switchTab(2)"
                            :class="[tabActive == 2 ? 'active-tab bouton' : 'bouton']">

						{{ messages.get('dsi', 'diffusion_channel') }}
                        <b class="not-saved">{{ diffusion.numChannel != 0 ? "" : "*"}}</b>
					</button>
					<button v-if="diffusion.numChannel"
                            type="button" @click="switchTab(3)"
                            :class="[tabActive == 3 ? 'active-tab bouton' : 'bouton']">

						{{ messages.get('dsi', 'diffusion_content') }}
                        <b class="not-saved">{{ diffusion.numView != 0 && diffusion.numItem != 0 ? "" : "*"}}</b>
					</button>
					<button v-if="channelIsCompatibleWithAttachments"
                            type="button" @click="switchTab(4)"
                            :class="[tabActive == 4 ? 'active-tab bouton' : 'bouton']">

						{{ messages.get('dsi', 'channel_form_attachments') }}
                        <b class="not-saved">{{ diffusion.numView != 0 && diffusion.numItem != 0 ? "" : "*"}}</b>
					</button>
					<button v-if="showRecipientsTab"
							type="button"
                            @click="switchTab(5)"
                            :class="[tabActive == 5 ? 'active-tab bouton' : 'bouton']">

						{{ messages.get('dsi', 'diffusion_recipients') }}
                        <b class="not-saved">{{ diffusion.numSubscriberList != 0 ? "" : "*"}}</b>
					</button>
					<button type="button"
                            @click="switchTab(6)"
                            :class="[tabActive == 6 ? 'active-tab bouton' : 'bouton']">

						{{ messages.get('dsi', 'diffusion_triggers') }}
                        <b class="not-saved">{{ diffusion.events.length != 0 ? "" : "*"}}</b>
					</button>
                    <button v-if="tabsView"
                            type="button"
                            @click="tabActive = 7"
                            :class="[tabActive == 7 ? 'active-tab bouton' : 'bouton']">

                        {{ tabsView.name ? tabsView.name : "Sans nom" }}
                        <span type="button"
                              style="cursor: pointer"
                              @click.stop="closeViewTab"
                              :title="messages.get('dsi', 'view_wysiwyg_close_tab')">

                            <i class="fa fa-times" aria-hidden="true"></i>
                        </span>
                    </button>
				</template>
		    </div>
		    <div class="dsi-tab-bodies">
		        <div class="dsi-content" v-show="tabActive == 1">
		        	<formDiffusion
                        :channels="channels"
                        :status="status"
                        :diffusion="diffusion"
                        :products="products"
						:empr="empr">
                    </formDiffusion>
		        </div>
		        <div class="dsi-content" v-show="tabActive == 2">
					<formDiffusionChannel :diffusion="diffusion"></formDiffusionChannel>
		        </div>
				<div class="dsi-content" v-show="tabActive == 3">
					<formDiffusionView 
						:diffusion="diffusion"
						:channelCompatibility="channelCompatibility">
					</formDiffusionView>
		        </div>
				<div class="dsi-content" v-show="tabActive == 4">
					<formDiffusionAttachment 
						:diffusion="diffusion"
						:channelCompatibility="channelCompatibility">
					</formDiffusionAttachment>
		        </div>
				<div class="dsi-content" v-show="tabActive == 5">
					<formSubscriberListContainer
                        :subscriberList="diffusion.subscriberList"
                        :channel-type="diffusion.channel.type"
                        :id-entity="diffusion.id">
                    </formSubscriberListContainer>
		        </div>
		        <div class="dsi-content" v-show="tabActive == 6">
					<formDiffusionEvent :diffusion="diffusion"></formDiffusionEvent>
		        </div>
		        <div v-if="tabsView && tabActive == 7" class="dsi-content">
					<formView
                        :types="viewTypes"
                        :view="tabsView"
						:item="fromWysiwygViewId && fromWysiwygViewId.item ? fromWysiwygViewId.item : 0"
                        :entities="entityTypes"
                        :is_model="false"
                        :isTabForm="true"
						:channelCompatibility="channelCompatibility"
						:from-wysiwyg-view-id="fromWysiwygViewId">
                    </formView>
		        </div>
		    </div>
		</div>
	</div>
</template>

<script>
	import formDiffusion from "./formDiffusion.vue";
	import formDiffusionView from "./formDiffusionView.vue";
	import formDiffusionAttachment from "./formDiffusionAttachment.vue";
	import formDiffusionChannel from "./formDiffusionChannel.vue";
	import formDiffusionEvent from "./formDiffusionEvent.vue";
	import formSubscriberListContainer from "@dsi/subscriberList/components/formSubscriberListContainer.vue";
	import formView from "@dsi/views/components/formView.vue";

	export default {
		props : ["channels", "status", "diffusion", "products", "categ", "empr"],
		data: function () {
			return {
			    tabActive: this.getTab(),
                tabsView: "",
                viewTypes: [],
                entityTypes: [],
				fromWysiwygViewId: {},
				openerTabView: null,
			}
		},
		created: async function() {
			this.getListners();
			this.fetchData();
		},
		components: {
		    formDiffusion,
		    formDiffusionView,
		    formDiffusionAttachment,
			formDiffusionChannel,
			formDiffusionEvent,
			formSubscriberListContainer,
			formView
		},
		computed: {
			showRecipientsTab: function() {
				if (!this.diffusion.channel.type) {
					return false;
				}
				let channel = this.channels.find((c)=> c.id == this.diffusion.channel.type)
				return (channel && channel.compatibility.subscriber_list);
			},
			channelCompatibility: function() {
				if(!this.diffusion.numChannel) {
					return null;
				}

				let channelSelected = this.channels.find(channel => channel.id == this.diffusion.channel.type);

				if(!channelSelected) {
					return null;
				}

				return channelSelected;
			},
			channelIsCompatibleWithAttachments: function() {
				let channelSelected = this.channelCompatibility;
				if(!channelSelected) {
					return false;
				}

				// Cas pour le canal de type mail
				if(this.diffusion.channel.type == 1) {
					if(this.diffusion.channel.settings.mail_choice != "mail_attachments") {
						return false;
					}
				}
				
				if(channelSelected.compatibility) {
					if(channelSelected.compatibility.attachments) {
						if(channelSelected.compatibility.attachments.view.length > 0) {
							return true;
						}
					}
				}

				return false;
			}
		},
		methods: {
			getListners: function() {
				this.$root.$on("updateSubscriberList", async (idSubscriberList)=>{
					this.diffusion.numSubscriberList = idSubscriberList;

					this.$set(this.diffusion.subscriberList.source, "id", idSubscriberList);
					this.$set(this.diffusion.subscriberList.source, "idSubscriberList", idSubscriberList);

					let response = await this.ws.post('diffusions', 'save', this.diffusion);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					}else {
						this.notif.info(this.messages.get('dsi', 'diffusion_form_subscriber_list_success'));
					}
				});
                this.$root.$on("addTabView", this.addTabView);
                this.$root.$on("closeViewTab", this.closeViewTab);
				this.$root.$on("replaceSubscriberList", ($event) => {
					this.$set(this.diffusion, "numSubscriberList", $event.source.idSubscriberList);
					this.$set(this.diffusion, "subscriberList", $event);
				});
			},
		    switchTab: function(tab) {
				if(this.diffusion.id != 0) {
					this.$set(this, "tabActive", tab);
					sessionStorage.setItem("tabDiffusionActive", JSON.stringify({id: this.diffusion.id, tab: tab}));
				}
		    },
			getTab: function() {
				if(sessionStorage.getItem("tabDiffusionActive")) {
					let obj = JSON.parse(sessionStorage.getItem("tabDiffusionActive"));
					if(obj.id == this.diffusion.id) {
						return obj.tab;
					}
				}
				return 1;
			},
            // Récupère une instance d'item vide
            getEmptyView: async function() {
                let response = await this.ws.get("views", 'getEmptyInstance');

                if (response.error) {
                    this.notif.error(this.messages.get('dsi', response.errorMessage));
                } else {
                    return response;
                }
                return null;
            },
            addTabView: async function(event) {
				this.openerTabView = this.tabActive;
				if(event.blockId) {
					this.fromWysiwygViewId = { block: event.blockId, view: this.diffusion.view.id, item : event.item };
				}

                if(event.view) {
                    this.tabsView = event.view;
                } else {
                    this.tabsView = await this.getEmptyView();
                }
                this.tabActive = 7;
            },
            closeViewTab: async function(event) {
                this.tabsView = "";

				if (this.tabActive == 7) {
					this.switchTab(this.openerTabView || 3);
					this.openerTabView = null;
				}
            },
			fetchData: async function() {
				const promises = [];

				// Vérifie si les types d'entités ne sont pas déjà chargés
				const shouldFetchEntities = !this.$root.entities;
				if (shouldFetchEntities) {
					promises.push(this.ws.get('diffusions', 'getEntityList'));
				}

				// Vérifie si les types de vues ne sont pas déjà chargés
				const shouldFetchViews = !this.$root.types?.views;
				if (shouldFetchViews) {
					promises.push(this.ws.get('views', 'getTypeListAjax'));
				}

				// Exécute les requêtes en parallèle
				const results = await Promise.all(promises);

				// Index pour récupérer les résultats dans le bon ordre
				let index = 0;

				if (shouldFetchEntities) {
					this.entityTypes = results[index++];
				} else {
					this.entityTypes = JSON.parse(JSON.stringify(this.$root.entities));
				}

				if (shouldFetchViews) {
					this.viewTypes = results[index++];
				} else {
					this.viewTypes = JSON.parse(JSON.stringify(this.$root.types.views));
				}
			}
		}
	}
</script>