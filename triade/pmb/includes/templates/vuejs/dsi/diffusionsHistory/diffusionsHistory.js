import Vue from "vue";
import loader from "../components/loader.vue";
import list from "./components/list.vue";
import preview from "./components/preview.vue";
import detail from "./components/detail.vue";
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
	el: "#diffusionsHistory",
	components: {
		loader,
		list,
		preview,
		detail
	},
	data: function () {
		return {
			...$data,
			...{
				contentHistoryTypes: {},
				defaultTab: 'list',
				tabActive: 'list',
				tabs: {
					list: {
						name : "list",
						title: "msg:dsi_history_dashboard",
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
					detail: {
						name : "detail",
						title: "Details",
						component: "detail",
						show: false,
						close: () => this.close('detail'),
						props: () => {
							return {
								list: {},
								filters: this.filters,
								entities: this.entities,
								contentHistoryTypes: this.contentHistoryTypes
							};
						}
					},
					preview: {
						name : "preview",
						title: "msg:preview",
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
				}
			}
		}
	},
	created: function () {
        this.ws.get('diffusionsHistory', 'contentHistoryTypes').then(response => {
			this.contentHistoryTypes = response
		});
	},
	computed: {
		currentTab: function () {
			return this.tabs[this.tabActive];
		},
		countTab: function () {
			const tabs = Object.values(this.tabs).filter(tab => tab.show === true);
			return tabs.length || 0;
		},
		displayedTabs : function() {
			return Object.values(this.tabs).filter(tab => tab.show === true);
		}
	},
	methods: {
		switchTab: function (tabIndex) {
			this.tabActive = tabIndex || this.defaultTab;
			this.tabs[this.tabActive].show = true;
		},
		foundTabOpen: function(tabIndex) {
			const tabsKeys = Object.keys(this.tabs);
			const index = tabsKeys.findIndex(tab => tab == tabIndex);
			if (index === -1) {
				return this.defaultTab;
			}

			const previousTabIndex = index - 1;
			const previousTab = tabsKeys[previousTabIndex] || null;
			if (previousTab !== null && previousTab) {
				return this.tabs[previousTab].show ? previousTab : this.foundTabOpen(previousTab);
			}
			return this.defaultTab;
		},
		close: function (tabIndex) {
			if (typeof this.tabs[tabIndex].close === "undefined") {
				return false;
			}

			if (this.tabActive == tabIndex) {
				const tab = this.foundTabOpen(tabIndex);
				this.switchTab(tab || this.defaultTab);
			}
			this.tabs[tabIndex].show = false;
		},
		preview: function (historyID) {
			const history = this.list.find(item => item.id === historyID);
			if (history) {
				this.tabs.preview.props = () => {
					return {
						history: history,
						contentHistoryTypes: this.contentHistoryTypes
					};
				};
				this.switchTab('preview');
			}
		},
		detail: function (historyID) {
			this.close('preview');

			const history = this.list.find(item => item.id === historyID);
			if (history) {
				let historyList = this.list.filter(h => h.diffusion.id == history.diffusion.id);
				this.tabs.detail.props = () => {
					return {
						list: historyList,
						products : this.products,
						diffusion : history.diffusion,
						contentHistoryTypes: this.contentHistoryTypes
					};
				};
				this.switchTab('detail');
			}
		}
	}
});