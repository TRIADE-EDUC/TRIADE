<template>
    <form-modal 
        :title="title"
        formClass="dashboard-form"
        :showCancel="true"
        :showSave="true"
        :showDelete="this.currentDashboard.idDashboard ? true : false"
        :showDuplicate="this.currentDashboard.idDashboard ? true : false"
        @close="$emit('close')"
        @show="$emit('show')"
        @submit="submit"
        @remove="remove"
        @duplicate="duplicate"
        ref="formDashboard">

        <div class="dashboard-form-modal">
            
            <!-- Nom du tableau de bord -->
            <div class="mb-s">
                <label class="dashboard-form-label" for="dashboard-name">
                    {{ messages.get('dashboard', 'form_name_dashboard') }}
                </label>
                <input type="text" name="dashboard-name" id="dashboard-name" v-model="currentDashboard.dashboardName" required>
            </div>
    
            <!-- Partage du tableau de bord -->
            <div class="mb-s">
                <label class="dashboard-form-label" for="dashboard-share">
                    {{ messages.get('dashboard', 'form_share_dashboard') }}
                </label>
                <select name="dashboard-share" id="dashboard-share" multiple v-model="currentDashboard.dashboardUsersGroups">
                    <option v-for="group in groups" :value="group.grp_id">
                        {{ group.grp_name }}
                    </option>
                </select>
            </div>
    
            <!-- Edition du tableau de bord -->
            <div>
                <label class="dashboard-form-label" for="dashboard-editable">
                    {{ messages.get('dashboard', 'form_edit_dashboard') }}
                    <i 
                        class="fa fa-info-circle" 
                        :title="messages.get('dashboard', 'form_edit_title_dashboard')" 
                        aria-hidden="true">
                    </i>
                </label>
                <input type="checkbox" name="dashboard-editable" id="dashboard-editable" v-model="currentDashboard.dashboardEditable">
            </div>
        </div>
    </form-modal>
</template>

<script>
    import formModal from '@/common/components/FormModal.vue';
    
    export default {
        props: ["dashboard", "groups", "current_user"],
        components: {
            formModal
        },
        data: function() {
            return {
                currentDashboard: null, // Tableau de bord courant
                emptyDashboard: { // Tableau de bord vide
                    idDashboard: 0,
                    dashboardName: "",
                    dashboardEditable: true,
                    numUser: this.current_user,
                    dashboardUsersGroups: [],
                    layout: [],
                    widgets: []
                }
            }
        },
        created: function() {
            this.getCurrentDashboard();
        },
        watch: {
            dashboard: function() {
                this.getCurrentDashboard();
            }
        },
        computed: {

            /**
             * Retourne le titre en fonction de l'existence du tableau de bord
             *
             * @return {string}
             */
            title: function() {
                if(this.dashboard && this.dashboard.idDashboard) {
                    return this.messages.get('dashboard', 'param_dashboard');
                }
                return this.messages.get('dashboard', 'add_form_dashboard');
            }
        },
        methods: {

            /**
             * Affiche le formulaire de tableau de bord
             *
             * @return {void}
             */
            show: function() {
                this.$refs.formDashboard.show();
                this.getCurrentDashboard();
            },

            /**
             * Ferme le formulaire de tableau de bord
             *
             * @return {void}
             */
            close: function() {
                this.$refs.formDashboard.close();
            },

            /**
             * Récupère le tableau de bord courant
             *
             * @return {void}
             */
            getCurrentDashboard: function() {
                if(this.dashboard && this.dashboard.idDashboard) {
                    this.currentDashboard = this.dashboard;
                } else {
                    this.currentDashboard = this.helper.cloneObject(this.emptyDashboard);
                }
            },

            /**
             * Enregistre le tableau de bord
             *
             * @return {void}
             */
            submit: async function() {
                let response = await this.ws.post('dashboard', 'save', this.currentDashboard);

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                } else {
                    if(!this.currentDashboard.idDashboard) {
                        this.$emit("addDashboard", this.currentDashboard);
                    }
                    this.currentDashboard.idDashboard = response.idDashboard;
                    this.notif.info(this.messages.get('common', 'success_save'));

                    this.close();
                }
            },

            /**
             * Supprime le tableau de bord
             *
             * @return {void}
             */
            remove: async function() {
                if(confirm(this.messages.get('common', "confirm_delete"))) {
                    let response = await this.ws.post('dashboard', 'delete', { idDashboard: this.currentDashboard.idDashboard });
    
                    if (response.error) {
                        this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    } else {
                        this.$emit("removeDashboard", this.currentDashboard.idDashboard);
                        
                        this.notif.info(this.messages.get('common', 'success_delete'));
                        this.close();
                    }
                }
            },

            /**
             * Duplique le tableau de bord
             *
             * @return {void}
             */
            duplicate: async function() {
                let response = await this.ws.post('dashboard', 'duplicate', {
                     idDashboard: this.currentDashboard.idDashboard 
                });

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    return;
                }

                this.$emit("duplicateDashboard");
                
                this.notif.info(this.messages.get('common', 'successful_operation'));
                this.close();
            }
        }
    }

</script>