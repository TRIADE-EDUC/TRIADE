<template>
    <form-modal 
        :title="title"
        formClass="widget-form"
        :showCancel="true"
        :showSave="true"
        :showDelete="isDeletable"
        :showDuplicate="isDeletable"
        @close="close()"
        @show="$emit('show')"
        @submit="submit"
        @remove="remove"
        @duplicate="duplicate"
        id="formWidget"
        ref="formWidget">

        <div class="widget-form-modal">
            <template v-if="currentWidget.numUser == current_user">
                <!-- Nom du widget -->
                <div>
                    <label class="widget-form-label" for="widget-name">
                        {{ messages.get('dashboard', 'form_name_dashboard') }}
                    </label>
                    <input type="text" id="widget-name" name="widget-name" v-model="currentWidget.widgetName" required>
                </div>

                <!-- Propriétaire -->
                <div v-if="currentWidget.idWidget">
                    <label class="widget-form-label" for="widget-owner">
                        {{ messages.get('dashboard', 'form_owner_dashboard') }}
                    </label>
                    <span>{{ users[currentWidget.numUser] }}</span>
                </div>
        
                <!-- Edition du widget -->
                <div>
                    <label class="widget-form-label" for="widget-editable">
                        {{ messages.get('dashboard', 'form_edit_dashboard') }}
                        <i 
                            class="fa fa-info-circle" 
                            :title="messages.get('dashboard', 'form_edit_title_widget')" 
                            aria-hidden="true">
                        </i>
                    </label>
                    <input type="checkbox" id="widget-editable" name="widget-editable" v-model="currentWidget.widgetEditable">
                </div>
                
                <div>
                    <label class="widget-form-label" for="widget-shared">
                        {{ messages.get('dashboard', 'form_share_widget') }}
                    </label>
                    <input 
                        type="checkbox" 
                        id="widget-shared" 
                        name="widget-shared" 
                        :title="currentWidget.widgetShared ? messages.get('dashboard', 'form_shared_widget') : ''"
                        :disabled="currentWidget.widgetShared ? true : false"
                        v-model="currentWidget.widgetShareable">
                </div>
    
                <!-- Type du widget -->
                <div>
                    <label class="widget-form-label" for="widget-type">
                        {{ messages.get('dashboard', 'form_widget_type_label') }}
                    </label>
                    <select name="widget-type" id="widget-type" v-model="currentWidget.widgetType" required>
                        <option value="" disabled>{{ messages.get('dashboard', 'form_widget_type_select') }}</option>
                        <option v-for="widgetType in widget_types" :value="widgetType.type">
                            {{ widgetType.msg.name }}
                        </option>
                    </select>
                </div>
            </template>

            <div v-if="currentWidget.numUser != current_user">
                <div>
                    <label class="widget-form-label" for="widget-name">
                        {{ messages.get('dashboard', 'form_name_dashboard') }}
                    </label>
                    <span>{{ currentWidget.widgetName }}</span>
                </div>
                <!-- Propriétaire -->
                <div v-if="currentWidget.idWidget">
                    <label class="widget-form-label" for="widget-owner">
                        {{ messages.get('dashboard', 'form_owner_dashboard') }}
                    </label>
                    <span>{{ users[currentWidget.numUser] }}</span>
                </div>
            </div>
            <!-- Formulaire du type de widget -->
            <div v-if="currentWidget.widgetType">
                <component 
                    :is="currentWidgetType" 
                    :widget="currentWidget" 
                    :from="from" 
                    :current_user="current_user"
                    :widget_type="widgetType"
                    :ref="currentWidgetType">
                </component>
            </div>

        </div>
    </form-modal>
</template>

