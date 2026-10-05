<template>
    <div>
        <div class="mb-s">
            <form-display :settings="settings" :display_formats="widget_type.display_formats"></form-display>
        </div>

        <div class="mb-s">
            <fieldset class="widget-menu-method widget-fieldset">
                <legend class="widget-legend">{{ messages.get('dashboard', 'form_pmb_menu_legend') }}</legend>

                <div class="mb-s">
                    <label class="widget-form-label visually-hidden" for="widget-menu-opt-module">
                        {{ messages.get('dashboard', 'form_pmb_menu_label') }}
                    </label>
                    <select multiple id="widget-menu-opt-module" v-model="selectedMenu" @change="addMenu" size="10" >
                        <template v-for="menu in configuration">
                            <option class="widget-menu-optgroup" :key="menu.hash" :value="menu.hash" >
                                {{ menu.label}}
                            </option>
                            <optgroup v-for="section in menu.sections" :label="section.label">
                                <option v-for="tab in section.tabs" :key="tab.hash" class="widget-menu-opt-tab" :value="tab.hash">
                                    {{ tab.label }}
                                </option>
                            </optgroup>
                        </template>
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="mb-s">
            <fieldset class="widget-menu-method widget-fieldset">
                <legend class="widget-legend">{{ messages.get('dashboard', 'form_cst_menu_legend') }}</legend>

                <div class="mb-s">
                    <label class="widget-form-label" for="widget-menu-label-custom-module">
                        {{ messages.get('dashboard', 'form_cst_menu_label') }}
                    </label>
                    <input id="widget-menu-label-custom-module" type="text" v-model="customLabel">
                </div>
                <div class="mb-s">
                    <label class="widget-form-label" for="widget-menu-url-custom-module">
                        {{ messages.get('dashboard', 'form_cst_menu_link') }}
                    </label>
                    <input id="widget-menu-url-custom-module" type="url" v-model="customLink" placeholder="https://exemple.com" >
                    <button 
                        :disabled="customLabel === '' || customLink === ''"
                        type="button" 
                        class="bouton" 
                        @click="addCustomLink"
                        :title="messages.get('common', 'submit')"
                        >
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </fieldset>
        </div>
        <div 
            v-if="widget.widgetSettings.menu && widget.widgetSettings.menu.length" 
            class="mb-s">
            
            <label class="widget-form-label" role="presentation">{{ messages.get('dashboard', 'form_active_menu_list') }}</label>
            <ul class="widget-menu-list">
                <li v-for="menu, index in widget.widgetSettings.menu"  :key="index" >
                    <span>{{ menu.label}}</span>
                    <div class="widget-menu-left">
                        <button :disabled="index <= 0" type="button" class="bouton"
                            @click="moveUp(index)" :title=" messages.get('common', 'common_move_up')">
                            <i class="fa fa-arrow-up" aria-hidden="true"></i>
                        </button>
                        <button :disabled="index >= widget.widgetSettings.menu.length - 1" type="button" class="bouton"
                            @click="moveDown(index)" :title=" messages.get('common', 'common_move_down')">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                        </button>
                        <input 
                            type="color" 
                            :id="'widget-color' + index"
                            v-model="menu.backgroundColor"
                            @change="updateColor()">
                        <button 
                            type="button" 
                            class="dashboard-button" 
                            @click="removeMenu(index)"
                            :title="messages.get('common', 'remove')"
                            >
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>

