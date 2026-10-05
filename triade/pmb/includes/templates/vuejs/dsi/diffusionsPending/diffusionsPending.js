import Vue from "vue";
import loader from "../components/loader.vue";
import list from "./components/list.vue";
import preview from "./components/preview.vue";
import item from "./components/item.vue";
import subscriber from "./components/subscriber.vue";
import DsiMessages from "../helper/DsiMessages";
import Const from "../../common/helper/Const.js";

import InitVue from "../../common/helper/InitVue.js";
InitVue(Vue, {
	urlWebservice: $data.url_webservice,
	useLoader: true,
    webserviceCachingOptions: {
        lifetime: 1000
    },
	plugins : {
		"dsiMessages" : new DsiMessages($data.url_webservice),
		"Const" : new Const("dsi", ["tags", "items", "subscriberlist", "views"])
	}
});

new Vue({
	el: "#diffusionsPending",
	components: {
		loader,
		list,
		preview,
		item,
		subscriber
	},
	data: function () {
		return {
			...$data,
			...{
				contentHistoryTypes: {},
				tabActive: 'list',
				tabs: {
					list: {
						title: "msg:dsi_sending_pending",
						component: "list",
						show: true,
						props: () => {
							return {
								list: this.list,
								filters: this.filters,
								entities: this.entities,
								contentHistoryTypes: this.contentHistoryTypes
							}
						}
					},
					preview: {
						title: "msg:diffusion_pending_view",
						component: "preview",
						show: false,
						close: () => this.close('preview'),
						props: () => {
							return {
								history: {},
								contentHistoryTypes: this.contentHistoryTypes
							};
						}
					},
					item: {
						title: "msg:diffusion_pending_item",
						component: "item",
						show: false,
						close: () => this.close('item'),
						props: () => {
							return {
								history: {},
								contentHistoryTypes: this.contentHistoryTypes
							};
						}
					},
					subscriber: {
						title: "msg:diffusion_pending_subscriber",
						component: "subscriber",
						show: false,
						close: () => this.close('subscriber'),
						props: () => {
							return {
								history: {},
								contentHistoryTypes: this.contentHistoryTypes
							};
						}
					}
				}
			}
		}
	},
	created: function () {
        this.ws.get('diffusionsHistory', 'contentHistoryTypes').then((response) => {
			this.contentHistoryTypes = response;
		});
	},
	computed: {
		currentTab: function () {
			return this.tabs[this.tabActive];
		},
		countTab: function () {
			const tabs = Object.values(this.tabs).filter(tab => tab.show === true);
			return tabs.length || 0;
		}
	},
	methods: {
		switchTab: function (tabIndex) {
			this.tabActive = tabIndex || 'list';
		},
		close: function (tabIndex) {
			this.switchTab('list');

			this.tabs.preview.show = false;
			this.tabs.item.show = false;
			this.tabs.subscriber.show = false;
		},
		edit: async function (historyId) {
			let historyIndex = this.list.findIndex((h) => h.id == historyId);
			if(historyIndex == -1) {
				return false;
			}
			let response = await this.ws.get("DiffusionsPending", "contentBuffer/" + historyId);
			if(response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
				return false;
			}

			this.list[historyIndex].contentBuffer = response;

			this.tabs.preview.props = () => {
				return {
					history: this.list[historyIndex],
					entities: this.getFormatedEntities(),
					contentHistoryTypes: this.contentHistoryTypes
				};
			};
			this.tabs.item.props = () => {
				return {
					history: this.list[historyIndex],
					entities: this.getFormatedEntities(),
					contentHistoryTypes: this.contentHistoryTypes
				};
			};
			this.tabs.subscriber.props = () => {
				return {
					history: this.list[historyIndex],
					types: this.subscriberTypes,
					contentHistoryTypes: this.contentHistoryTypes
				};
			};
			this.tabs.preview.show = true;
			this.tabs.item.show = true;
			this.tabs.subscriber.show = true;

			this.switchTab('preview');
		},
		getFormatedEntities: function() {
			let entities = {};
			for(const entity of this.entities) {
				entities[entity.value] = entity.label;
			}
			return entities;
		},
		saveContent: async function(idHistory, contentType, content) {
			let response = await this.ws.post("DiffusionsPending", "saveContentBuffer/" + idHistory + "/" + contentType, content);

			if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
				content.data[0].modified = true;
				this.notif.info(this.messages.get('common', 'success_save'));
			}
		},
		resetContent: async function(idHistory, contentType) {
			if(confirm(this.messages.get('dsi', 'confirm_reset'))) {
				let response = await this.ws.post("DiffusionsPending", "resetContentBuffer/" + idHistory + "/" + contentType);

				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					this.notif.info(this.messages.get('common', 'success_save'));
					return response;
				}
			}
        },
		historySent: function (historyID) {
			for (const tab of this.Const.diffusionsPendingTabs) {
				if (this.tabs[tab].show) {
					const props = this.tabs.preview.props();
					if (props.history.id == historyID) {
						this.close();
						return true;
					}
				}
			}
		}
	}
});