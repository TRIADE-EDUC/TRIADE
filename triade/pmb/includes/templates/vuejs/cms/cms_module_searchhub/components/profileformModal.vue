<template>
    <form-modal
        :title="cmsMessages['cms_module_searchhub_profile_add_search']"
        formClass="searchhub-form-modal"
        :showSave="true"
        :showDelete="deletable"
        @close="$emit('close')"
        @show="$emit('show')"
        @submit="submit"
        @remove="remove"
        ref="formSearch">

        <div class="settings-form-modal">
            <!-- Nom -->
            <div class="row mb-s">
                <div class="colonne3">
                    <label class="etiquette" for="search-name">
                        {{ cmsMessages['cms_module_searchhub_profile_search_name_label'] }}
                    </label>
                </div>
                <div class="colonne-suite">
                    <input type="text" name="name" id="search-name" v-model="search.name" class="saisie-80em" required />
                    <!-- traduction nom -->
                    <translation 
                    nodeName="search-name" 
                    type="text" 
                    :attributes="{
                        class : 'saisie-80em'
                    }" 
                    :data="search.translation.name"
                    v-model="search.translation.name"
                    ></translation>
                </div>
            </div>

            <!-- Description -->
            <div class="row mb-s">
                <div class="colonne3">
                    <label class="etiquette" for="search-description">
                        {{ cmsMessages['cms_module_searchhub_profile_search_description_label'] }}
                    </label>
                </div>
                <div class="colonne-suite">
                    <textarea cols="90" rows="3" name="name" id="search-description" v-model="search.description" class="saisie-80em"></textarea>
                    <!-- traduction description -->
                    <translation 
                    nodeName="search-description" 
                    type="textarea" 
                    :attributes="{
                        class : 'saisie-80em',
                        rows : 3,
                        cols : 90
                    }" 
                    :data="search.translation.description"
                    v-model="search.translation.description"
                    ></translation>
                </div>
            </div>

            <!-- type -->
            <div class="row mb-s">
                <div class="colonne3">
                    <label class="etiquette" for="search-type">
                        {{ cmsMessages['cms_module_searchhub_profile_search_type_label'] }}
                    </label>
                </div>
                <div class="colonne-suite">
                    <select name="type" id="search-type" v-model="search.type" class="saisie-80em" required>
                        <option value="" disabled>{{ cmsMessages['cms_module_searchhub_profile_search_type_none'] }}</option>
                        <option value="simple">{{ cmsMessages['cms_module_searchhub_profile_search_type_simple'] }}</option>
                        <option value="universe">{{ cmsMessages['cms_module_searchhub_profile_search_type_universe'] }}</option>
                        <option value="external">{{ cmsMessages['cms_module_searchhub_profile_search_type_external'] }}</option>
                        <option value="cms_editorial">{{ cmsMessages['cms_module_searchhub_profile_search_type_cms_editorial'] }}</option>
                    </select>
                </div>
            </div>

            <!-- parametrage -->
            <component
                v-if="settingsComponent" :is="settingsComponent"
                :search="search" ref="settingsComponent">
            </component>

            <!-- Texte indicatif par defaut -->
            <div class="row mb-s">
                <div class="colonne3">
                    <label class="etiquette" for="search-placeholder">
                        {{ cmsMessages['cms_module_searchhub_profile_search_placeholder'] }}
                    </label>
                </div>
                <div class="colonne-suite">
                    <input type="text" name="placeholder" id="search-placeholder" v-model="search.settings.placeholder" class="saisie-80em">
                    <!-- traduction texte indicatif -->
                    <translation 
                    nodeName="search-placeholder" 
                    type="text" 
                    :attributes="{
                        class : 'saisie-80em'
                    }" 
                    :data="search.translation.placeholder"
                    v-model="search.translation.placeholder"
                    ></translation>
                </div>
            </div>

            <!-- Visibilite -->
            <div class="row mb-s">
                <div class="colonne3">
                    <label class="etiquette" for="search-visibility">
                        {{ cmsMessages['cms_module_searchhub_profile_search_visibility'] }}
                    </label>
                </div>
                <div class="colonne-suite">
                    <select name="visibility" id="search-visibility" v-model="search.settings.visibility" class="saisie-80em" required>
                        <option value="" disabled>{{ cmsMessages['cms_module_searchhub_profile_search_visibility_none'] }}</option>
                        <option value="all">{{ cmsMessages['cms_module_searchhub_profile_search_visibility_all'] }}</option>
                        <option value="onlyConnected">{{ cmsMessages['cms_module_searchhub_profile_search_visibility_connected'] }}</option>
                        <option value="categories">{{ cmsMessages['cms_module_searchhub_profile_search_visibility_categories'] }}</option>
                    </select>
                </div>
            </div>
            <div class="row mb-s" v-if="search.settings.visibility === 'categories'">
                <div class="colonne3">
                    <label class="etiquette" for="search-visibility-categories">
                        {{ cmsMessages['cms_module_searchhub_profile_search_categories_label'] }}
                    </label>
                </div>
                <div class="colonne-suite">
                    <select name="categories" id="search-visibility-categories" v-model="search.settings.categories" class="saisie-80em" multiple="multiple" required>
                        <option v-for="(label, id) in categories" :key="id" :value="id">{{ label }}</option>
                    </select>
                </div>
            </div>

            <!-- recherche active -->
            <div class="row mb-s">
                <div class="colonne3">
                    <label class="etiquette" for="search-active">
                        {{ cmsMessages['cms_module_searchhub_profile_search_active'] }}
                    </label>
                </div>
                <div class="colonne-suite">
                    <input type="checkbox" name="active" id="search-active" v-model="search.active" true-value="1" false-value="0">
                </div>
            </div>

             <!-- Autre lien -->
            <div class="row mb-s" v-for="(otherLink, index) in search.settings.otherLinks" :key="index">
                <button :class="['bouton up', upDisabled(index) ? 'disabled' : '']" type="button" @click="down(index)" :disabled="upDisabled(index)">
                    <img :src="images.get('top-arrow.png')" :alt="messages.get('common', 'up')">
                </button>
                <button :class="['bouton down', downDisabled(index) ? 'disabled' : '']" type="button" @click="up(index)" :disabled="downDisabled(index)">
                    <img :src="images.get('bottom-arrow.png')" :alt="messages.get('common', 'down')">
                </button>

                <img v-if="checkingOtherLink[index] === 'loading'" :src="images.get('patience.gif')" alt="">
                <img v-else-if="checkingOtherLink[index] === 'error'" :src="images.get('error.gif')" alt="">
                <img v-else-if="checkingOtherLink[index] === 'success'" :src="images.get('tick.gif')" alt="">
                
                <label class="etiquette" :for="`search-other-link-${index}`">{{ cmsMessages['cms_module_searchhub_profile_search_link'] }}</label>
                <input type="text" :id="`search-other-link-${index}`" class="saisie-20em"  v-model="otherLink.link">

                <button class="bouton" type="button" @click="checkOtherLink(index)">{{ cmsMessages['cms_module_searchhub_profile_search_link_check'] }}</button>

                <label class="etiquette" :for="`search-other-link-${index}-label`">{{ cmsMessages['cms_module_searchhub_profile_search_link_label'] }}</label>
                <input type="text" :id="`search-other-link-${index}-label`" class="saisie-15em" size="50" v-model="otherLink.label">

                <label class="etiquette" :for="`search-other-link-${index}-title`">{{ cmsMessages['cms_module_searchhub_profile_search_link_title'] }}</label>
                <input type="text" :id="`search-other-link-${index}-title`" class="saisie-15em" size="50" v-model="otherLink.title">

                <input :id="`search-other-link-${index}-target-blank`" type="checkbox" v-model="otherLink.target_blank" value="1">
                <label :for="`search-other-link-${index}-target-blank`">{{ cmsMessages['cms_module_searchhub_profile_search_link_target_blank'] }}</label>

                <button class="bouton" type="button" @click="delOtherLink(index)">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
             <div class="row mb-s">
                <div class="colonne3">
                    <label class="etiquette" for="search-other-link">
                        {{ cmsMessages['cms_module_searchhub_profile_search_other_link'] }}
                    </label>
                    <button class="bouton" type="button" @click="addOtherLink">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </form-modal>
