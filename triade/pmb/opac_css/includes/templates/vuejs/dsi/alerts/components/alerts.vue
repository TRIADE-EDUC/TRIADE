<template>
    <div class="row">
        <h1 class="visually-hidden">{{ messages.get('dsi', 'title_alerts') }}</h1>
        <div id="dsi-alerts-pub" class="dsi-alerts">
            <h2>{{ messages.get('dsi', 'public_alerts') }}</h2>
            <div class="row">
                <div class="dsi-tools">
                    <div class="dsi-search-container">
                        <svg fill="#000000" height="12px" width="12px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" transform="matrix(-1, 0, 0, 1, 0, 0)"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><g><g><path d="M278.718,0C150.086,0,45.435,104.65,45.435,233.282c0,55.642,19.592,106.789,52.228,146.928L0,477.872L34.128,512 l97.663-97.663c40.137,32.635,91.284,52.228,146.926,52.228C407.35,466.565,512,361.914,512,233.282S407.35,0,278.718,0z M278.718,418.299c-102.018,0-185.017-82.999-185.017-185.017S176.699,48.265,278.718,48.265s185.017,82.999,185.017,185.017 S380.736,418.299,278.718,418.299z"></path></g></g></g></svg>
                        <input class="text_query" type="search" v-model="searchDiffusionsPublic" :placeholder="messages.get('dsi', 'search_filter_placeholder')">
                    </div>
                    <div v-show="diffusions.some(obj => obj.tags.length > 0)" class="dsi-btn-filter">
                        <display-button :show="show" @toggle="show = $event">
                            <svg width="12px" height="12px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M15 10.5A3.502 3.502 0 0 0 18.355 8H21a1 1 0 1 0 0-2h-2.645a3.502 3.502 0 0 0-6.71 0H3a1 1 0 0 0 0 2h8.645A3.502 3.502 0 0 0 15 10.5zM3 16a1 1 0 1 0 0 2h2.145a3.502 3.502 0 0 0 6.71 0H21a1 1 0 1 0 0-2h-9.145a3.502 3.502 0 0 0-6.71 0H3z" fill="#000000"></path></g></svg>
                            {{ messages.get('dsi', 'show_filters') }}
                        </display-button>
                    </div>
                </div>
            </div>
            <div v-show="diffusions.some(obj => obj.tags.length > 0)" class="row">
                <div class="dsi-container-tag" :class="{'dsi-visible-tags' : show }">
                    <filter-tags
                        :diffusions="diffusions" 
                        @filter='filteredDiffusions = $event' 
                        @selected='selectedTags = $event'
                        v-model="selectedTags"
                        @removeTag="unselectTag($event)"
                        >
                    </filter-tags>
                </div>
            </div>
            <div class="dsi-table-container">
                <table class="uk-table uk-table-small uk-table-middle">
                    <thead>
                        <tr>
                            <th>{{ messages.get('dsi', 'alert_list_name') }}</th>
                            <th>{{ messages.get('dsi', 'alert_list_last_diffusion') }}</th>
                            <th>{{ messages.get('dsi', 'alert_list_number_elements') }}</th>
                            <th>{{ messages.get('dsi', 'alert_list_taglist') }}</th>
                            <th>{{ messages.get('dsi', 'alert_list_subscribe_status') }}</th>
                        </tr>
                    </thead>
                    <tbody v-for="(diffusion, i) in filteredDiffusionsPublic" :key="i">
                        <tr>
                            <td :data-label="messages.get('dsi', 'alert_list_name')" >
                                <div class="dsi-btn-history">
                                    <display-button 
                                        :show="showHistory[diffusion.id]" 
                                        @toggle="showHistory[diffusion.id] = $event" 
                                        type="button" 
                                        :class="showHistory[diffusion.id] ? 'dsi-active-history' : ''"
                                        :title="messages.get('dsi', 'alert_btn_title_show_history')">
                                        <svg width="16px" height="16px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><path d="M17 9.5L12 14.5L7 9.5" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
                                        <span>{{ diffusion.settings.opacName }}</span>
                                    </display-button>
                                </div>
                            </td>
                            <td v-if="diffusion.lastDiffusion" :data-label="messages.get('dsi', 'alert_list_last_diffusion')">{{ diffusion.lastDiffusion }}</td>
                            <td v-else :data-label="messages.get('dsi', 'alert_list_last_diffusion')"> - - - </td>
                            <td :data-label="messages.get('dsi', 'alert_list_number_elements')">{{ diffusion.nbResults }}</td>
                            <td :data-label="messages.get('dsi', 'alert_list_taglist')">
                                <button v-for="(tag, j) in diffusion.tags" 
                                    :key="j" 
                                    class="bouton"
                                    :class="selectedTags.includes(tag.id) ? 'dsi-tag dsi-tag-selected' : 'dsi-tag dsi-tag-not-selected'"
                                    @click="selectedTags.includes(tag.id) ? unselectTag(tag.id) : selectTag(tag.id); show = selectedTags.length > 0">
                                    {{ tag.name }}
                                </button>
                            </td>
                            <td :data-label="messages.get('dsi', 'alert_list_subscribe_status')">
                                <label class="switch" :class="[{ 'dsi-subscribe-animation' : classAnimation === i}, diffusion.isSubscribed ? 'dsi-subscribed' : 'dsi-not-subscribed'] " >
                                    <input type="checkbox" :checked="! diffusion.isSubscribed" @change="subscribe(diffusion.id, diffusion.isSubscribed, i)">
                                    <span v-if="diffusion.isSubscribed" class="switch-label">{{ messages.get('dsi', 'alert_list_subscribe_status_checkbox') }}</span>
                                    <span v-else class="switch-label">{{ messages.get('dsi', 'alert_list_not_subscribe_status_checkbox') }} </span>
                                </label>
                            </td>
                        </tr>
                        <tr class="dsi-row-history">
                            <td colspan="5" class="dsi-cell-history">
                                <div class="dsi-container-history">
                                    <transition name="dsi-slide">
                                        <div v-show="showHistory[diffusion.id]" class="dsi-content-history">
                                            <div v-if="diffusion.diffusionHistory.length">
                                                <p>{{ messages.get('dsi', 'alert_paragraph_show_history').replace('!!nb_history!!', diffusion.settings.nb_history_saved) }}</p>
                                                <ol v-if="diffusion.diffusionHistory.length">
                                                    <li v-for="history in diffusion.diffusionHistory.slice().reverse()" :key="history.id">
                                                        <button @click="seeDiffusion(history.render)" type="button" class="bouton dsi-btn-previous-diff" :title="messages.get('dsi', 'alert_show_diffusion')">
                                                            <svg width="12px" height="12px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><path fill="#444" d="M8 3.9c-6.7 0-8 5.1-8 5.1s2.2 4.1 7.9 4.1 8.1-4 8.1-4-1.3-5.2-8-5.2zM5.3 5.4c0.5-0.3 1.3-0.3 1.3-0.3s-0.5 0.9-0.5 1.6c0 0.7 0.2 1.1 0.2 1.1l-1.1 0.2c0 0-0.3-0.5-0.3-1.2 0-0.8 0.4-1.4 0.4-1.4zM7.9 12.1c-4.1 0-6.2-2.3-6.8-3.2 0.3-0.7 1.1-2.2 3.1-3.2-0.1 0.4-0.2 0.8-0.2 1.3 0 2.2 1.8 4 4 4s4-1.8 4-4c0-0.5-0.1-0.9-0.2-1.3 2 0.9 2.8 2.5 3.1 3.2-0.7 0.9-2.8 3.2-7 3.2z"></path></g></svg>
                                                            <span>{{ messages.get('dsi', 'alert_before_diffusion_date') }}{{history.date}}</span>
                                                        </button>
                                                    </li>
                                                </ol>
                                            </div>
                                            <div v-else>{{ messages.get('dsi', 'alert_no_history') }}</div>
                                        </div>
                                    </transition>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="dsi-alerts-priv" class="dsi-alerts">
            <h2>{{ messages.get('dsi', 'self_alerts') }}</h2>
            <div class="row">
                <div class="dsi-tools">
                    <div class="dsi-search-container">
                        <svg fill="#000000" height="12px" width="12px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" transform="matrix(-1, 0, 0, 1, 0, 0)"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><g><g><path d="M278.718,0C150.086,0,45.435,104.65,45.435,233.282c0,55.642,19.592,106.789,52.228,146.928L0,477.872L34.128,512 l97.663-97.663c40.137,32.635,91.284,52.228,146.926,52.228C407.35,466.565,512,361.914,512,233.282S407.35,0,278.718,0z M278.718,418.299c-102.018,0-185.017-82.999-185.017-185.017S176.699,48.265,278.718,48.265s185.017,82.999,185.017,185.017 S380.736,418.299,278.718,418.299z"></path></g></g></g></svg>
                        <input class="text_query" type="search" v-model="searchDiffusionsPrivate" :placeholder="messages.get('dsi', 'search_filter_placeholder')">
                    </div>
                </div>
            </div>
            <div class="dsi-table-container">
                <table class="uk-table uk-table-small uk-table-middle">
                    <thead>
                        <tr>
                            <th>{{ messages.get('dsi', 'alert_list_name') }}</th>
                            <th>{{ messages.get('dsi', 'alert_list_search_input') }}</th>
                            <th>{{ messages.get('dsi', 'alert_list_last_diffusion') }}</th>
                            <th>{{ messages.get('dsi', 'alert_list_number_elements') }}</th>
                            <th>{{ messages.get('dsi', 'diffusion_private_remove') }}</th>
                        </tr>
                    </thead>
                    <tbody v-for="(diffusion, i) in filteredDiffusionsPrivate" :key="i">
                        <tr>
                            <td :data-label="messages.get('dsi', 'alert_list_name')" >
                                <div class="dsi-btn-history">
                                    <display-button 
                                        :show="showHistory[diffusion.id]" 
                                        @toggle="showHistory[diffusion.id] = $event" 
                                        type="button" 
                                        :class="showHistory[diffusion.id] ? 'dsi-active-history' : ''"
                                        :title="messages.get('dsi', 'alert_btn_title_show_history')">
                                        <svg width="16px" height="16px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><path d="M17 9.5L12 14.5L7 9.5" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
                                        <span>{{ diffusion.settings.opacName }}</span>
                                    </display-button>
                                </div>
                            </td>
                            <td :data-label="messages.get('dsi', 'alert_list_search_input')" v-html="diffusion.searchInput"></td>
                            <td v-if="diffusion.lastDiffusion" :data-label="messages.get('dsi', 'alert_list_last_diffusion')">{{ diffusion.lastDiffusion }}</td>
                            <td v-else :data-label="messages.get('dsi', 'alert_list_last_diffusion')"> - - - </td>
                            <td :data-label="messages.get('dsi', 'alert_list_number_elements')">{{ diffusion.nbResults }}</td>
                            <td :data-label="messages.get('dsi', 'diffusion_private_remove')">
                                <button :title="messages.get('dsi', 'diffusion_private_remove')" class="dsi-private-delete-button" @click="deleteDiffusionPrivate(diffusion)">
                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#cc4466" transform="rotate(0)"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><path d="M12.0004 9.5L17.0004 14.5M17.0004 9.5L12.0004 14.5M4.50823 13.9546L7.43966 17.7546C7.79218 18.2115 7.96843 18.44 8.18975 18.6047C8.38579 18.7505 8.6069 18.8592 8.84212 18.9253C9.10766 19 9.39623 19 9.97336 19H17.8004C18.9205 19 19.4806 19 19.9084 18.782C20.2847 18.5903 20.5907 18.2843 20.7824 17.908C21.0004 17.4802 21.0004 16.9201 21.0004 15.8V8.2C21.0004 7.0799 21.0004 6.51984 20.7824 6.09202C20.5907 5.71569 20.2847 5.40973 19.9084 5.21799C19.4806 5 18.9205 5 17.8004 5H9.97336C9.39623 5 9.10766 5 8.84212 5.07467C8.6069 5.14081 8.38579 5.2495 8.18975 5.39534C7.96843 5.55998 7.79218 5.78846 7.43966 6.24543L4.50823 10.0454C3.96863 10.7449 3.69883 11.0947 3.59505 11.4804C3.50347 11.8207 3.50347 12.1793 3.59505 12.5196C3.69883 12.9053 3.96863 13.2551 4.50823 13.9546Z" stroke="#cc4466" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="dsi-row-history">
                            <td colspan="5" class="dsi-cell-history">
                                <div class="dsi-container-history">
                                    <transition name="dsi-slide">
                                        <div v-show="showHistory[diffusion.id]" class="dsi-content-history">
                                            <div v-if="diffusion.diffusionHistory.length">
                                                <p>{{ messages.get('dsi', 'alert_paragraph_show_history').replace('!!nb_history!!', diffusion.settings.nb_history_saved) }}</p>
                                                <ol v-if="diffusion.diffusionHistory.length">
                                                    <li v-for="history in diffusion.diffusionHistory.slice().reverse()" :key="history.id">
                                                        <button @click="seeDiffusion(history.render)" type="button" class="bouton dsi-btn-previous-diff" :title="messages.get('dsi', 'alert_show_diffusion')">
                                                            <svg width="12px" height="12px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g><path fill="#444" d="M8 3.9c-6.7 0-8 5.1-8 5.1s2.2 4.1 7.9 4.1 8.1-4 8.1-4-1.3-5.2-8-5.2zM5.3 5.4c0.5-0.3 1.3-0.3 1.3-0.3s-0.5 0.9-0.5 1.6c0 0.7 0.2 1.1 0.2 1.1l-1.1 0.2c0 0-0.3-0.5-0.3-1.2 0-0.8 0.4-1.4 0.4-1.4zM7.9 12.1c-4.1 0-6.2-2.3-6.8-3.2 0.3-0.7 1.1-2.2 3.1-3.2-0.1 0.4-0.2 0.8-0.2 1.3 0 2.2 1.8 4 4 4s4-1.8 4-4c0-0.5-0.1-0.9-0.2-1.3 2 0.9 2.8 2.5 3.1 3.2-0.7 0.9-2.8 3.2-7 3.2z"></path></g></svg>
                                                            <span>{{ messages.get('dsi', 'alert_before_diffusion_date') }}{{history.date}}</span>
                                                        </button>
                                                    </li>
                                                </ol>
                                            </div>
                                            <div v-else>{{ messages.get('dsi', 'alert_no_history') }}</div>
                                        </div>
                                    </transition>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import filterTags from './filterTags.vue';