<script>
    import formModal from '@/common/components/FormModal.vue';
    import widgetFormNote from './widgets/note/widgetFormNote.vue';
    import widgetFormCounter from './widgets/counter/widgetFormCounter.vue';
    import widgetFormIndicator from './widgets/indicator/widgetFormIndicator.vue';
    import widgetFormRss from './widgets/rss/widgetFormRss.vue';
    import widgetFormAlert from './widgets/alert/widgetFormAlert.vue';
    import widgetFormStat from './widgets/stat/widgetFormStat.vue';
    import widgetFormMenu from './widgets/menu/widgetFormMenu.vue';
    //import widgetFormSnake from './widgets/snake/widgetFormSnake.vue';

    // window.easterEgg = () => {
    //     const widgetTypeSelector = document.getElementById("widget-type");

    //     if(widgetTypeSelector) {
    //         const snake = document.createElement("option");

    //         snake.value = "snake";
    //         snake.innerText = "Snake Game";

    //         widgetTypeSelector.appendChild(snake);
    //     }
    // };

    export default {
        props: ["widget", "from", "dashboard", "widget_types", "current_user", "users"],
        components: {
            formModal,
            widgetFormNote,
            widgetFormCounter,
            widgetFormIndicator,
            widgetFormRss,
            widgetFormAlert,
            widgetFormStat,
            widgetFormMenu
            //widgetFormSnake
        },
        data: function() {
            return {
                currentWidget: null, // Widget courant
                emptyWidget: { // Widget vide
                    idWidget: 0,
                    widgetName: "",
                    widgetType: "",
                    widgetEditable: true,
                    widgetShareable: false,
                    numUser: this.current_user,
                    widgetSettings: {}
                }
            }
        },
        created: function() {
            this.getCurrentWidget();
        },
        computed: {

            /**
             * Retourne le titre en fonction de l'existence du widget
             *
             * @return {string}
             */
            title: function() {
                if(this.widget && this.widget.idWidget) {
                    return this.messages.get('dashboard', 'param_widget');
                }
                return this.messages.get('dashboard', 'add_form_widget');
            },

            /**
             * Retourne le type de widget courant
             *
             * @return {string}
             */
            currentWidgetType: function() {
                return `widget-form-${this.currentWidget.widgetType}`;
            },

            /**
             * Retourne si le widget courant est supprimable
             *
             * @return {boolean}
             */
            isDeletable: function() {
                if(this.currentWidget.idWidget && this.currentWidget.numUser == this.current_user && this.from == 'widget') {
                    return true;
                }

                return false;
            },
            isDuplicable: function() {
                if(this.currentWidget.idWidget && this.from == 'widget') {
                    return true;
                }

                return false;
            },
            widgetType: function() {
                if(this.currentWidget.widgetType) {
                    return this.widget_types.find(widgetType => widgetType.type == this.currentWidget.widgetType);
                }

                return null;
            }
        },
        methods: {

            /**
             * Affiche le formulaire de widget
             *
             * @return {void}
             */
            show: function() {
                this.getCurrentWidget();
                this.$refs.formWidget.show();
            },

            /**
             * Ferme le formulaire de widget
             *
             * @return {void}
             */
            close: function() {
                this.currentWidget = this.cloneObject(this.emptyWidget);
            },

            cloneObject: function(object) {
                return JSON.parse(JSON.stringify(object));
            },

            /**
             * Récupère le widget courant
             *
             * @return {void}
             */
            getCurrentWidget: function() {
                if(this.from == 'dashboard') {
                    this.currentWidget = this.dashboard.widgets.find(widget => widget.idWidget == this.widget.idWidget);
                    return;
                }

                if(this.widget && this.widget.idWidget) {
                    this.currentWidget = this.widget;
                } else {
                    this.currentWidget = this.cloneObject(this.emptyWidget);
                }
            },

            /**
             * Enregistre le widget
             *
             * @return {void}
             */
            submit: async function() {
                const widgetForm = this.$refs[this.currentWidgetType];
                if(widgetForm && widgetForm.checkForm) {
                    if(!widgetForm.checkForm()) {
                        return;
                    }
                }

                if(this.from == 'widget') {
                    this.saveWidget();
                    return;
                }

                this.saveDashboardWidget();
                this.close();
            },

            saveWidget: async function() {
                let response = await this.ws.post('widget', 'save', this.currentWidget);

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                } else {
                    if(!this.currentWidget.idWidget) {
                        this.$emit("addWidget", this.currentWidget);
                    }
                    this.currentWidget.idWidget = response.idWidget;
                    this.notif.info(this.messages.get('common', 'success_save'));

                    this.$refs.formWidget.close();
                }
            },

            saveDashboardWidget: async function() {
                const position = this.dashboard.layout.find(w => w.i == this.currentWidget.idWidget);
                
                if(!position) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    return;
                }

                this.currentWidget.dashboardWidgetSettings.position = position;

                let response = await this.ws.post('dashboard', 'saveDashboardWidget', { idDashboard: this.dashboard.idDashboard, widget: this.currentWidget });
                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                } else {
                    // if(!this.currentWidget.idWidget) {
                    //     //this.$emit("addWidget", this.currentWidget);
                    // }
                    //this.currentWidget.idWidget = response.idWidget;
                    this.notif.info(this.messages.get('common', 'success_save'));
                    this.$refs.formWidget.close();
                }
            },

            /**
             * Supprime le widget
             *
             * @return void
             */
            remove: async function() {
                if(confirm(this.messages.get('common', "confirm_delete"))) {
                    let response = await this.ws.post('widget', 'delete', { idWidget: this.currentWidget.idWidget });
    
                    if (response.error) {
                        this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    } else {
                        this.$emit("removeWidget", this.currentWidget.idWidget);
                        
                        this.notif.info(this.messages.get('common', 'success_delete'));
                        this.$refs.formWidget.close();
                    }
                }
            },

            /**
             * Duplique un widget
             *
             * @return {void}
             */
             duplicate: async function() {
                let response = await this.ws.post('widget', 'duplicate', {
                     idWidget: this.currentWidget.idWidget 
                });

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    return;
                }

                this.$emit("duplicateWidget");
                
                this.notif.info(this.messages.get('common', 'successful_operation'));
                this.close();
            }
        }
    }

</script>