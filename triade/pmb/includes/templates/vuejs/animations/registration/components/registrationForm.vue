<template>
	<div class="container">
		<form class="form-admin" id="formAdmin">
			<div class="form-contenu">
				<div class='row'>
					<h4>{{ pmb.getMessage("animation", "animation") }} : {{ formdata.animation.name }}</h4>
				</div>
				<div class="row">
					<b>{{ pmb.getMessage("animation", "animation_date") }} :</b>
					{{ formdata.animation.event.startDate }} <template v-if="!formdata.animation.event.duringDay"> {{ pmb.getMessage("animation", "animation_au") }} {{ formdata.animation.event.endDate }}</template>
				</div>
				<div class='row' v-if="!this.formdata.animation.hasChildrens">
					<span>
						<b>{{ pmb.getMessage("animation", "list_animation_quota_available") }} :</b>
						<template v-if="formdata.animation.globalQuota >  0">{{ formdata.animation.allQuotas.availableQuotas.global }} / {{ formdata.animation.globalQuota }}</template>
						<template v-else>{{ pmb.getMessage("animation", "form_search_illimited_quotas") }}</template>
					</span>
				</div>
				<div class='row' v-if="!this.formdata.animation.hasChildrens">
					<span>
						<b>{{ pmb.getMessage("animation", "list_animation_quota_available_on_internet") }} :</b>
						<template v-if="formdata.animation.internetQuota > 0">{{ formdata.animation.allQuotas.availableQuotas.internet }} / {{ formdata.animation.internetQuota }}</template>
						<template v-else>{{ pmb.getMessage("animation", "form_search_illimited_quotas") }}</template>
					</span>
				</div>

				<animations :animations="formdata.listDaughters" :pmb="pmb" :action="action" :formdata="formdata"></animations>

				<!-- contact -->
				<hr />
				<div class='row'>
					<h3> {{ pmb.getMessage("animation", "animation_registration_contact") }} </h3>
				</div>

				<table>
					<thead>
						<tr>
							<th>{{ pmb.getMessage("animation", "list_registration_barcode") }} <sup v-if="formdata.params.animations_only_empr">*</sup></th>
							<th>{{ pmb.getMessage("animation", "list_registration_name") }} <sup>*</sup></th>
							<th>{{ pmb.getMessage("animation", "list_registration_email") }} <sup>*</sup></th>
							<th>{{ pmb.getMessage("animation", "list_registration_phone") }}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input id="registrationlist.barcode" v-model="registrationlist.barcode" type="text" class='saisie-20em' @change="getContactByBarcode"/>
								<input type="hidden" id="registrationlist.numEmpr" @change="updateContact" />
							</td>
							<td>
								<input id="registrationlist.name" v-model="registrationlist.name" type="text" class="saisie-20emr" completion="animationsEmpr" autfield="registrationlist.numEmpr" :param1="formdata.animation.id" autocomplete="off" />
							</td>
							<td>
								<input id="registrationlist.email" v-model="registrationlist.email" type="email" class='saisie-20emr' completion="animationsEmprMail" :param1="formdata.animation.id" autfield="registrationlist.numEmpr" autocomplete="off" />
							</td>
							<td>
								<input id="registrationlist.phoneNumber" v-model="registrationlist.phoneNumber" type="text" class='saisie-20em'/>
							</td>
						</tr>
					</tbody>
				</table>

				<!-- personnes inscrites -->
				<hr />
				<div class="row">
					<div class="colonne5">
						<h3> {{ pmb.getMessage("animation", "animation_registered_persons") }} {{ registrationlist.registrationListPerson.length }}</h3>
					</div>
				</div>
				<div class="row">
					<input @click="addPerson($event)" :title="pmb.getMessage('animation', 'registration_add_persons')" class="bouton" type="button" :value="pmb.getMessage('animation', 'registration_add_persons')"/>
					<input @click="addContactToRegisteredPersons($event)" :title="pmb.getMessage('animation', 'animation_add_contact_toregistered_persons')" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_add_contact_toregistered_persons')" />
				</div>
				<table class="uk-table">
					<template v-for="(person, indexPerson) in registrationlist.registrationListPerson">
						<thead>
							<tr>
								<th>{{ pmb.getMessage("animation", "list_registration_barcode") }} <sup v-if="formdata.params.animations_only_empr">*</sup></th>
								<th>{{ pmb.getMessage("animation", "list_registration_name") }} <sup>*</sup></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input v-model="person.barcode" type="text" :id="'person.barcode_'+indexPerson" class='saisie-20em' @change="getPersonByBarcode" />
									<input type="hidden" :id="'person.numEmpr_'+indexPerson" @change="updatePerson" />
								</td>
								<td>
									<input :id="'person.name_'+indexPerson" v-model="person.name" type="text" class='saisie-20emr' completion="emprunteur" :autfield="'person.numEmpr_'+indexPerson" autocomplete="off" />
								</td>
								<td>
									<div class="center">
										<input @click="deletePerson(indexPerson)" :title="pmb.getMessage('animation', 'registration_remove_persons')" class="bouton" type="button" value="X" />
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<template v-if="!formdata.animation.hasChildrens">
										<label :for="'person_' + indexPerson" class='etiquette'> {{ pmb.getMessage("animation", "animation_type") }} </label>
					 					<select :id="'person_' + indexPerson" v-model="person.numPrice" @change="getcustomField(indexPerson)" v-if="formdata.animation.prices.length > 1">
											<option v-for="(price, indexPrice) in formdata.animation.prices" :value="price.idPrice">
												{{ price.name }}
											</option>
										</select>
										<p :id="'person_' + indexPerson" v-else>{{ formdata.animation.prices[0].name }}</p>
										<div class="row">
											<template v-for="(priceAnime, indexPrice) in formdata.animation.prices" v-if="person.numPrice == priceAnime.idPrice && priceAnime.value > 0">
												<p>{{ pmb.getMessage("animation", "admin_type_price") }} : {{ priceAnime.value }}</p>
											</template>
										</div>
										<div class="row">
											<customfields :customfields="person.personCustomsFields" customprefixe="price_type" :img="formdata.img" :pmb="pmb"></customfields>
										</div>
									</template>
									<template v-else>
										<label class='etiquette'> {{ pmb.getMessage("animation", "animation_type") }} : </label>
										<template v-for="(anim, indexAnim) in animationsSelected">
											<div class="row" :id="'animation_' + anim.id + '_prices'">
												<label for="" class='etiquette'> {{ anim.name }} </label>
							 					<select v-model="person.animations[anim.id].numPrice" @change="getcustomFieldAnimation(indexPerson, anim.id)">
													<option v-for="(price, indexPrice) in anim.prices" :value="price.idPrice">
														{{ price.name }}
													</option>
												</select>
												<template v-for="(priceAnime, indexPrice) in anim.prices" v-if="person.animations[anim.id].numPrice == priceAnime.idPrice">
													{{ pmb.getMessage("animation", "admin_type_price") }} : {{ priceAnime.value }}
												</template>
											</div>
											<div class="row" :id="'animation_' + anim.id + '_cp'">
												<customfields :customfields="person.animations[anim.id].personCustomsFields" customprefixe="price_type" :img="formdata.img" :pmb="pmb" :index="indexPerson+'_'+indexAnim"></customfields>
											</div>
											<hr />
										</template>
									</template>
								</td>
							</tr>
						</tbody>
					</template>
				</table>
				<div class="row">
					<input @click="addPerson($event)" v-if="registrationlist.registrationListPerson.length > 0" :title="pmb.getMessage('animation', 'registration_add_persons')" class="bouton" type="button" :value="pmb.getMessage('animation', 'registration_add_persons')"/>
				</div>
	    	</div>
	    	<component is="script" src="./javascript/ajax.js"></component>
			<div class="row">
				<!-- Boutons -->
				<div class="left">
					<input @click="cancel" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_cancel')"/>
					<input @click="save" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_save')"/>
				</div>
				<div class="right">
					<input v-if="registrationlist.id" @click="delRegistration(registrationlist.id)" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_delete')"/>
				</div>
			</div>
   		</form>
	</div>
