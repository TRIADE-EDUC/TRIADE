<template>
    <div>
        <div class="row mb-s">
            <div class="colonne3">
                <label class="etiquette" for="search-settings-universe">
                    {{ cmsMessages['cms_module_searchhub_profile_search_universe'] }}
                </label>
            </div>
            <div class="colonne-suite">
                <select id="search-settings-universe" v-model="search.settings.universe" name="universe" class="saisie-80em" required>
                    <option value="">{{ cmsMessages['cms_module_searchhub_profile_search_universe_none'] }}</option>
                    <option v-for="universe in universes" :key="universe.id" :value="universe.id">{{ universe.label }}</option>
                </select>
            </div>
        </div>
        <div class="row mb-s">
            <div class="colonne3">
                <label class="etiquette" for="search-settings-segments">
                    {{ cmsMessages['cms_module_searchhub_profile_search_segments'] }}
                </label>
            </div>
            <div class="colonne-suite">
                <select id="search-settings-segments" v-model="selectedSegment" class="saisie-80em">
                    <option value="">{{ cmsMessages['cms_module_searchhub_profile_search_segments_none'] }}</option>
                    <option v-for="segment in segments" :key="segment.id" :value="segment.id">{{ segment.label }}</option>
                </select>
                <button class="bouton" type="button" @click="addSegment">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="row mb-s">
            <div class="colonne3">
                &nbsp;
                <input type="hidden" name="segments" :value="toJSON(search.settings.segments)">
            </div>
            <div class="colonne-suite">
                <table>
                    <thead>
                        <tr>
                            <th>{{ cmsMessages['cms_module_searchhub_profile_search_segments_order'] }}</th>
                            <th>{{ cmsMessages['cms_module_searchhub_profile_search_segments_name'] }}</th>
                            <th>{{ cmsMessages['cms_module_searchhub_profile_search_segments_action'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(segment, index) in search.settings.segments" :key="segment.id">
                            <td>
                                <button :class="['bouton up', upSegmentDisabled(index) ? 'disabled' : '']" type="button" @click="downSegment(index)" :disabled="upSegmentDisabled(index)">
                                    <img :src="images.get('top-arrow.png')" :alt="messages.get('common', 'up')">
                                </button>
                                <button :class="['bouton down', downSegmentDisabled(index) ? 'disabled' : '']" type="button" @click="upSegment(index)" :disabled="downSegmentDisabled(index)">
                                    <img :src="images.get('bottom-arrow.png')" :alt="messages.get('common', 'down')">
                                </button>
                            </td>
                            <td>{{ getSegmentLabel(segment) }}</td>
                            <td>
                                <button type="button" class="bouton" @click="removeSegment(index)">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["search"],
        data: function() {
            return {
                universes: [],
                selectedSegment: '',
            }
        },
		updated: function() {
			if (typeof domUpdated === "function") {
			    domUpdated();
			}
		},
        mounted: function() {
            this.$set(this.search.settings, 'universe', this.search.settings.universe || '');
            this.$set(this.search.settings, 'segments', this.search.settings.segments || []);

            this.initUniverses();
        },
        computed: {
            segments: function() {
                const universe = this.universes.find(universe => universe.id == this.search.settings.universe);
                if (universe) {
                    return universe.segments.filter(segment => {
                        return !this.search.settings.segments.includes(segment.id);
                    });
                }
                return [];
            }
        },
        methods: {
            /**
             * teste sur la desactivation du bouton de deplacement vers le bas
             * @param {number} index - index courant de la recherche
             * @return {boolean} vrai si le bouton est desactive
             */
		    downSegmentDisabled: function(index) {
		        return index == this.search.settings.segments.length - 1;
		    },

            /**
             * teste sur la desactivation du bouton de deplacement vers le haut
             * @param {number} index - index courant de la recherche
             * @return {boolean} vrai si le bouton est desactive
             */
		    upSegmentDisabled: function(index) {
		        return index == 0;
		    },

            /**
             * Deplace une recherche vers le haut dans la liste.
             *
             * @param {number} index - L'index courant de la recherche a deplacer.
             */
             upSegment: function (index) {
		        this.search.settings.segments.splice(index+2, 0, this.search.settings.segments[index])
		        this.search.settings.segments.splice(index, 1)
		    },

            /**
             * Deplace une recherche vers le bas dans la liste.
             *
             * @param {number} index - L'index courant de la recherche a deplacer.
             */
		    downSegment: function (index) {
		        this.search.settings.segments.splice(index-1, 0, this.search.settings.segments[index])
		        this.search.settings.segments.splice(index+1, 1)
		    },

            /**
             * Recupere les universes
             *
             * @return {void}
             */
            initUniverses: function() {
                fetch(this.urlWebservice+"&do=get_universes_segments", {
                    method: 'GET',
                }).then((response)=> {
                    if (response.ok) {
                        response.json().then((jsonContent) => {
                            this.universes = jsonContent;
                        })
                    } else {
                        console.log("error from response : " + response);
                    }
                    this.hiddenLoader();
                }).catch((error) => {
                    console.log("error during fetch : " + error.message);
                    this.hiddenLoader();
                });
            },

            /**
             * Ajoute un segment
             *
             * @return {void}
             */
            addSegment: function() {
                if (this.selectedSegment) {
                    const segment = this.segments.find(segment => segment.id == this.selectedSegment);
                    if (segment) {
                        this.search.settings.segments.push(segment.id);
                        this.selectedSegment = '';
                    }
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

            /**
             * Retourne le label d'un segment
             *
             * @param {number} segment_id
             * @return {string}
             */
            getSegmentLabel: function(segment_id) {
                const universe = this.universes.find(universe => universe.id == this.search.settings.universe);
                const segment = universe?.segments.find(segment => segment.id == segment_id);
                return segment?.label || '';
            },

            /**
             * Retire un segment
             *
             * @param {number} index
             */
            removeSegment: function(index) {
                this.search.settings.segments.splice(index, 1);
            }
        }
    }

</script>