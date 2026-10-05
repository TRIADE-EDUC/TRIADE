<template>
    <div class="dashboard">
        <div class="dashboard-menu">
            <div class="dashboard-menu-left">
                <!-- Sélecteur de tableau de bord à afficher -->
                <select v-if="dashboards.length" v-model="selectedDashboard" :disabled="editMode" @change="$forceUpdate()">
                    <option v-for="(dashboard, index) in dashboards" :value="index" :key="index">
                        {{ dashboard.dashboardName }}
                    </option>
                </select>

                <span 
                    v-if="dashboards.length && selectedDashboard >= 0"
                    class="dashboard-owner" 
                    :title="`${messages.get('dashboard', 'form_owner_dashboard')} ${users[dashboards[selectedDashboard].numUser]}`">

                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                </span>

                <!-- Bouton d'ajout d'un tableau de bord -->
                <input 
                    type="button" 
                    class="bouton" 
                    @click="openDashboardForm(false)" 
                    :value="messages.get('dashboard', 'add_dashboard')">

                <!-- Bouton d'import d'un tableau de bord -->
                <!-- <input 
                    type="button" 
                    class="bouton"
                    :value="messages.get('dashboard', 'import_dashboard')"> -->

            </div>
            <div class="mg-left-auto">

                <!-- Bouton d'ajout d'un widget -->
                <input 
                    v-if="editMode"
                    type="button" 
                    class="bouton"
                    @click="showWidgetAdd = !showWidgetAdd"
                    :value="messages.get('dashboard', 'add_widget')">

                <!-- Bouton d'édition du paramétrage du tableau de bord -->
                <input 
                    v-if="editMode"
                    type="button" 
                    class="bouton"
                    @click="openDashboardForm(true)"
                    :disabled="this.current_user !== dashboards[selectedDashboard].numUser"
                    :value="messages.get('dashboard', 'param_dashboard')">


                <!-- Bouton de sauvegarde du placement -->
                <input 
                    v-if="editMode"
                    type="button" 
                    class="bouton" 
                    @click="saveLayout" 
                    :value="messages.get('common', 'submit')">

                <!-- Bouton d'annulation de l'édition -->
                <input 
                    v-if="editMode"
                    type="button" 
                    class="bouton" 
                    @click="editMode = !editMode"  
                    :value="messages.get('common', 'cancel')">

                <!-- Bouton du passage en mode édition -->
                <input 
                    v-if="!editMode && dashboards.length"
                    type="button" 
                    class="bouton" 
                    @click="editMode = !editMode" 
                    :disabled="!dashboards[selectedDashboard].dashboardEditable &&
                        this.current_user !== dashboards[selectedDashboard].numUser"
                    :value="messages.get('dashboard', 'edit_mode')">
            </div>
        </div>

        <!-- Affichage du tableau de bord -->
        <dashboard-layout 
            v-if="dashboards.length"
            :dashboard="dashboards[selectedDashboard]" 
            :current_user="current_user"
            ref="dashboardLayout"
            :editMode="editMode"
            :widget_types="widget_types"
            @editWidget="editWidget">
        </dashboard-layout>

        <!-- Affichage de la modal du formulaire du tableau de bord -->
        <dashboard-form 
            :dashboard="formUpdateDashboard ? dashboards[selectedDashboard] : null"
            :groups="groups"
            :current_user="current_user"
            ref="dashboardForm"
            @addDashboard="addDashboard"
            @removeDashboard="removeDashboard"
            @duplicateDashboard="duplicateDashboard">
        </dashboard-form>

        <!-- Affichage de la modal du formulaire de widget -->
        <widget-form 
            :widget="formUpdateWidget ? editedWidget : null"
            :from="formFromWidget"
            :dashboard="dashboards[selectedDashboard]"
            :widget_types="widget_types"
            :current_user="current_user"
            :users="users"
            ref="widgetForm"
            @addWidget="addWidget"
            @removeWidget="removeWidget"
            @duplicateWidget="duplicateWidget">
        </widget-form>

        <!-- Affichage du formulaire d'ajout de widget -->
        <widgetAdd 
            v-if="showWidgetAdd && editMode" 
            :dashboard="dashboards[selectedDashboard]" 
            :widgets="widgets"
            :current_user="current_user"
            @editWidget="editWidget"
            @close="showWidgetAdd = false">
        </widgetAdd>
    </div>