import displayButton from '../../../common/components/displayButton.vue';

export default {
    name : 'alerts',
    components : {
        filterTags,
        displayButton
    },
    props : ["diffusions", "subscriber", "emprType", "diffusionsPrivate"],
    data : function() {
        return {
            selectedTags : [],
            searchDiffusionsPublic : '',
            searchDiffusionsPrivate : '',
            show: false,
            classAnimation: null,
            showHistory : {},
        }
    },
    created : function() {
        this.diffusions.forEach(d => this.$set(this.showHistory, d.id, false));
        this.diffusionsPrivate.forEach(d => this.$set(this.showHistory, d.id, false));
    },
    methods : {
        selectTag(tagId) {
            this.$set(this.selectedTags, this.selectedTags.length, tagId);
        },
        unselectTag(tagId) {
            let i = this.selectedTags.findIndex(t => t == tagId);
            if(i != -1) {
                this.$delete(this.selectedTags, i);
            }
        },
        subscribe : async function(idDiffusion, isSubscribed, index) {
            //Si inscrit on se désinscrit
            let response = null;
            let i = this.diffusions.findIndex(d => d.id == idDiffusion);
            if(i == -1) {
                this.notif.error('');
                return;
            }
            let subscriber = this.subscriber;
            if(this.diffusions[i].subscriber) {
                subscriber = this.diffusions[i].subscriber;
            }
            if(isSubscribed) {
                response = await this.ws.post("subscribers", "diffusions/unsubscribe/" + idDiffusion, subscriber);
            } else {
                response = await this.ws.post("subscribers", "diffusions/subscribe/" + idDiffusion, subscriber);
            }
            if(! response.error) {
                //On met à jour le subscriber diffusion si on n'est pas dans la source
                if(response.id && response.type != 2) {
                    this.$set(this.diffusions[i], 'subscriber',  response);
                }
                this.$set(this.diffusions[i], 'isSubscribed', ! isSubscribed);
                this.animationBtn(index);
                

            } else {
                this.$set(this.diffusions[i], 'isSubscribed', isSubscribed);
                this.notif.error(this.messages.get('dsi', response.errorMessage));
            }
        },
        animationBtn : function(index){
            this.classAnimation = index;
            setTimeout(() => {this.classAnimation = null}, 500)
        },
        deleteDiffusionPrivate : async function(diffusionPrivate) {
            if(! confirm(this.messages.get("dsi", "diffusion_private_delete_confirm"))) {
                return;
            }
            let response = await this.ws.post("diffusionsPrivate", "delete", {
                "subscriber" : this.subscriber,
                "diffusionPrivate" : diffusionPrivate
            });
            if(! response.error) {
                let i = this.diffusionsPrivate.findIndex(d => d.id == diffusionPrivate.id);
                if(i != -1) {
                    this.$delete(this.diffusionsPrivate, i);
                }
            } else {
                this.notif.error(response.errorMessage);
            }
        },
        seeDiffusion: function(diffusionContent){

            var newWindow = window.open();
            newWindow.document.write(diffusionContent);

        }
    },
    computed : {
        filteredDiffusionsPublic : function() {
            if((! this.selectedTags.length) && (! this.searchDiffusionsPublic.length)) {
                return this.diffusions;
            }

            // Filtre par étiquette
            let filterDiffusions = this.diffusions.filter((diffusion) => {
                for(let tag of diffusion.tags) {
                    if(this.selectedTags.includes(tag.id)) {
                        return true;
                    }
                }
                return false;
            });

            if(! this.selectedTags.length){
                filterDiffusions = this.diffusions;
            }

            // Filtre par la barre de recherche
            return filterDiffusions.filter((diffusion) => {
                if(diffusion.settings.opacName.toLowerCase().match(this.searchDiffusionsPublic.toLowerCase())){
                    return true;
                }
                return false;
            });
        },
        filteredDiffusionsPrivate : function() {
            if((! this.searchDiffusionsPrivate.length)) {
                return this.diffusionsPrivate;
            }

            // Filtre par la barre de recherche
            return this.diffusionsPrivate.filter((diffusion) => {
                if(diffusion.settings.opacName.toLowerCase().match(this.searchDiffusionsPrivate.toLowerCase())){
                    return true;
                }
                return false;
            });
        }
    }
}
</script>