</template>

<script>
	import customfields from "../../../common/customFields/form/customFields.vue";
	import animations from "../../../animations/animations/components/animations.vue";

	export default {
		props : ["registrationlist", "formdata", "pmb", "action"],

		created : function() {
			window.addEventListener("load", function(event) {
				ajax_parse_elements();
			});

			this.formdata.params.animations_only_empr = parseInt(this.formdata.params.animations_only_empr);
		},

		data: function () {
			return {
				quotasAnimationCurrent : {
					"global" : this.formdata.animation.allQuotas.availableQuotas.global,
					"internet" : this.formdata.animation.allQuotas.availableQuotas.internet,
				}
			}
		},

		components : {
			customfields,
			animations
		},

		computed: {

			animationsSelected: function () {
				let animationsSelected = []
				for (const idAnimation of this.formdata.animationsSelected) {
					for (let i = 0; i < this.formdata.listDaughters.length; i++) {
						if (idAnimation == this.formdata.listDaughters[i].id) {
							animationsSelected.push(this.formdata.listDaughters[i]);
						}
					}
				}
				return animationsSelected;
			},

			animationsPricesTypesPerso: function () {
				let cp = {};
				if (this.animationsSelected && this.animationsSelected.length) {
					for (const animation of this.animationsSelected) {
						cp[animation.id] = {};
						for (const price of animation.prices) {
							cp[animation.id][price.id] = this.cloneObject(price.priceType.customFields);
						}
					}
				}
				return cp;
			}
		},

		methods : {

		    isValidContact: function () {

		        // On check le code barre
                if (this.formdata.params.animations_only_empr && this.registrationlist.barcode == "") {
                    alert(this.pmb.getMessage('animation', 'animation_registration_error_contact_barcode'));
                    return false;
                }

				if ('' === this.registrationlist.name) {
					alert(this.pmb.getMessage('animation', 'animation_registration_name_check_contact'));
					return false;
				}

                if ('' == this.registrationlist.email || !is_valid_mail(this.registrationlist.email)) {
                    alert(this.pmb.getMessage('animation', 'animation_registration_error_contact_mail'));
                    return false;
                }

                if (this.registrationlist.phoneNumber && !this.isValidPhone()) {
                    alert(this.pmb.getMessage('animation', 'animation_registration_phone_check'));
                    return false;
                }

				if (typeof this.registrationlist.numAnimation == 'undefined') {
					alert(this.pmb.getMessage('animation', 'animation_registration_animation_check'));
					return false;
				}

				return true;
		    },

		    isValidPersons: function () {

		        if (this.registrationlist.nbRegisteredPersons == 0) {
		            return false;
		        }

		        for (let person of this.registrationlist.registrationListPerson) {

                    // On check le code barre
                    if (this.formdata.params.animations_only_empr && person.barcode == "") {
                        alert(this.pmb.getMessage('animation', 'animation_registration_error_barcode'));
                        return false;
                    }

					if(person.name == ""){
						alert(this.pmb.getMessage('animation', 'animation_registration_name_check_contact_registred'));
						return false;
					}

					if (this.animationsSelected && this.animationsSelected.length) {
						for (let indexAnimation in person.animations) {
							for (let indexCp in person.animations[indexAnimation].personCustomsFields) {
								if (person.animations[indexAnimation].personCustomsFields[indexCp].customField.mandatory == '1') {
									if (this.isCustomFieldEmpty(person.animations[indexAnimation].personCustomsFields[indexCp])) {
										let msg = this.pmb.getMessage('animation', 'animation_error_cp');
										msg = msg.replace('%s', person.animations[indexAnimation].personCustomsFields[indexCp].customField.titre);
										alert(msg);
										return false;
									}
								}
							}
						}
					} else {
						for (let index in person.personCustomsFields) {
							if (person.personCustomsFields[index].customField.mandatory == '1') {
								if (this.isCustomFieldEmpty(person.personCustomsFields[index])) {
									let msg = this.pmb.getMessage('animation', 'animation_error_cp');
									msg = msg.replace('%s', person.personCustomsFields[index].customField.titre);
									alert(msg);
									return false;
								}
							}
						}
					}
				}

		        return true;
		    },

			save : function() {

			    if (this.isValidContact() && this.isValidPersons()) {

					let data = new FormData();
					let dataContent = this.registrationlist;
					dataContent.animationsSelected = this.formdata.animationsSelected;
					data.append('data', JSON.stringify(dataContent));
					let url = "./ajax.php?module=animations&categ=registration&action=save";

					fetch(url, {
						method: 'POST',
						body: data
					}).then((response)=> {
						if (response.ok) {
							response.text().then((id)=> {
								document.location = './animations.php?categ=animations&action=view&id='+this.registrationlist.numAnimation;
						    });
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					}).catch((error) => {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
			    }
			},

			cancel : function() {
				history.go(-1);
			},

			delRegistration : function(id) {
				const resultat = window.confirm(this.pmb.getMessage('animation', 'animation_confirm_del_registration'));

				if (resultat == 0) {
					event.preventDefault();
				} else {
					let url = "./ajax.php?module=animations&categ=registration&action=delete";
					let data = new FormData();
					data.append('data', JSON.stringify({id: id}));

					fetch(url, {
						method: 'POST',
						body: data
					}).then(function(response) {
						if (response.ok) {
							document.location = './animations.php?categ=registration&action=list';
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					})
					.catch(function(error) {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
				}
			},

			deletePerson : function(index) {
				this.registrationlist.registrationListPerson.splice(index, 1);
				this.registrationlist.nbRegisteredPersons--;

				if (this.formdata.animation.allQuotas.availableQuotas.global < this.quotasAnimationCurrent.global){
					this.formdata.animation.allQuotas.availableQuotas.global++
				}

			},

			cloneObject : function(object) {
			    let newObject = new Object();
			    for (let index in object) {
			        if (typeof object[index] != 'object') {
			            newObject[index] = object[index];
			        } else if (Array.isArray(object[index])) {
						newObject[index] = [];
						for (let i = 0; i < object[index].length; i++) {
							if (Array.isArray(object[index][i])) {
								newObject[index].push(object[index][i])
							} else {
								newObject[index].push(this.cloneObject(object[index][i]))
							}
						}
					} else {
						newObject[index] = this.cloneObject(object[index]);
					}
			    }
			    return newObject;
			},

			addPerson : function(event) {
			  	//on verifie s'il reste des places disponibles
				const quotasRestant = this.checkQuotas();
				if (!quotasRestant) {
					alert(this.pmb.getMessage('animation', 'animation_registration_max_registered'));
					return;
				}

				//on verifie s'il reste des places disponibles sur les animation selectionner
				if (this.formdata.animationsSelected && this.formdata.animationsSelected.length > 0){
				    let quotas = this.getAnimationsSelectedQuotas();
					for (let quota of quotas) {
					    if (quota.places == 0) {
					        let msg = this.pmb.getMessage('animation', 'animation_selected_registration_max_registered');
					        msg = msg.replace('%s', quota.animation);
					        alert(msg);
							return;
						}
					}
				}


				if (false === this.checkAvailablePlace() && false === this.allowWaitingList ) {
					alert(this.pmb.getMessage('animation', 'add_animation_alert_max'));
					return;
				}

				this.registrationPushNewPerson({
					numRegistration : 0,
					numEmpr : 0,
					barcode : '',
					name : '',
				});

				this.$nextTick(function () {
					let lastId = this.registrationlist.registrationListPerson.length - 1;
					let elt = document.getElementById('person.name_' + lastId);
  					ajax_pack_element_without_spans(elt, event);
				})
			},

			addContactToRegisteredPersons : function(event) {

			  	//on verifie s'il reste des places disponibles
				const quotasRestant = this.checkQuotas();
				if (!quotasRestant) {
					alert(this.pmb.getMessage('animation', 'animation_registration_max_registered'));
					return;
				}

				//on verifie s'il reste des places disponibles sur les animation selectionner
				if (this.formdata.animationsSelected && this.formdata.animationsSelected.length > 0){
					let quotas = this.getAnimationsSelectedQuotas();
					for (let quota of quotas) {
						if (quota.places == 0) {
							let msg = this.pmb.getMessage('animation', 'animation_selected_registration_max_registered');
							msg = msg.replace('%s', quota.animation);
							alert(msg);
							return;
						}
					}
				}

				if (false === this.checkAvailablePlace() && false === this.allowWaitingList ) {
					alert(this.pmb.getMessage('animation', 'add_animation_alert_max'));
					return;
				}

				this.registrationPushNewPerson({
					numRegistration : 0,
					numEmpr : this.registrationlist.numEmpr,
					barcode : this.registrationlist.barcode,
					name : this.registrationlist.name,
				});

				this.$nextTick(function () {
					let lastId = this.registrationlist.registrationListPerson.length - 1;
					document.getElementById('person.numEmpr_' + lastId).value = this.registrationlist.numEmpr;
					let elt = document.getElementById('person.name_' + lastId);
  					ajax_pack_element_without_spans(elt, event);
				})
			},
			getContactByBarcode : function(event) {
				let contact_barcode = event.target.value;
				this.getEmprByBarcode(contact_barcode).then ( (response) => {
					let empr_name = response.empr_nom;
					if('' != response.empr_prenom) {
						empr_name = empr_name+' '+response.empr_prenom;
					}
					this.registrationlist.numEmpr = response.id_empr;
					this.registrationlist.name = empr_name;
					this.registrationlist.barcode = response.empr_cb;
					this.registrationlist.email = response.empr_mail;
					this.registrationlist.phoneNumber = response.empr_tel1;
				}).catch(function(error) {
				});
			},
			getPersonByBarcode : function(event) {
				let person_id = event.target.id.split('_')[1];
				let person_barcode = event.target.value;
				this.getEmprByBarcode(person_barcode).then ( (response) => {
					let empr_name = response.empr_nom;
					if('' != response.empr_prenom) {
						empr_name = empr_name+' '+response.empr_prenom;
					}
					this.registrationlist.registrationListPerson[person_id].name = empr_name;
					this.registrationlist.registrationListPerson[person_id].barcode = response.empr_cb;
				}).catch(function(error) {
					console.error(error);
				});
			},
			checkAvailablePlace : function() {
				// Dans le cas des quotas illimit�s
				if (this.formdata.animation.allQuotas.animationQuotas.global == 0) {
					return true;
				}
				return (this.formdata.animation.allQuotas.availableQuotas.global - this.registrationlist.registrationListPerson.length > 0);
			},
			updateContact : function(event) {
				this.registrationlist.numEmpr = document.getElementById('registrationlist.numEmpr').value;
				this.registrationlist.name = document.getElementById('registrationlist.name').value;
				let empr_id = event.target.value;
				this.getEmprById(empr_id).then ( (response) => {
					let empr_name = response.empr_nom;
					if('' != response.empr_prenom) {
						empr_name = empr_name+' '+response.empr_prenom;
					}
					this.registrationlist.name = empr_name;
					this.registrationlist.barcode = response.empr_cb;
					this.registrationlist.email = response.empr_mail;
					this.registrationlist.phoneNumber = response.empr_tel1;
				}).catch(function(error) {
				});
			},
			updatePerson : function(event) {
				let person_id = event.target.id.split('_')[1];
				this.registrationlist.registrationListPerson[person_id].numEmpr = document.getElementById('person.numEmpr_'+person_id).value;
				this.registrationlist.registrationListPerson[person_id].name = document.getElementById('person.name_'+person_id).value;
				let empr_id = event.target.value;
				this.getEmprById(empr_id).then ( (response) => {
					let empr_name = response.empr_nom;
					if('' != response.empr_prenom) {
						empr_name = empr_name+' '+response.empr_prenom;
					}
					this.registrationlist.registrationListPerson[person_id].name = empr_name;
					this.registrationlist.registrationListPerson[person_id].barcode = response.empr_cb;
				}).catch(function(error) {
				});
			},
			getEmprById : function(id) {
				let url = "./ajax.php?module=ajax&categ=empr&fname=get_empr_by_id&id="+id;
				return this.queryEmpr(url);
			},
			getEmprByBarcode : function(barcode) {
				let url = "./ajax.php?module=ajax&categ=empr&fname=get_empr_by_barcode&barcode="+barcode;
				return this.queryEmpr(url);
			},
			queryEmpr(url) {
				return new Promise(function(resolve, reject) {
					fetch(url).then( (response) => {
						if (!response.ok) {
							reject();
						} else {
							response.json().then((jsonContent) => {
								resolve(jsonContent);
							})
							.catch((error) =>{
								reject();
							});
						}
					})
					.catch(function(error) {
						reject();
					});
				});
			},
			getcustomField : function(indexPerson) {
				for (let price of this.formdata.animation.prices) {
					if (price.idPrice == this.registrationlist.registrationListPerson[indexPerson].numPrice) {
						this.registrationlist.registrationListPerson[indexPerson].personCustomsFields = this.cloneObject(price.priceType.customFields);
					}
				}
			},

			getcustomFieldAnimation : function(indexPerson, idAnimation) {
			    if (this.formdata.animationsSelected && this.formdata.animationsSelected.length) {
			        let person = this.registrationlist.registrationListPerson[indexPerson];
					if (this.animationsPricesTypesPerso[idAnimation]) {
						let prices = this.animationsPricesTypesPerso[idAnimation];
						if (prices[person.animations[idAnimation].numPrice]) {
							person.animations[idAnimation].personCustomsFields = this.cloneObject(prices[person.animations[idAnimation].numPrice])
						}
					}
				}
			},

			isValidPhone : function() {
				let tempPhone = this.registrationlist.phoneNumber.replace(/[\W\s]/gm, '');
				return !isNaN(tempPhone);
			},
			isCustomFieldEmpty : function(customField) {
				let flag = true;
				let value = customField.customValues[0].value;

				switch (true) {
					case (Array.isArray(value) && value.length != 0) :
					case (typeof value == 'string' && value != '') :
					case (typeof value == 'number') :
						flag = false;
						break;
				}

				return flag;
			},
			checkQuotas : function(){
				//Si on a pas limit� les inscriptions, on continue
				if (this.formdata.animation.allQuotas.animationQuotas.global == 0) {
					return true;
				}

				//inscription en liste d'attente, on continue
				if (this.formdata.animation.allowWaitingList == 1) {
					return true;
				}

				if (this.formdata.animation.allQuotas.availableQuotas.global > 0){
					this.formdata.animation.allQuotas.availableQuotas.global--
					return true;
				}

				return false;
			},

			registrationPushNewPerson : function (newPerson) {
				if (this.formdata.animationsSelected && this.formdata.animationsSelected.length) {
					newPerson.animations = {};
					for (let i = 0; i < this.animationsSelected.length; i++) {
						newPerson.animations[this.animationsSelected[i].id] = {
							numAnimation: this.animationsSelected[i].id,
							numPrice: this.animationsSelected[i].prices[0].idPrice,
							personCustomsFields: this.cloneObject(this.animationsSelected[i].prices[0].priceType.customFields)
						};
					}
					this.registrationlist.registrationListPerson.push(newPerson);
				} else {
					newPerson.personCustomsFields = this.cloneObject(this.formdata.animation.prices[0].priceType.customFields);
					newPerson.numPrice = this.formdata.animation.prices[0].idPrice;
					this.registrationlist.registrationListPerson.push(newPerson);
				}
				this.registrationlist.nbRegisteredPersons++;
			},

			getAnimationsSelectedQuotas : function() {

			    var quotas = [];

				for (let idAnimation of this.formdata.animationsSelected) {
					for (var i = 0; i < this.formdata.listDaughters.length; i++) {

				    	if (idAnimation == this.formdata.listDaughters[i].idAnimation) {

				    	    var quotasRestant = 0;

							//Si on a pas limit� les inscriptions, on continue
							if (this.formdata.listDaughters[i].allQuotas.animationQuotas.global == 0) {
							    quotasRestant = true;
							}

							//inscription en liste d'attente, on continue
							if (this.formdata.listDaughters[i].allowWaitingList == 1) {
							    quotasRestant = true;
							}

							if (quotasRestant != true) {
								if (this.formdata.listDaughters[i].allQuotas.availableQuotas.global && this.formdata.listDaughters[i].allQuotas.availableQuotas.internet == 0){
									quotasRestant = this.formdata.listDaughters[i].allQuotas.availableQuotas.global--;
								}
								if (this.formdata.listDaughters[i].allQuotas.availableQuotas.internet > 0){
									quotasRestant = this.formdata.listDaughters[i].allQuotas.availableQuotas.global--;
									quotasRestant = this.formdata.listDaughters[i].allQuotas.availableQuotas.internet--;
								}
							}

						    quotas.push({
					        	animation: this.formdata.listDaughters[i].name,
					        	id: idAnimation,
					        	places:quotasRestant
				        	});
				    	}
					}
				}
				return quotas;
			},
		}
	}
</script>