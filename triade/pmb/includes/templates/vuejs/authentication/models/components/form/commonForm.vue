<template>
    <div>
        <form class="form-admin" action="" method="POST" @submit.prevent="saveModel">
               <div class="form-contenu">
                    <!-- Nom du modele -->
                    <div class="row">
	                   <div class="colonne3">
	                       <label class="etiquette" for="name">{{ data.messages.model_name }}</label>
	                   </div>
	                   <div class="colonne_suite">
	                   <input type="text" name="name" id="name" v-model="modelParams.name" class="saisie-40em" required />
	                   <button type="button" @click.prevent="clear('name')" class="bouton btnCancel">X</button>
	                   </div>
	               </div>

                  <!-- Service -->
                  <div class="row">
                      <div class="colonne3">
                          <label class="etiquette" >{{ data.messages.service }}</label>
                      </div>
                      <div class="colonne_suite">
                          <label class="etiquette">{{ data.sourceName }}</label>
                      </div>
                  </div>

                 <!-- Contexte OPAC/Gestion -->
                 <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages.context }}</label>
                        </div>
                        <div class="colonne_suite">
                            <input type="checkbox" id="context_opac" name="context_opac" value="1" v-model="modelParams.context_opac" />
                            <label class="etiquette" for="context_opac" >{{ data.messages.context_opac}}</label>
                            <input type="checkbox" id="context_gestion" name="context_gestion" value="1" v-model="modelParams.context_gestion" />
                            <label class="etiquette" for="context_gestion" >{{ data.messages.context_gestion}}</label>
                        </div>
                    </div>

                 <hr />

                <!-- Parametres -->
                <component :is="sourceView" @cancel="cancel" :data="data"></component>

				<hr />

                <!-- Template -->
				<div class="row">
					<div class="colonne3">
					   <label class="etiquette" for="template">Template</label>
					</div>
					<div class="colonne_suite">
					   <textarea v-model="modelParams.template" id="template" class='saisie-40em' rows="5"></textarea>
					</div>
				</div>

				<hr />

                <!-- Attribut utilise pour l'authentification -->
                <div class="row">
                    <div class="colonne3">
                       <label class="etiquette" for="login_attr">{{ data.messages["login_attr"] }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-40em" type="text" name="login_attr" id="login_attr" v-model="modelParams.login_attr"/>
                        <button class="bouton btnCancel" type="button" @click="clear('login_attr')">X</button>
                    </div>
                </div>

                <!-- Attributs externes -->
                <div class="row">
                    <div class="colonne3">
                       <label class="etiquette" for="attrs">{{ data.messages["attrs"] }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-40em" type="text" name="attrs" id="attrs" v-model="modelParams.attrs"/>
                        <button class="bouton btnCancel" type="button" @click="clearAttrsList()">X</button>
                        <button class="bouton btnCancel" type="button" @click="updateAttrsList">
                          <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                </div>

		        <!-- Entete donnees lecteur -->
		        <template v-if="1 == modelParams.context_opac">
					<hr />
                    <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages["opac_param"] }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages["empr_data"] }}</label>
                        </div>
                        <div class="colonne_suite">
                            <select v-model="selectedAttrEmpr">
                                <option value="">{{ data.messages["choose"] }}</option>
                                <option v-for="(attr, attrIndex) in modelExtAttrs" :key="attrIndex" :value="attr">{{ attr }}</option>
                            </select>
                            <button class="bouton" type="button" @click.prevent="updateModelSelectedAttrsEmpr(selectedAttrEmpr)">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
	                <div class="row">
                        <div class="colonne3">&nbsp;</div>
	                    <div class="colonne5">
	                        <label class="etiquette">{{ data.messages.ext_attr }}</label>
	                    </div>
	                    <div class="colonne5">
	                        <label class="etiquette">{{ data.messages.empr_field }}</label>
	                    </div>
	                    <div class="colonne_suite">
	                        <label class="etiquette">{{ data.messages.trans_fct }}</label>
	                    </div>
	                </div>

	                <!-- Liste donnees lecteur -->
                    <div class="row" v-for="(attr, attrIndex) in modelEmprData" :key="'modelEmprData'+attrIndex">
	                    <!-- attribut externe -->
	                    <div class="colonne3">&nbsp;</div>
	                    <div class="colonne5">
	                        <label class="etiquette">{{ attr.attr }}</label>
	                    </div>
	
	                    <!-- champ lecteur + fonction de transfo associee -->
	                    <div class="colonne5">
	                        <select v-model="modelEmprData[attrIndex].emprField">
	                            <option value="">{{ data.messages.ignore }}</option>
	                            <option v-for="(emprField, emprFieldIndex) in modelEmprFields" :key="'emprField'+emprFieldIndex" :value="emprField">{{ emprField }}</option>
	                        </select>
	                    </div>
	                    <div class="colonne_suite">
	                        <select v-model="modelEmprData[attrIndex].transfoClass">
	                            <option value="">{{ data.messages.ignore }}</option>
	                            <option v-for="(transfoClass, transfoClassIndex) in transfo_class_list" :key="'emprTransfo'+transfoClassIndex" :value="transfoClass">{{ data.messages[transfoClass] }}</option>
	                        </select>
                            <button class="bouton" type="button" @click="deleteAttrEmpr(attrIndex)">
                                <i class="fa fa-trash"></i>
                            </button>
	                    </div>
                    </div>
                </template>

                <!-- Selection fonction de recherche lecteur -->
                <template v-if="1 == modelParams.context_opac">
                    <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages["SearchEmpr"] }}</label>
                        </div>
                        <div class="colonne_suite">
                            <select v-model="selectedEmprSearchClass">
                                <option value="">{{ data.messages["choose"] }}</option>
                                <option v-for="(emprSearchClass, emprSearchClassIndex) in empr_search_class_list" :key="emprSearchClassIndex" :value="emprSearchClass">{{ data.messages[emprSearchClass] }}</option>
                            </select>
                            <button type="button" class="bouton" @click.prevent="addEmprSearchClass(selectedEmprSearchClass)">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Liste des fonctions de recherche lecteur selectionnees -->
                    <div v-if="modelEmprSearchClass.length > 0 " class="row">
                        <div v-for="(emprSearchClass, emprSearchClassIndex) in modelEmprSearchClass" :key="emprSearchClassIndex" class="row">
                            <div class="colonne3">&nbsp;</div>
                            <div class="colonne_suite">
                                <label class="etiquette">{{ data.messages[emprSearchClass] }}</label>
                                <button type="button" @click.prevent="removeEmprSearchClass(emprSearchClass)" class="bouton">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Selection fonction de creation lecteur -->
                <template v-if="1 == modelParams.context_opac">
                    <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages["CreateEmpr"] }}</label>
                        </div>
                        <div class="colonne_suite">
                            <select v-model="modelEmprCreateClass">
                                <option value="">{{ data.messages["ignore"] }}</option>
                                <option v-for="(emprCreateClass, emprCreateClassIndex) in empr_create_class_list" :key="emprCreateClassIndex" :value="emprCreateClass">{{ data.messages[emprCreateClass] }}</option>
                            </select>
                        </div>
                    </div>
                </template>

		        <!-- Entete donnees utilisateur -->
		        <template v-if="1 == modelParams.context_gestion">
	                <hr />
	                <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages["gestion_param"] }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages.user_data }}</label>
                        </div>
                        <div class="colonne_suite">
                            <select v-model="selectedAttrUser">
                                <option value="">{{ data.messages["choose"] }}</option>
                                <option v-for="(attr, attrIndex) in modelExtAttrs" :key="attrIndex" :value="attr">{{ attr }}</option>
                            </select>
                            <button class="bouton" type="button" @click.prevent="updateModelSelectedAttrsUser(selectedAttrEmpr)">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
	                <div class="row">
                        <div class="colonne3">&nbsp;</div>
	                    <div class="colonne5">
	                        <label class="etiquette">{{ data.messages.ext_attr }}</label>
	                    </div>
	                    <div class="colonne5">
	                        <label class="etiquette">{{ data.messages.user_field }}</label>
	                    </div>
	                    <div class="colonne_suite">
	                        <label class="etiquette">{{ data.messages.trans_fct }}</label>
	                    </div>
	                </div>

	                <!-- Liste donnees utilisateur -->
                    <div class="row" v-for="(attr, attrIndex) in modelUserData" :key="'modelSelectedAttrs'+attrIndex">
	
	                    <!-- attribut externe -->
	                    <div class="colonne3">&nbsp;</div>
	                    <div class="colonne5">
	                        <label class="etiquette">{{ attr.attr }}</label>
	                    </div>
	
	                    <!-- champ utilisateur + fonction de transfo associee -->
                        <div class="colonne5">
                            <select v-model="modelUserData[attrIndex].userField">
                                <option value="">{{ data.messages.ignore }}</option>
                                <option v-for="(userField, userFieldIndex) in modelUserFields" :key="'userField'+userFieldIndex" :value="userField">{{ userField }}</option>
                            </select>
                        </div>
                        <div class="colonne_suite">
                            <select  v-model="modelUserData[attrIndex].transfoClass">
                                <option value="">{{ data.messages.ignore }}</option>
                                <option v-for="(transfoClass, transfoClassIndex) in transfo_class_list" :key="'userTransfo'+transfoClassIndex" :value="transfoClass">{{ data.messages[transfoClass] }}</option>
                            </select>
                            <button class="bouton" type="button" @click="deleteAttrUser(attrIndex)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
	                </div>
                </template>

                <!-- Selection fonction de recherche Utilisateur -->
                <template v-if="1 == modelParams.context_gestion">
                    <div class="row">
	                    <div class="colonne3">
	                        <label class="etiquette">{{ data.messages["SearchUser"] }}</label>
	                    </div>
	                    <div class="colonne_suite">
                            <select v-model="selectedUserSearchClass">
                                <option value="">{{ data.messages["choose"] }}</option>
                                <option v-for="(userSearchClass, userSearchClassIndex) in user_search_class_list" :key="userSearchClassIndex" :value="userSearchClass">{{ data.messages[userSearchClass] }}</option>
	                       </select>
                            <button type="button" class="bouton" @click.prevent="addUserSearchClass(selectedUserSearchClass)">
                                <i class="fa fa-plus"></i>
                            </button>
	                    </div>
	                </div>

                    <!-- Liste des fonctions de recherche Utilisateur selectionnees -->
                    <div v-if="modelUserSearchClass.length > 0" class="row">
                        <div v-for="(userSearchClass, userSearchClassIndex) in modelUserSearchClass" :key="userSearchClassIndex" class="row">
                            <div class="colonne3">&nbsp;</div>
                            <div class="colonne_suite">
                                <label class="etiquette">{{ data.messages[userSearchClass] }}</label>
                                <button type="button" @click.prevent="removeUserSearchClass(userSearchClass)" class="bouton">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Selection fonction de creation utilisateur -->
                <template v-if="1 == modelParams.context_gestion">
                    <div class="row">
                        <div class="colonne3">
                            <label class="etiquette">{{ data.messages["CreateUser"] }}</label>
                        </div>
                        <div class="colonne_suite">
                            <select v-model="modelUserCreateClass">
                                <option value="">{{ data.messages["ignore"] }}</option>
                                <option v-for="(userCreateClass, userCreateClassIndex) in user_create_class_list" :key="userrCreateClassIndex" :value="userCreateClass">{{ data.messages[userCreateClass] }}</option>
                            </select>
                        </div>
                    </div>
                </template>

                <!-- Boutons -->
                <div class="row">
                    <button type="button" class="bouton btnCancel" @click="cancel">{{ messages.get("common", "cancel") }}</button>
                    <button class="bouton" type="submit">{{ messages.get("common", "submit") }}</button>
                    <button type="button" class="bouton right" v-if="0 !== modelParams.id" @click="deleteModel">{{ messages.get("common", "remove") }}</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import openIDConnectForm from "../../../common/commonService/openIDConnectForm.vue";

	export default {
        props : [
        	"data", 
        	"empr_search_class_list", 
        	"empr_create_class_list", 
        	"user_search_class_list", 
        	"user_create_class_list", 
        	"transfo_class_list"
        	],
        data: function () {
            return {
                modelParams : {
                	"id": 0,
                	"template": ""
                },
                modelExtAttrs : [],
                modelUserFields : [
                    'username',
                    'nom',
                    'prenom',
                    'mail',
                ],
                modelEmprFields : [
                    'empr_cb',
                    'empr_nom',
                    'empr_prenom',
                    'empr_mail',
                    'empr_login',
                ],
                modelEmprSearchClass : [],
                modelEmprCreateClass : "",
                modelUserSearchClass : [],
                modelUserCreateClass : "",
                modelEmprData: [],
                modelUserData: [],
                selectedEmprSearchClass : "",
                selectedUserSearchClass : "",
                sourceView: "",
                sourceName: "",
                selectedAttrUser: "",
                selectedAttrEmpr: "",
            }
        },
        components : {
            openIDConnectForm,
        },
      created: function() {
       	  this.sourceName = this.data.manifest.name.value;
       	  this.sourceView = this.data.manifest.params.view.value;

          if(this.data.model) {
        	    // Recuperation des parametres du model
        	    let settings = JSON.parse(this.data.model.settings);

        	    // On les assigne
                this.$set(this.modelParams, "name", this.data.model.name);

                this.$set(this, "modelEmprData", settings.emprData ?? []);
                this.$set(this, "modelEmprSearchClass", settings.emprSearchClass ?? []);
                this.$set(this, "modelEmprCreateClass", settings.emprCreateClass ?? "");

                this.$set(this, "modelUserData", settings.userData ?? []);
                this.$set(this, "modelUserSearchClass", settings.userSearchClass ?? []);
                this.$set(this, "modelUserCreateClass", settings.userCreateClass ?? "");

                this.$set(this.modelParams, "login_attr", settings.login_attr ?? "");

                this.$set(this.modelParams, "id", this.data.model.id);
                
                this.$set(this.modelParams, "template", settings.template ?? "");

                this.$set(this, "modelExtAttrs", "");
                this.$set(this.modelParams, "attrs", []);
                if(settings.attrs && 0 < settings.attrs.length) {
                    this.$set(this.modelParams, "attrs", settings.attrs);
                	this.$set(this, "modelExtAttrs", settings.attrs.replaceAll(" ", "").split(','));
                }

                // 0 -> Rien
                // 1 -> OPAC
                // 2 -> GESTION
                // 3 -> Les deux mon capitaine
                switch(true) {
                   case (1 == this.data.model.context) :
		               this.$set(this.modelParams, "context_gestion", false);
		               this.$set(this.modelParams, "context_opac", true);
                       break;
                   case (2 == this.data.model.context) :
		               this.$set(this.modelParams, "context_gestion", true);
		               this.$set(this.modelParams, "context_opac", false);
                       break;
                   case (3 == this.data.model.context) :
		               this.$set(this.modelParams, "context_gestion", true);
		               this.$set(this.modelParams, "context_opac", true);
                       break;
                }
            }
        },
		methods: {
			cancel: function () {
			    this.$emit('cancel');
			},

           clear: function(index) {
                if('name' == index) {
                    this.modelParams.name = '';
                    return;
                }
                if('login_attr' == index) {
                    this.modelParams.login_attr = '';
                    return;
                }
                this.modelParams[index].default_value = "";
                return;
           },

            saveModel: async function () {
            	// 0 -> Rien
            	// 1 -> OPAC
            	// 2 -> GESTION
            	// 3 -> Les deux mon capitaine
            	let context = 0;

            	switch(true) {
            	   case (this.modelParams.context_opac && this.modelParams.context_gestion) :
            		   context = 3
            		   break;
            	   case (!this.modelParams.context_opac && this.modelParams.context_gestion) :
            		   context = 2
            		   break;
            	   case (this.modelParams.context_opac && !this.modelParams.context_gestion) :
            		   context = 1
            		   break;
            	}

            	let param = {
            		"id": this.modelParams.id,
            		"name": this.modelParams.name,
            		"settings": {
            			"params": this.$children[0].modelParams,
            			"template": this.modelParams.template,
	            		"attrs": this.modelParams.attrs,
	            		"login_attr": this.modelParams.login_attr,
	            		"emprData": this.modelEmprData,
	            		"emprSearchClass": this.modelEmprSearchClass,
	            		"emprCreateClass": this.modelEmprCreateClass,
	            		"userData": this.modelUserData,
	            		"userSearchClass": this.modelUserSearchClass,
	            		"userCreateClass": this.modelUserCreateClass,
            		},
            		"context" : context
            	};

            	let sourceName = this.data.sourceName ?? this.data.model.source_name;

               let response = await this.ws.post(sourceName, "saveModel", {
                   modelParams: param,
                   sourceName: sourceName,
               });
               if (response.error) {
                   if (response.errorMessage) {
                       console.error(response.errorMessage);
                   }
                   this.notif.error(this.messages.get("common", "failed_save"));
               } else {
                   this.notif.info(this.messages.get("common", "success_save"));
                   this.modelParams.id = response.id;
                   this.$root.$emit('updateModelList');
               }
           },

           deleteModel: async function () {
               let response = await this.ws.get("deleteModelForm", this.modelParams.id);
               if (response.error) {
                   if (response.errorMessage) {
                       console.error(response.errorMessage);
                   }
                   this.notif.error(this.messages.get("common", "failed_delete"));
               } else {
                   this.notif.info(this.messages.get("common", "success_delete"));
                   this.$root.$emit('deleteModelEvent');
                   this.$root.$emit('updateModelList');
               }
           },

            changeContext: function() {
                return;
            },

            addEmprSearchClass: function (className) {
                if("" == className) {
                    return;
                }
                let found = this.modelEmprSearchClass.find(element => element == className);
                if (undefined != found) {
                    return;
                }
                this.modelEmprSearchClass.push(className);
                this.selectedEmprSearchClass = '';
            },

            removeEmprSearchClass: function (className) {
                if("" == className) {
                    return;
                }
                this.modelEmprSearchClass = this.modelEmprSearchClass.filter(element => element != className);
                return;
            },

            addUserSearchClass: function (className) {
                if("" == className) {
                    return;
                }
                let found = this.modelUserSearchClass.find(element => element == className);
                if (undefined != found) {
                    return;
                }
                this.modelUserSearchClass.push(className);
                this.selectedUserSearchClass = '';
            },

            removeUserSearchClass: function (className) {
                if("" == className) {
                    return;
                }
                this.modelUserSearchClass = this.modelUserSearchClass.filter(element => element != className);
                return;
            },

            updateAttrsList: function () {
            	let attrsList = document.getElementById("attrs").value;
	           	this.$set(this, "modelUserData", []);
	           	this.$set(this, "modelEmprData", []);
           	    if("" == attrsList) {
	            	this.$set(this, "modelExtAttrs", []);
	            	return;
           	    }

            	attrsList = attrsList.replaceAll(" ", "").split(',');

	           	for(let key in attrsList) {
	           	    this.$set(this.modelUserData, key, {
	           	    	"attr":attrsList[key],
	           	    	"transfoClass":"",
	           	    	"userField":""
           	    	});
	           	    this.$set(this.modelEmprData, key, {
	           	    	"attr":attrsList[key],
	           	    	"transfoClass":"",
	           	    	"emprField":""
           	    	});
	           	}

            	this.$set(this, "modelExtAttrs", attrsList);
            },

            clearAttrsList: function () {
            	this.$set(this, "modelExtAttrs", []);
            	this.$set(this, "modelUserData", []);
            	this.$set(this, "modelEmprData", []);
                this.$set(this.modelParams, "attrs", []);
            },

            updateModelSelectedAttrsEmpr: function (attr) {
            	if("" == attr) {
                    return;
                }
                this.modelEmprData.push({
                    "attr":attr,
                    "transfoClass":"",
                    "emprField":""
                });
            },

            updateModelSelectedAttrsUser: function (attr) {
            	if("" == attr) {
                    return;
                }
                this.modelUserData.push({
                    "attr":attr,
                    "transfoClass":"",
                    "emprField":""
                });
            },

            deleteAttrUser: function (attrIndex) {
                this.modelUserData.splice(attrIndex, 1);
            },

            deleteAttrEmpr: function (attrIndex) {
                this.modelEmprData.splice(attrIndex, 1);
            },
		}
	}
</script>

