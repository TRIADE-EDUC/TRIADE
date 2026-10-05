<template>
    <div class="searchhub-profile">
        <form name="cms_module_searchhub" method="POST" action="./cms.php?categ=manage&sub=searchhub&quoi=module&action=save_form">
            <input type="hidden" name="profile[id]" :value="profile.id" />
            <div class="form-contenu">
                <div class="row">
                    <div class="colonne3">
                        <label for="profile_name">
                            {{ cmsMessages['cms_module_searchhub_profile_name'] }}
                        </label>
                    </div>
                    <div class="colonne-suite">
                        <input type="text" name="profile[name]" id="profile_name" v-model.trim="profile.name"  class="saisie-80em" />
                    </div>
                </div>
                <div class="row">
                    <div class="colonne3">
                        <label for="profile_name">
                            {{ cmsMessages['cms_module_searchhub_profile_search'] }}
                        </label>
                    </div>
                    <div class="colonne-suite">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ cmsMessages['cms_module_searchhub_profile_search_order'] }}</th>
                                    <th>{{ cmsMessages['cms_module_searchhub_profile_search_name'] }}</th>
                                    <th>{{ cmsMessages['cms_module_searchhub_profile_search_type'] }}</th>
                                    <th>{{ cmsMessages['cms_module_searchhub_profile_action'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(search, index) in profile.searches">
                                    <td>
                                        <input type="hidden" :name="`profile[searches][${index}][name]`" :value="search.name" />
                                        <input type="hidden" :name="`profile[searches][${index}][type]`" :value="search.type" />
                                        <input type="hidden" :name="`profile[searches][${index}][description]`" :value="search.description" />
                                        <input type="hidden" :name="`profile[searches][${index}][settings]`" :value="toJSON(search.settings)" />
                                        <input type="hidden" :name="`profile[searches][${index}][translation]`" :value="toJSON(search.translation)" />
                                        <input type="hidden" :name="`profile[searches][${index}][active]`" :value="search.active" />
                                        <button :class="['bouton up', upDisabled(index) ? 'disabled' : '']" type="button" @click="down(index)" :disabled="upDisabled(index)">
                                            <img :src="images.get('top-arrow.png')" :alt="messages.get('common', 'up')">
                                        </button>
                                        <button :class="['bouton down', downDisabled(index) ? 'disabled' : '']" type="button" @click="up(index)" :disabled="downDisabled(index)">
                                            <img :src="images.get('bottom-arrow.png')" :alt="messages.get('common', 'down')">
                                        </button>
                                    </td>
                                    <td>
                                        <p>{{ search.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ cmsMessages['cms_module_searchhub_profile_search_type_'+search.type] }}</p>
                                    </td>
                                    <td>
                                        <button type="button" class="bouton" @click="editSearch(index)">{{ cmsMessages['cms_manage_module_edit'] }}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="bouton" @click="editSearch()">{{ cmsMessages['cms_module_searchhub_profile_add_search'] }}</button>
                    </div>
                </div>
                <div class="row">
                    <hr />
                    <div class="left">
                        <button type="submit" class="bouton">{{ cmsMessages['cms_manage_module_save'] }}</button>
                    </div>
                    <div class="right">
                        <button v-if="profile.id" type="button" class="bouton btnDelete" @click="remove">{{ cmsMessages['cms_module_root_delete'] }}</button>
                    </div>
                </div>
            </div>
        </form>
        <profileform-modal
            v-if="currentSearch"
            :profile="profile"
            :search="currentSearch"
            :index="currentIndex"
            :deletable="currentIndex !== undefined"
            ref="searchSettingsModal"
            @close="resetCurrentSearch"
            @submit="saveSearch"
            @remove="removeSearch">
        </profileform-modal>
    </div>
</template>

<script>
    import profileformModal from './profileformModal.vue';

	export default {
        name: 'profile-form',
        props: ['profile'],
        components: { profileformModal },
        data : function() {
            return {
                currentSearch : undefined,
                currentIndex : undefined,
                emptySearch : {
                    name : '',
                    type : '',
                    description : '',
                    settings : {
                        visibility : 'all',
                        placeholder : '',
                        otherLinks : []
                    },
                    translation : {
                        name : {},
                        description : {},
                        placeholder : {}
                    }, 
                    active : 0
                }
            }
        },
        methods: {
            /**
             * teste sur la desactivation du bouton de deplacement vers le bas
             * @param {number} index - index courant de la recherche
             * @return {boolean} vrai si le bouton est desactive
             */
		    downDisabled: function(index) {
		        return index == this.profile.searches.length - 1;
		    },

            /**
             * teste sur la desactivation du bouton de deplacement vers le haut
             * @param {number} index - index courant de la recherche
             * @return {boolean} vrai si le bouton est desactive
             */
		    upDisabled: function(index) {
		        return index == 0;
		    },

            /**
             * Deplace une recherche vers le haut dans la liste.
             *
             * @param {number} index - L'index courant de la recherche a deplacer.
             */
            up: function (index) {
		        this.profile.searches.splice(index+2, 0, this.profile.searches[index])
		        this.profile.searches.splice(index, 1)
		    },

            /**
             * Deplace une recherche vers le bas dans la liste.
             *
             * @param {number} index - L'index courant de la recherche a deplacer.
             */
		    down: function (index) {
		        this.profile.searches.splice(index-1, 0, this.profile.searches[index])
		        this.profile.searches.splice(index+1, 1)
		    },

            /**
             * Clone un objet
             *
             * @param {Object} obj
             */
            clone: function(obj) {
                return JSON.parse(JSON.stringify(obj));
            },

            /**
             * Affiche le formulaire de modification d'une recherche
             *
             * @param {number|undefined} index - (optionnel) L'index de la recherche a modifier, undefined pour ajouter une nouvelle recherche
             */
            editSearch : function(index = undefined) {
                if (index !== undefined) {
                    this.currentIndex = index;
                    this.$set(this, 'currentSearch', this.clone(this.profile.searches[index]));
                } else {
                    this.currentIndex = undefined;
                    this.$set(this, 'currentSearch', this.clone(this.emptySearch));
                }

                this.$nextTick(() => { this.$refs.searchSettingsModal.show(); })
            },

            /**
             * Enregistre une recherche dans le profile.
             *
             * @return {void}
             */
            saveSearch : function() {
                if (this.profile.searches === undefined) {
                    this.profile.searches = [];
                }
                if (this.currentIndex !== undefined) {
                    this.$set(this.profile.searches, this.currentIndex, this.clone(this.currentSearch));
                } else {
                    this.profile.searches.push(this.clone(this.currentSearch));
                }
                this.resetCurrentSearch();
            },

            /**
             * Supprime la recherche courante.
             *
             * @return {void}
             */
            resetCurrentSearch: function() {
                this.$set(this, 'currentSearch', undefined);
                this.$set(this, 'currentIndex', undefined);
            },

            /**
             * Supprime une recherche du profil.
             * Cette methode supprime la recherche a l'index courant (currentIndex) de la liste des recherches du profil.
             *
             * @return {void}
             */
            removeSearch: function() {
                this.profile.searches.splice(this.currentIndex, 1);
                this.resetCurrentSearch();
            },

            /**
             * Supprime le profil actuel apres confirmation.
             *
             * @return {void}
             */
            remove: function() {
                if (confirm(this.cmsMessages['cms_module_searchhub_profile_confirm_delete'])) {
                    document.location.href = './cms.php?categ=manage&sub=searchhub&quoi=module&action=save_form&deleted_profile=' + this.profile.id;
                }
            },

            /**
             * Retourne un Object en JSON
             *
             * @param {Object} obj
             * @return {String}
             */
            toJSON: function(obj) {
                return JSON.stringify(obj);
            },
        }
    }
</script>