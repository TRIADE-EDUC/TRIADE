<template>
    <div class="container">
        <div class="form-contenu">
            <form class="form-animations" action="" method="POST" @submit="sendSearch">
                <h3>{{ pmb.getMessage("animation", "animations") }}</h3>
                <div class="form-contenu">
                      <div class="row">
                        <label for="search_text">{{ pmb.getMessage("animation", "form_search_tlc") }}</label>
                    </div>
                      <div class="row">
                        <input type="text" class="saisie-80em" v-model="searchData.tlc" name="search_text" id="search_text"/>
                    </div>
                    <div class="row">
                        <span class="saisie-contenu">
                            {{ pmb.getMessage("animation", "form_search_saisie") }}
                            <a class="aide" href="./help.php?whatis=regex" onclick="aide_regex(); return false;">{{ pmb.getMessage("animation", "form_search_boolean_expression") }}</a>
                        </span>
                    </div>
                    <div class="row">
                          <div class="row">
                              <label for="search_date_start">{{ pmb.getMessage("animation", "form_search_date") }}</label>
                          </div>
                          <div class="row">
                              <input id="searchExactDate" name="searchExactDate" type="radio" :value="true" v-model="searchData.inputSearchExactDate"/>
                              <label for="searchExactDate">{{ pmb.getMessage("animation", "form_search_exact_date") }}</label>
                              <input id="searchSinceDate" name="searchSinceDate" type="radio" :value="false" v-model="searchData.inputSearchExactDate"/>
                              <label for="searchSinceDate">{{ pmb.getMessage("animation", "form_search_date_parution_start") }}</label>
                              <input id="searchDateStart" name="searchDateStart" type="date" v-model="searchData.dateStart" :max="searchData.dateEnd"/>
                              <label for="searchDateEnd">{{ pmb.getMessage("animation", "form_search_date_parution_end") }}</label>
                              <input id="searchDateEnd" name="searchDateEnd" type="date" v-model="searchData.dateEnd" :min="searchData.dateStart" :disabled="searchData.inputSearchExactDate"/>
                          </div>
                    </div>
                    <div class="row">
                        <div id="filters" class="row uk-clearfix">
                        <img :src='formdata.img.plus' class="img_plus" name="imEx" id="filtersImg" style='border:0px; margin:3px 3px' onClick="expandBase('filters', true); return false;">
                            <label>{{ pmb.getMessage("animation", "form_search_filter") }}</label>
                        </div>
                        <div id="filtersChild" style="display:none;">
                            <div class="row">
                                <div class="colonne2">
                                    <div class="row">
                                        <label for="search_status">{{ pmb.getMessage("animation", "form_search_status") }}</label>
                                    </div>
                                    <select name="search_status" id="search_status" v-model="filter.status" multiple @click="searchResultWithFilters = 0">
                                        <option value="0" selected>{{ pmb.getMessage("animation", "form_search_all_status") }}</option>
                                        <option v-for="statut in formdata.status" :value="statut.id_status" :key="statut.id_status">
                                            {{ statut.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="colonne_suite">
                                    <div class="row">
                                        <label for="search_localisation">{{ pmb.getMessage("animation", "form_search_location") }}</label>
                                    </div>
                                    <select name="search_localisation" id="search_localisation" v-model="filter.locations" multiple @click="searchResultWithFilters = 0">
                                        <option value="0">{{ pmb.getMessage("animation", "form_search_all_location") }}</option>
                                        <option v-for="location in formdata.locations" :value="location.idlocation" :key="location.idlocation">{{ location.location_libelle }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row"></div>
                            <div class="row">
                                <div class="colonne2">
                                    <div class="row">
                                        <label for="search_types">{{ pmb.getMessage("animation", "form_search_type") }}</label>
                                    </div>
                                    <select name="search_types" id="search_types" v-model="filter.types" multiple @click="searchResultWithFilters = 0">
                                        <option value="0" selected>{{ pmb.getMessage("animation", "form_search_all_types") }}</option>
                                        <option v-for="type in formdata.types" :value="type.id_type" :key="type.id_type">{{ type.label }}</option>
                                    </select>
                                </div>
                                <div class="colonne_suite">
                                    <div class="row">
                                        <label for="search_communication_type">{{ pmb.getMessage("animation", "form_search_communication_type") }}</label>
                                    </div>
                                    <select name="search_localisation" id="search_localisation" v-model="filter.communication_type" multiple @click="searchResultWithFilters = 0">
                                        <option value="0">{{ pmb.getMessage("animation", "form_search_all_communication_type") }}</option>
                                        <option
                                            v-for="communication_type in formdata.communication_type"
                                            :value="communication_type.id"
                                            :key="communication_type.id">
                                                {{ communication_type.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="left">
                            <input type="submit" class="bouton" value="Rechercher" />
                            <p v-if="error" style="color:red;">{{ error }}</p>
                        </div>
                    </div>
                </div>
              </form>
               <div class="row uk-clearfix align_left">
                <p>{{ pmb.getMessage("animation", "form_search_animations_search") }} => <b>{{ searchResult.total }}</b> {{ pmb.getMessage("animation", "form_search_animations_search_resultat") }}</p>
            </div>
        </div>
        <div class="result-contenu" v-if="searchResult.total">
              <div class="result">
                <table class="uk-table uk-table-small uk-table-striped uk-table-middle">
                    <thead>
                        <tr>
                            <th style="cursor:pointer;" @click="setSort('name')">
                                {{ pmb.getMessage("animation", "list_animation_name") }} <i :class="getClassSort('name')"></i>
                            </th>
                            <th style="cursor:pointer;">
                                {{ pmb.getMessage("animation", "list_animation_location") }}
                            </th>
                            <th style="cursor:pointer;" @click="setSort('event.rawStartDate')">
                                {{ pmb.getMessage("animation", "update_add_animation_startDate") }} <i :class="getClassSort('event.rawStartDate')"></i>
                            </th>
                            <th style="cursor:pointer;" @click="setSort('event.rawEndDate')">
                                {{ pmb.getMessage("animation", "update_add_animation_endDate") }} <i :class="getClassSort('event.rawEndDate')"></i>
                            </th>
                            <th style="cursor:pointer;" @click="setSort('status.label')">
                                {{ pmb.getMessage("animation", "list_animation_status") }} <i :class="getClassSort('status.label')"></i>
                            </th>
                            <th style="cursor:pointer;" @click="setSort('type.label')">
                                {{ pmb.getMessage("animation", "list_animation_type") }} <i :class="getClassSort('type.label')"></i>
                            </th>
                            <th style="cursor:pointer;" @click="setSort('calendar.name')">
                                {{ pmb.getMessage("animation", "list_animation_calendar") }} <i :class="getClassSort('calendar.name')"></i>
                            </th>
                            <th style="cursor:pointer;">
                                {{ pmb.getMessage("animation", "list_animation_available_place") }}
                            </th>
                            <th style="cursor:pointer;">
                                {{ pmb.getMessage("animation", "list_animation_reserved_place") }}
                            </th>
                            <th style="cursor:pointer;">
                                {{ pmb.getMessage("animation", "update_add_animation_mailing") }}
                            </th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(animation, index) in searchResult.result" :key="index" @mouseover="hover = index" @mouseout="hover = -1"
                            :class="[ index%2 == 0 ? 'even' : 'odd', index == hover ? 'surbrillance' : '' ]"
                            style='cursor: pointer'>
                            <td @click="view(animation.id)">{{ animation.name }}</td>
                            <td @click="view(animation.id)">
                                <template v-if="animation.location.length">
                                    <template v-for="loc in animation.location">
                                        {{ loc.locationLibelle }}
                                    </template>
                                </template>
                                <template v-else>{{ pmb.getMessage("animation", "form_search_no_location") }}</template>
                            </td>
                            <td @click="view(animation.id)">
                                <template v-if="animation.event">{{ animation.event.startDate }}</template>
                                <template v-else>{{ pmb.getMessage("animation", "form_search_no_event") }}</template>
                            </td>
                            <td @click="view(animation.id)">
                                <template v-if="!animation.event.duringDay">{{ animation.event.endDate }}</template>
                                <template v-else>{{ pmb.getMessage("animation", "form_event_during_day") }}</template>
                            </td>
                            <td @click="view(animation.id)">
                                <template v-if="animation.status.label">{{ animation.status.label }}</template>
                                <template v-else>{{ pmb.getMessage("animation", "form_search_no_status") }}</template>
                            </td>
                            <td @click="view(animation.id)">
                                <template>{{ animation.type.label }}</template>
                            </td>
                            <td @click="view(animation.id)">
                                <template>{{ animation.calendar.name }}</template>
                            </td>
                            <td @click="view(animation.id)">
                                <template v-if='animation.hasChildrens'>
                                    {{ pmb.getMessage("animation", "form_search_NA") }}
                                </template>
                                <template v-else-if="animation.allQuotas.animationQuotas.global">
                                    <template v-if="(typeof animation.allQuotas.availableQuotas.global) !== 'undefined'">
                                        {{ animation.allQuotas.availableQuotas.global }} &sol; {{ animation.allQuotas.animationQuotas.global }}
                                    </template>
                                </template>
                                <template v-else>
                                    {{ pmb.getMessage("animation", "form_search_illimited_quotas") }}
                                </template>
                            </td>
                            <td @click="view(animation.id)">
                                <template v-if='animation.hasChildrens'>
                                    {{ pmb.getMessage("animation", "form_search_NA") }}
                                </template>
                                <template v-else-if="animation.allQuotas.reserved.global >= 0 || animation.allQuotas.reserved.internet >= 0">
                                    {{ animation.allQuotas.reserved.global + animation.allQuotas.reserved.internet }}
                                </template>
                            </td>
                            <td @click="view(animation.id)">
                                <template v-if='animation.mailingType.length == 0'>
                                    {{ pmb.getMessage("animation", "update_add_animation_noMailing") }}
                                </template>
                                <template v-else v-for="mailing in animation.mailingType">
                                    {{ mailing.name }}
                                </template>
                            </td>
                            <td class="center">
                                <template v-if="!animation.event.dateExpired">
                                    <template v-if="(animation.allQuotas.availableQuotas.global > 0 && animation.allQuotas.animationQuotas.global && animation.hasChildrens === false) || (animation.allQuotas.animationQuotas.global == 0 && animation.hasChildrens === false) || (animation.allowWaitingList == 1 && animation.hasChildrens === false)">
                                        <div class="center">
                                            <i @click="addRegistration(animation.id)" class="fa fa-user-plus" :title="pmb.getMessage('animation', 'animation_add_registration')"></i>
                                        </div>
                                    </template>
                                </template>
                            </td>
                            <td @click.stop="" class="center">
                                <div class="center">
                                    <i class="fa fa-envelope" @click="mailing(animation)" :title="pmb.getMessage('animation', 'animation_go_mailing')"></i>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>


                <paginator
                    :total="searchResult.total"
                    :current-page="searchResult.currentPage"
                    :nbPerPage="searchResult.nbPerPage"
                    @switchPage="switchPage($event)"
                />
              </div>
        </div>
    </div>
</template>

<script>
import paginator from "@/common/navbar/paginator.vue";

export default {
  props: ["pmb", "formdata"],
  data: function() {
    return {
        searchData : {
            tlc : '',
            dateStart : '',
            dateEnd : '',
            inputSearchExactDate : true
        },
        searchResult : {
            currentPage: 0,
            total: 0,
            nbPerPage: 20,
            result: [],
        },
        // filtredResult : [],
        error : '',
        hover : -1,
        filter : {
            status: ['0'],
            locations: ['0'],
            types: ['0'],
            communication_type: ['0']
        },
        sort: {
            name: '',
            order: '',
        }
    };
  },
  components : {
      paginator
  },
  computed : {
    //   filtreResult: function () {
    // 	  this.filtredResult.splice(0);
    // 	  for (let animation of this.searchResult.result) {
    // 		  if (this.filterAnimation(animation)) {
    // 			  this.filtredResult.push(animation);
    // 		  }
    // 	  }
    // 	  return this.filtredResult;
    //   },
  },
  methods: {
    switchPage: async function(event) {

        if ((typeof this.showLoader) === 'function') {
            this.showLoader();
        }

        this.completeForm();

        try {
            let data = new FormData();
            data.append('page', event.page)
            data.append('data', JSON.stringify({
                searchFields : this.searchData,
                filter: this.filter
            }));

            const result = await fetch("./ajax.php?module=animations&categ=animations&action=search", {
                method : "POST",
                body: data
            });
            this.searchResult = await result.json();
            document.activeElement.blur();
        } catch (e) {
            console.error(e.message || e);
        }

        if ((typeof this.hiddenLoader) === 'function') {
            this.hiddenLoader();
        }
    },
    sendSearch : async function(event) {
        event.preventDefault();

        if ((typeof this.showLoader) === 'function') {
            this.showLoader();
        }

        this.completeForm();

        try {
            let data = new FormData();
            data.append('data', JSON.stringify({
                searchFields : this.searchData,
                filter: this.filter
            }));

            const result = await fetch("./ajax.php?module=animations&categ=animations&action=search", {
                method : "POST",
                body: data
            });
            this.searchResult = await result.json();
            document.activeElement.blur();
        } catch (e) {
            console.error(e.message || e);
        }

        if ((typeof this.hiddenLoader) === 'function') {
            this.hiddenLoader();
        }
    },
    completeForm : function () {
        let searchFlag = false;
        let fields = Object.keys(this.searchData);

        for (let field of fields) {
            if (typeof this.searchData[field] === 'string') {
                if (this.searchData[field] !== '') {
                    searchFlag = true;
                }
            } else if (typeof this.searchData[field] === 'object') {
                if (this.searchData[field].length !== 0) {
                    if (this.searchData[field][0] !== '') {
                        searchFlag = true;
                    }
                }
            }
        }

        if (!searchFlag) {
            this.searchData.tlc = '*';
        }
    },
    setSort : function (sortName) {
        this.sort.name = sortName;

        if (this.sort.order == "asc") {
            this.sort.order = "desc";
        } else {
            this.sort.order = "asc";
        }

        var sortTab = sortName.split('.');
        // TODO : Arriver a gerer le filter pour aller chercher dans des sous objets
        var sort = this.filter.order;

        this.searchResult.result.sort(function (a, b) {
            for (let i = 0; i < sortTab.length; i++) {
                if (a[sortTab[i]]) {
                    if (Array.isArray(a[sortTab[i]])) {
                        a = a[sortTab[i]][0];
                    } else {
                        a = a[sortTab[i]];
                    }
                } else {
                    a = '';
                }
                if (b[sortTab[i]]) {
                    if (Array.isArray(b[sortTab[i]])) {
                        b = b[sortTab[i]][0];
                    } else {
                        b = b[sortTab[i]];
                    }
                } else {
                    b = '';
                }
            }
            if (a.toLowerCase() == b.toLowerCase()) {
                return 0;
            }
            if (sort == "asc") {
                return (a.toLowerCase() < b.toLowerCase()) ? - 1 : 1;
            } else {
                return (a.toLowerCase() > b.toLowerCase()) ? - 1 : 1;
            }
        })
    },
    getClassSort : function (name) {
        var className = "fa fa-sort";

        if (this.filter.name == name) {
            if (this.filter.sort == "asc") {
                className += "-asc";
            } else {
                className += "-desc";
            }
        }
        return className;
    },

    filterAnimation : function(animation) {
        // On filtre en fonction du statut
        let status = false;
        if (this.filter.status.includes('0') || (animation.status && this.filter.status.includes(animation.status.id))) {
            status = true;
        }

        // On filtre en fonction de la localisation

        let location = false;
        let flagLoc = false;
        if (animation.location) {
            for (let i = 0; i < animation.location.length; i++) {
                if (this.filter.locations.includes(animation.location[i].id)) {
                    flagLoc = true;
                }
            }
        }
        if (this.filter.locations.includes('0') || flagLoc) {
            location = true;
        }

        // On filtre en fonction du type
        let types = false;
        if (this.filter.types.includes('0') || (animation.type && this.filter.types.includes(animation.type.id))) {
            types = true;
        }

        // On filtre en fonction du type de communication
        let comType = false;
        let flagComType = false;
        if (0 < animation.mailingType.length) {
            for (let i = 0; i < animation.mailingType.length; i++) {
                if (this.filter.communication_type.includes(parseInt(animation.mailingType[i].id))) {
                    comType = true;
                }
            }
        }
        if (this.filter.communication_type.includes('0') || comType) {
            flagComType = true;
        }

        // On verifie si on doit afficher ou non cette animation
        if (status && location && types && flagComType) {
            return true;
        }
        return false;
    },

    view : function(id) {
        document.location = './animations.php?categ=animations&action=view&id=' + id;
    },

    addRegistration : function(id) {
        document.location = './animations.php?categ=registration&action=add&numAnimation=' + id;
    },
    mailing : function(animation) {
        //on teste s'il y a des inscrits
        if (animation.allQuotas.reserved.global){
            document.location = './animations.php?categ=animations&action=mailing&id=' + animation.id;
        } else {
            alert(this.pmb.getMessage('animation', 'animation_mailing_no_registred_persons'));
        }
    }
  }
};
</script>