</template>

<script>
    import dashboardLayout from "./dashboardLayout.vue";
    import dashboardForm from "./dashboardForm.vue";
    import widgetForm from "./widgetForm.vue";
    import widgetAdd from "./widgetAdd.vue";

    export default {
        props: [
            "dashboards", // Liste des tableaux de bord
            "widgets", // Liste des widgets
            "widget_types", // Liste des types de widgets
            "groups", // Liste des groupes d'utilisateurs
            "users", // Liste des d'utilisateurs
            "current_user", // Identifiant de l'utilisateur connecté
            "current_group" // Identifiant du groupe d'utilisateur connecté
        ],
        components: {
            dashboardLayout,
            dashboardForm,
            widgetAdd,
            widgetForm
        },
        data: function() {
            return {
                editMode: false, // Mode d'édition

                selectedDashboard: 0, // Index du tableau de bord selectionné
                selectedWidget: 0, // Identifiant du widget selectionné

                formUpdateDashboard: false, // Affichage du formulaire de modification / création de tableau de bord
                formUpdateWidget: false, // Affichage du formulaire de modification / création de widget
                formFromWidget: "widget",

                showWidgetAdd: false // Affichage du formulaire d'ajout de widget
            }
        },
        created: function() {
            this.$root.$on("saveWidget", this.saveWidget);
            this.$root.$on("refreshWidget", this.refreshWidget);
        },
        computed: {

            /**
             * Renvoie le widget modifié du tableau de widgets du tableau de bord sélectionné.
             *
             * @return {object} Le widget modifié, ou null si le tableau de bord ou le tableau de widgets est indéfini.
             */
            editedWidget: function() {
                if(this.widgets.length) {
                    return this.widgets.find(
                        widget => widget.idWidget == this.selectedWidget
                    );
                }

                return null;
            },
            // editedDashboardWidget: function() {
            //     if(this.dashboards[this.selectedDashboard].dashboardWidgets.length) {
            //         return this.dashboards[this.selectedDashboard].dashboardWidgets.find(
            //             dashboardWidget => dashboardWidget.numWidget == this.selectedWidget
            //         );
            //     }

            //     return null;
            // }      
        },
        methods: {

            /**
             * Fonction pour ouvrir le formulaire de tableau de bord.
             *
             * @param {boolean} update - Indicateur pour indiquer si le formulaire est pour une modification
             * @return {void} 
             */
            openDashboardForm: function(update = false) {
                if(update) {
                    this.formUpdateDashboard = true;
                } else {
                    this.formUpdateDashboard = false;
                }

                this.$refs.dashboardForm.show();
            },

            /**
             * Fonction pour ouvrir le formulaire de widget.
             *
             * @param {boolean} update - Indicateur pour indiquer si le formulaire est pour une modification
             * @return {void} 
             */
            openWidgetForm: function(update = false) {
                if(update) {
                    this.formUpdateWidget = true;
                } else {
                    this.formUpdateWidget = false;
                }

                this.$nextTick(() => {
                    this.$refs.widgetForm.show();
                });
            },

            /**
             * Editer un widget.
             *
             * @param {type} id - Identifiant du widget
             * @return {void}
             */
            editWidget: function(id, from = "widget") {
                this.selectedWidget = id;
                this.formFromWidget = from;

                this.openWidgetForm(true);
            },

            /**
             * Sauvegarde la mise en page (layout) du tableau de bord.
             * 
             * @return {void}
             */
            saveLayout: async function() {
                this.editMode = false;

                let response = await this.ws.post('dashboard', 'saveLayout', { 
                    layout: this.dashboards[this.selectedDashboard].layout, 
                    dashboardWidgets: this.getDashboardWidgetSettingsList(),
                    idDashboard: this.dashboards[this.selectedDashboard].idDashboard 
                });

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                } else {
                    this.notif.info(this.messages.get('common', 'success_save'));
                }
            },

            getDashboardWidgetSettingsList: function() {
                let dashboardWidgets = [];

                this.dashboards[this.selectedDashboard].widgets.forEach(widget => {
                    dashboardWidgets.push({
                        numWidget: widget.idWidget,
                        numDashboard: this.dashboards[this.selectedDashboard].idDashboard,
                        dashboardWidgetSettings: widget.dashboardWidgetSettings
                    });
                });

                return dashboardWidgets;
            },

            /**
             * Ajouter un tableau de bord.
             *
             * @param {object} dashboard - Tableau de bord a ajouter
             * @return {void}
             */
            addDashboard: function(dashboard) {
                this.dashboards.push(dashboard);

                this.editMode = false;
                this.selectedDashboard = this.dashboards.length-1;
            },

            /**
             * Supprimer un tableau de bord.
             *
             * @param {integer} idDashboard - Identifiant du tableau de bord
             * @return {void}
             */
            removeDashboard: function(idDashboard) {
                this.editMode = false;
                
                const index = this.dashboards.map(dashboard => dashboard.idDashboard).indexOf(idDashboard);
                this.dashboards.splice(index, 1);
            },

            /**
             * Duplique un tableau de bord.
             *
             * @return {void}
             */
            duplicateDashboard: function() {
                this.editMode = false;

                window.location.reload();
            },
            

            /**
             * Ajouter un widget.
             *
             * @param {object} widget - Widget a ajouter
             * @return {void}
             */
            addWidget: function(widget) {
                this.widgets.push(widget);
            },

            /**
             * Supprimer un widget.
             *
             * @param {integer} idWidget - Identifiant du widget
             * @return {void}
             */
            removeWidget: function(idWidget) {
                // On retire le widget de la liste de widgets
                let index = this.widgets.map(widget => widget.idWidget).indexOf(idWidget);
                if(index !== -1) {
                    this.widgets.splice(index, 1);
                }

                for(let i = 0; i < this.dashboards.length; i++) {
                    const dashboard = this.dashboards[i];

                    // On retire le widget de la liste de widgets du dashboard
                    index = dashboard.widgets.map(widget => widget.idWidget).indexOf(idWidget);
                    if(index !== -1) {
                        dashboard.widgets.splice(index, 1);
                    }

                    // On retire le widget du layout du dashboard
                    index = dashboard.layout.map(widget => widget.i).indexOf(idWidget.toString());
                    if(index !== -1) {
                        dashboard.layout.splice(index, 1);
                    }
                }
            },
            saveWidget: async function(widget) {
                let response = await this.ws.post('dashboard', 'saveDashboardWidget', { 
                    idDashboard: this.dashboards[this.selectedDashboard].idDashboard,
                    widget: widget
                });

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                } else {
                    this.notif.info(this.messages.get('common', 'success_save'));
                }
            },

            /**
             * Duplique un widget.
             *
             * @return {void}
             */
            duplicateWidget: function() {
                window.location.reload();
            },

            refreshWidget: async function(widget, loader = false) {
                const formWidget = document.getElementById("formWidget").firstChild;
                if(formWidget.style.display == "none") {

                    let post = new URLSearchParams();
                    post.append("data", JSON.stringify({ 
                        idWidget: widget.idWidget,
                        idDashboard: this.dashboards[this.selectedDashboard].idDashboard 
                    }));

                    let options = {
                        method: "POST",
                        cache: 'no-cache',
                        body: post
                    };

                    if (loader) {
                        this.showLoader();
                    }

                    let response = await fetch(this.$root.url_webservice + "dashboard/refreshWidget", options);
			        let result = await response.json();
                    
                    if (!result.error) {
                        let updateWidget = this.dashboards[this.selectedDashboard].widgets.find(w => w.idWidget == widget.idWidget);
                        if(updateWidget) {
                            updateWidget = result;
                        }
                    }

                    if (loader) {
                        this.hiddenLoader();
                    }
                }
            }
        }
    }
</script> 