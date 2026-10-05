<template>
	<div>
		<div class="role-allowed-modules">
			<div class="row">
				<label>{{ messages.get("roles", "role_allowed_modules") }}</label>
			</div>
			<div class="row">
				<div class="rolesgroupcheckbox ui-clearfix ui-flex ui-flex-1-5 ui-flex-top">
					<span v-for="(module, index) in modulesList" :key="index" class='checkbox'>
						<template v-if="modules[module.name]">
							<input :id="'role_module_'+module.name" type="checkbox" v-model="modules[module.name].visible" />
							<label :for="'role_module_'+module.name">{{ module.label }}</label>
						</template>
					</span>
				</div>
			</div>
		</div>
		<div class="role-modules">
			<div class="role-modules">
				<template v-for="(module, index) in modulesList">
					<template v-if="modules[module.name].visible && module.name != 'dashboard'">
						<button type="button"
							@click.self="switchModule(module)" :class="['bouton', moduleActive == index ? 'active-module' : '']">
							{{ module.label }}
						</button>
						<!-- Hack pour avoir un espace entre les tabs -->
						{{ &#32; }}
					</template>
				</template>
				<tabs-list ref="tablist" v-if="moduleActive" :module-name="moduleActive" :tabs="tabs" :sub-tabs="subTabs">
				</tabs-list>
			</div>
			
			
		</div>
	</div>
</template>

<script>
	import tabsList from "./tabsList.vue";

	export default {
		props : {
			modules : {
	            'type' : Object
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
				modulesList: [],
				defaultModule: '',
				moduleActive: ''
			}
		},
		created: function() {
            this.getModulesList();
        },
		components : {
			tabsList
		},
		computed: {
			currentModule: function () {
				
			},
			countCheckedModules: function () {
				return this.checkedModules.length;
			}
		},
		methods: {
			getModulesList: async function() {
                let response = await fetch(this.$root.url_webservice + "roles/list/modules", {
                    method: "GET",
                    cache: 'no-cache'
                });

                let result = await response.json();
                if (result.error) {
                    this.notif.error(this.messages.get('roles', result.errorMessage));
                    return;
                }
                this.modulesList = result.modules;
                this.initModules();
            },
            initModules: function() {
            	for (const [order,module] of Object.entries(this.modulesList)) {
           			if (!this.modules[module.name]) {
            			this.$set(this.modules, module.name, {
							'visible' : 0,
							'privilege' : 0,
							'log' : 0,
						});
           			}
            	}
            },
            switchModule: function (module) {
            	const moduleCopy = Object.assign({}, module);
    			this.moduleActive = moduleCopy.name;
    			this.$nextTick(() => {
    				this.$refs.tablist.setModuleName(moduleCopy.name);
                	this.$refs.tablist.getTabsList();
				});
    		},	
		}
	}
</script>