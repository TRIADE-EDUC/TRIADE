<template>
	<div class="role-tabs">
		<template v-for="(sectionTabs, sectionName) in tabsList" >
			<accordion :title="sectionName" :index="sectionName" :key="sectionName" :expanded="true">
				<fieldset>
					<legend class="visually-hidden"></legend>
					<ul v-if="tabs[moduleName]" class="roles-group-subtabs">
						<li v-for="(tab, index) in sectionTabs" :key="index" style="display:block" class="checkbox">
							<template v-if="tabs[moduleName][getKeyTab(tab)]">
								<input type="checkbox" :id="'role_tabs_'+tab.label_code" v-model="tabs[moduleName][getKeyTab(tab)].visible" />
								<button type="button" class="bouton" @click="actionTab('privilege', tab)" style="display:none">
									<i v-if="tabs[moduleName][getKeyTab(tab)].privilege" class="fa fa-lock" aria-hidden="true"></i>
									<i v-else class="fa fa-unlock" aria-hidden="true"></i>
								</button>
								<label :for="'role_tabs_'+tab.label_code">{{ tab.label }}</label>
								
								<ul v-if="subTabsList[tab.label_code]" class="rolesgroupcheckbox ui-clearfix ui-flex ui-flex-1-5 ui-flex-top">
									<li v-for="(subtab, subIndex) in subTabsList[tab.label_code]" :key="subIndex" style="display:inline-block" class="checkbox">
										<template v-if="subTabs[moduleName][getKeySubTab(subtab)]">
											<input type="checkbox" :id="'role_tabs_'+tab.label_code+'_subtab_'+subtab.label_code" v-model="subTabs[moduleName][getKeySubTab(subtab)].visible" true-value="1" false-value="0" />
											<button type="button" class="bouton" @click="actionSubTab('privilege', subtab)" style="display:none">
												<i v-if="subTabs[moduleName][getKeySubTab(subtab)].privilege" class="fa fa-lock" aria-hidden="true"></i>
												<i v-else class="fa fa-unlock" aria-hidden="true"></i>
											</button>
											<label :for="'role_tabs_'+tab.label_code+'_subtab_'+subtab.label_code" :title="subtab.title">{{ subtab.label }}</label>
										</template>
									</li>
								</ul>
							</template>
						</li>
					</ul>
				</fieldset>
			</accordion>
		</template>
	</div>
</template>

<script>
	import accordion from "../../../../common/accordion/accordion.vue";

	export default {
		props : {
			moduleName : {
				'type' : String
			},
	        tabs : {
	            'type' : Object
	        },
	        subTabs : {
	            'type' : Object
	        }
		},
		data: function () {
			return {
				tabsList: [],
				subTabsList: []
			}
		},
		created: function() {
            this.getTabsList();
        },
		components : {
			accordion,
		},
		computed: {
			currentStepType : function() {
				return this.stepsTypes.find(el => el.namespace == this.currentStep.stepType)
			}
		},
		methods: {
			getTabsList: async function() {
                let response = await fetch(this.$root.url_webservice + "roles/list/tabs/" + this.moduleName, {
                    method: "GET",
                    cache: 'no-cache'
                });

                let result = await response.json();
                if (result.error) {
                    this.notif.error(this.messages.get('roles', result.errorMessage));
                    return;
                }
                this.tabsList = result.tabs;
               	this.initTabs();
                this.getSubTabsList();
            },
            getKeyTab: function(tab) {
            	return tab.categ+'/'+tab.sub+'/'+tab.url_extra;
            },
            initTabs: function() {
				if (!this.tabs[this.moduleName]) {
            		this.$set(this.tabs, this.moduleName, {});
				}
            	for (const [sectionName,sectionTabs] of Object.entries(this.tabsList)) {
            		sectionTabs.forEach((tab) => {
            			let keyTab = this.getKeyTab(tab);
						if (!this.tabs[this.moduleName][keyTab]) {
							this.$set(this.tabs[this.moduleName], keyTab, {
								'visible' : 1,
								'privilege' : 0,
								'log' : 0,
							});
						}
            		});
            	}
            	this.$forceUpdate();
            },
            getSubTabsList: async function() {
            	let response = await fetch(this.$root.url_webservice + "roles/list/subtabs/" + this.moduleName, {
                    method: "GET",
                    cache: 'no-cache'
                });

                let result = await response.json();
                if (result.error) {
                    this.notif.error(this.messages.get('roles', result.errorMessage));
                    return;
                }
                this.subTabsList = result.subtabs;
               	this.initSubTabs();
            },
            getKeySubTab: function(subtab) {
            	return subtab.categ+'/'+subtab.sub+'/'+subtab.url_extra;
            },
            initSubTabs: function() {
				if (!this.subTabs[this.moduleName]) {
            		this.$set(this.subTabs, this.moduleName, {});
				}
            	for (const [tabLabelCode,tabs] of Object.entries(this.subTabsList)) {
            		tabs.forEach((subtab) => {
            			let keySubTab = this.getKeySubTab(subtab);
            			if (!this.subTabs[this.moduleName][keySubTab]) {
	            			this.$set(this.subTabs[this.moduleName], keySubTab, {
								'visible' : 1,
								'privilege' : 0,
								'log' : 0,
							});
            			}
					});
            	}
            },
            actionTab: function(action, tab) {
            	let keyTab = this.getKeyTab(tab);
				if(parseInt(this.tabs[this.moduleName][keyTab][action])) {
            		this.$set(this.tabs[this.moduleName][keyTab], action, 0);
            	} else {
            		this.$set(this.tabs[this.moduleName][keyTab], action, 1);
            	}
            	this.$forceUpdate();
            },
            actionSubTab: function(action, subtab) {
            	let keySubTab = this.getKeySubTab(subtab);
				if(parseInt(this.subTabs[this.moduleName][keySubTab][action])) {
            		this.$set(this.subTabs[this.moduleName][keySubTab], action, 0);
            	} else {
            		this.$set(this.subTabs[this.moduleName][keySubTab], action, 1);	
            	}
            	this.$forceUpdate();
            },
            setModuleName: function(name) {
            	this.$set(this, "moduleName", name);
            }
		}
	}
</script>