import formDisplay from "../common/formDisplay.vue";
export default {
    props: ["widget", "from", "current_user", "widget_type"],
    components: {
        formDisplay
    },
    data: function(){
        return {
            configuration:null,
            selectedMenu: [],
            customLink: "",
            customLabel: "",
            defaultColor: "#444444" 
        }
    },
    mounted: async function () {
        if(this.widget.widgetSettings) {
            if(!this.widget.widgetSettings.menu) {
                this.$set(this.widget.widgetSettings, 'menu', []);
            }
        }

        this.widget.widgetSettings.menu.forEach(menu => {
            if(menu.hash){
                this.selectedMenu.push(menu.hash);
            }
            
        });

        this.configuration = await this.getConfiguration();

    },
    computed: {
        settings: function() {
            if(this.from == 'widget') {
                return this.widget.widgetSettings;
            }

            return this.widget.dashboardWidgetSettings;
        }
    },
    methods: {
        getConfiguration: async function () {
            let response = await this.ws.post('widget', 'getConfiguration', { source: this.widget_type.source });

            if (response.error) {
                this.notif.error(this.messages.get('dashboard', response.errorMessage));
                return;
            }

            return response;
        },
        addMenu: function(){
            let menuList = [];
            for(let menu of this.widget.widgetSettings.menu) {
                // On ajoute les liens customs
                if(!menu.hash){
                    menuList.push(menu);
                    continue;
                }

                // On ajoute les menus déjà sélectionné en base
                const hashInSelectedMenu = this.findHashInSelectedMenu(menu.hash);;
                if(hashInSelectedMenu){
                    menuList.push(menu);
                    continue;
                }
            }
            for(let hash of this.selectedMenu) {
                // On ajoute les nouvelles sélections de menu
                const hashInMenuList = menuList.find((menu => menu.hash === hash));
                if(!hashInMenuList){
                    const menuInConfiguration = this.findMenuInConfiguration(hash);
                    if(menuInConfiguration){
                        menuInConfiguration.backgroundColor = this.defaultColor;
                        menuList.push(menuInConfiguration);
                    }
                }
            }
            this.$nextTick(() => {
                this.$set(this.widget.widgetSettings, 'menu', menuList);
            });
        },
        findMenuInConfiguration : function(hash){
            for(let menuConfiguration of this.configuration) {    
                if(hash === menuConfiguration.hash) {
                    return menuConfiguration;
                }

                for(let sectionKey of Object.keys(menuConfiguration.sections)) {
                    for(let tab of menuConfiguration.sections[sectionKey].tabs) {
                        if(hash === tab.hash) {
                            return tab;
                        }
                    }
                }
            }
            return null;
        },
        findHashInSelectedMenu : function(hash){
            for(let hashMenu of this.selectedMenu) {
                if(hash === hashMenu) {
                    return hashMenu;
                }
            }
            return null;
        },
        addCustomLink: function() {
            this.widget.widgetSettings.menu.push({
                label: this.customLabel,
                link: this.customLink,
                backgroundColor: this.defaultColor
            })

            this.customLabel = '';
            this.customLink = '';
        },
        updateColor: function() {
            // Prob de réactivité 
            // Du coup on supprime le dernier élément du tableau puis on la re-rajoute
            if (this.widget.widgetSettings.menu.length === 0) return;

            const lastElement = this.widget.widgetSettings.menu.pop();
            this.widget.widgetSettings.menu.push(lastElement);     
        },
        removeMenu: function(index){
            let menu = this.widget.widgetSettings.menu[index];
            this.selectedMenu.splice(this.selectedMenu.indexOf(menu.hash), 1)
            this.widget.widgetSettings.menu.splice(index, 1);

        },
        removeMenuCustom : function(index){
            this.widget.widgetSettings.menuCustom.splice(index, 1);
        },
        checkForm: function() {
            if(!this.widget.widgetSettings.menu.length && !this.widget.widgetSettings.menuCustom.length) {
                this.notif.error(this.messages.get('dashboard', 'form_menu_error_widget'));
                return false;
            }

            return true;
        },
        moveUp: function(i) {
            if(i > 0) {
                const temp = this.widget.widgetSettings.menu[i];
                this.$set(this.widget.widgetSettings.menu, i, this.widget.widgetSettings.menu[i - 1]);
                this.$set(this.widget.widgetSettings.menu, i - 1, temp);
            }
        },
        moveDown: function(i) {
            if(i < this.widget.widgetSettings.menu.length-1) {
                const temp = this.widget.widgetSettings.menu[i];

                this.$set(this.widget.widgetSettings.menu, i, this.widget.widgetSettings.menu[i + 1]);
                this.$set(this.widget.widgetSettings.menu, i + 1, temp);
            }
        }
    },
}
</script>