</template>

<script>
    import formModal from '@/common/components/FormModal.vue';
    import searchUniverse from './search/searchUniverse.vue';
    import searchExternal from './search/searchExternal.vue';
    import searchCmsEditorial from './search/searchCmsEditorial.vue';
    import translation from '@/common/translation/translation.vue';
    
    export default {
        props: {
            search: {
                type: Object,
                required: true
            },
            deletable: {
                type: Boolean,
                default: false
            }
        },
        components: { formModal, searchUniverse, searchExternal, searchCmsEditorial, translation },
        data: function() {
            return {
                checkingOtherLink: [],
                showTranslation: {}
            }
        },
        watch: {
            'search.settings.visibility': function (newValue) {
                if (newValue === 'categories') {
                    this.$set(this.search.settings, 'categories', this.search.settings.categories || []);
                } else {
                    this.$delete(this.search.settings, 'categories');
                }
            }
        },
        computed: {
            /**
             * Retourne le composant de parametres de recherche
             *
             * @return {string}
             */
            settingsComponent: function() {
                switch (this.search.type) {
                    case 'universe':
                        return 'search-universe';
                    case 'external':
                        return 'search-external';
                    case 'cms_editorial':
                        return 'search-cms-editorial';
                    default:
                        return '';
                }
            }
        },
        created : function() {
            if (!this.search.translation) {
                this.$set(this.search, 'translation', {
                    name: {},
                    description: {},
                    placeholder: {}
                });
            }
        },
        methods: {
            /**
             * Affiche le formulaire de tableau de bord
             *
             * @return {void}
             */
            show: function() {
                this.$refs.formSearch.show();
                if (!this.search.settings) {
                    this.$set(this.search, 'settings', {});
                } else {
                    this.$set(this.search, 'settings', this.search.settings);
                }
            },

            /**
             * Ferme le formulaire de tableau de bord
             *
             * @return {void}
             */
            close: function() {
                this.$refs.formSearch.close();
            },

            /**
             * Enregistre le tableau de bord
             *
             * @return {void}
             */
            submit: function(data) {
                this.$emit('submit', data);
                this.close();
            },

            /**
             * Supprime le tableau de bord
             *
             * @return {void}
             */
            remove: function() {
                if (confirm(this.cmsMessages['cms_module_searchhub_profile_search_confirm_delete'])) {
                    this.$emit('remove');
                    this.close();
                }
            },

            /**
             * Ajoute un autre lien
             *
             * @return {void}
             */
            addOtherLink: function() {
                this.$set(this.search.settings, 'otherLinks', this.search.settings.otherLinks || []);
                this.search.settings.otherLinks.push({
                    link: '',
                    label: '',
                    title: '',
                    target_blank: false
                });
            },

            checkOtherLink: async function(index) {
                this.$set(this.checkingOtherLink, index, 'loading');
                if (this.csrfTokens.length === 0) {
                    this.$set(this.checkingOtherLink, index, 'error');
                    return false;
                }

                const formData = new FormData();
                formData.append('link', this.search.settings.otherLinks[index].link);
                formData.append('csrf_token', this.csrfTokens[0]);
                this.csrfTokens.splice(0, 1);

                const result = await fetch('./ajax.php?module=ajax&categ=chklnk', {
                    method: 'POST',
                    body: formData
                });
                const response = await result.text();
                if (response == '200') {
                    this.$set(this.checkingOtherLink, index, 'success');
                } else {
                    this.$set(this.checkingOtherLink, index, 'error');
                }
            },

            /**
             * Supprime un autre lien
             *
             * @param {number} index
             */
            delOtherLink: function(index) {
                this.search.settings.otherLinks.splice(index, 1);
                this.$delete(this.checkingOtherLink, index);
            },

            /**
             * gere la visibilite des traductions
             * @param {string} name
             */
            toggleTranslation(name) {
                if (!this.showTranslation[name]) {
                    this.showTranslation[name] = false;
                }
                this.$set(this.showTranslation, name, !this.showTranslation[name]);
            },
            /**
             * Deplace un autre lien vers le haut dans la liste.
             *
             * @param {number} index - L'index courant de l'autre lien a deplacer.
             */
             up: function (index) {
		        this.search.settings.otherLinks.splice(index+2, 0, this.search.settings.otherLinks[index])
		        this.search.settings.otherLinks.splice(index, 1)
		    },

            /**
             * Deplace un autre lien vers le bas dans la liste.
             *
             * @param {number} index - L'index courant de l'autre lien a deplacer.
             */
		    down: function (index) {
		        this.search.settings.otherLinks.splice(index-1, 0, this.search.settings.otherLinks[index])
		        this.search.settings.otherLinks.splice(index+1, 1)
		    },
            
            /**
             * teste sur la desactivation du bouton de deplacement vers le bas
             * @param {number} index - index courant de l'autre lien
             * @return {boolean} vrai si le bouton est desactive
             */
		    downDisabled: function(index) {
		        return index == this.search.settings.otherLinks.length - 1;
		    },

            /**
             * teste sur la desactivation du bouton de deplacement vers le haut
             * @param {number} index - index courant de l'autre lien
             * @return {boolean} vrai si le bouton est desactive
             */
		    upDisabled: function(index) {
		        return index == 0;
		    },
        }
    }

</script>