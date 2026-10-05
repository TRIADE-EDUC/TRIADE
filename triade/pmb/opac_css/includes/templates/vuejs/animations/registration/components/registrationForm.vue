<template>
	<div id="registrationForm" class="row">
        <h1 class="registration_intro" v-if="formdata.params.opac_rgaa_active">
          {{ pmb.getMessage("animation", "animation_registration_title") }}
        </h1>
		<h2 class="registration_intro" v-else>
		  {{ pmb.getMessage("animation", "animation_registration_title") }}
        </h2>
		<p v-if="!formdata.idEmpr">{{ pmb.getMessage("animation", "animation_registration_intro") }} <em>{{ pmb.getMessage("animation", "animation_registration_intro_info") }}</em></p>
		<form class="form-registration" action="#" method="POST" @submit="save">
			<div class="row registration_form_animation_name">
				<h2>
                    {{ pmb.getMessage("animation", "animation") }} : {{ formdata.animation.name }}
                </h2>
			</div>
			<div class="row registration_form_registration_date">
				<p>
					{{ pmb.getMessage("animation", "animation_registration_date") }} :
					{{ formdata.animation.event.startDate }} <template v-if="!formdata.animation.event.duringDay">{{ pmb.getMessage("animation", "animation_registration_date_to") }} {{ formdata.animation.event.endDate }}</template>
				</p>
			</div>


			<template v-if="formdata.listDaughters && formdata.listDaughters.length > 0">
				<p><em>{{ pmb.getMessage("animation", "animation_registration_explanation_required") }} </em></p>
				<animations :animations="formdata.listDaughters" :pmb="pmb" :formdata="formdata" @input="changeAnimationsSelected"></animations>
			</template>

			<!-- contact -->
			<hr />
			<div class='row registration_form_registration_contact'>
				<h3>{{ pmb.getMessage("animation", "animation_registration_contact") }}</h3>
			</div>
			<table class='row registration_form_registration_table'>
				<tr class='registration_form_registration_table_name'>
					<td>{{ pmb.getMessage("animation", "animation_registration_name") }} <sup>*</sup> :</td>
					<td>
						<input id="name" v-model="registration.name" type="text" class="saisie-20emr" required/>
					</td>
				</tr>
				<tr  class='registration_form_registration_table_email'>
					<td>{{ pmb.getMessage("animation", "animation_registration_email") }} <sup>*</sup> :</td>
					<td>
						<input id="email" v-model="registration.email" type="email" class='saisie-20emr' required/>
					</td>
				</tr>
				<tr class='registration_form_registration_table_phoneNumber'>
					<td>{{ pmb.getMessage("animation", "animation_registration_phone") }} :</td>
					<td>
						<input id="phoneNumber" v-model="registration.phoneNumber" type="text" class='saisie-20em'/>
					</td>
				</tr>
				<tr class='registration_form_registration_table_barcode'>
					<td>{{ pmb.getMessage("animation", "animation_registration_barcode") }} <sup v-if="formdata.params.animations_only_empr">*</sup> :</td>
					<td>
						<input id="barcode"
						  v-model="registration.barcode"
						  type="text" class='saisie-20em'
						  :required="formdata.params.animations_only_empr == 1"/>
					</td>
				</tr>
			</table>

			<!-- personnes inscrites -->
			<div class="row registration_form_registration_subscribe_person_form" v-if="!formdata.animation.uniqueRegistration">
				<hr />
				<div class="row registration_form_registration_nb_person">
					<h3> {{ pmb.getMessage("animation", "animation_registration_persons") }} {{ registration.registrationListPerson.length }}</h3>
				</div>
				<div class="row registration_form_registration_button_person">
                    <button class="bouton" type="button" @click="addPerson">
                        {{ pmb.getMessage('animation', 'animation_registration_add_person') }}
                    </button>
					<template v-if="can_add_contact">
	                    <button class="bouton" type="button" @click="addContactToRegisteredPersons">
	                        {{ pmb.getMessage('animation', 'animation_registration_add_contact_toregistered_persons') }}
	                    </button>
					</template>
					<template v-else>
	                    <button disabled="true" class="bouton" type="button" :title="pmb.getMessage('animation', 'animation_registration_contact_already_add')">
	                        {{ pmb.getMessage('animation', 'animation_registration_add_contact_toregistered_persons') }}
	                    </button>
					</template>
				</div>
				<br>
				<table class="registration_form_registration_person_list">
					<template v-for="(person, indexPerson) in registration.registrationListPerson">
						<thead>
							<tr>
								<th>{{ pmb.getMessage("animation", "animation_registration_barcode") }} <sup v-if="formdata.params.animations_only_empr">*</sup></th>
								<th>{{ pmb.getMessage("animation", "animation_registration_name") }} <sup>*</sup></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input :id="'person.barcode_'+indexPerson"
									   v-model="person.barcode"
									   type="text" class='saisie-20em'
									   @change="barcodeNotAlreadyUse(indexPerson)"
									   :required="formdata.params.animations_only_empr == 1"/>
								</td>
								<td>
									<input :id="'person.name_'+indexPerson" v-model="person.name" type="text" class='saisie-20emr' required/>
								</td>
								<td>
									<div class="center">
				                        <button class="bouton" type="button" @click="deletePerson(indexPerson)">
				                            {{ pmb.getMessage('animation', 'animation_registration_remove_cross') }}
				                        </button>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<label :for="'person_numPrice_' + indexPerson" class='etiquette'> {{ pmb.getMessage("animation", "animation_registration_price_type") }} <sup>*</sup> :</label>
									<template v-if="!formdata.animation.hasChildrens">
					 					<select :id="'person_numPrice_' + indexPerson" v-model="person.numPrice" @change="getcustomField(indexPerson)" required v-if="formdata.animation.prices.length > 1">
											<option v-for="(price, indexPrice) in formdata.animation.prices" :value="price.idPrice">
												{{ price.name }}
											</option>
										</select>
										<p :id="'person_numPrice_' + indexPerson" v-else>{{ formdata.animation.prices[0].name }}</p>
										<div class="row">
											<template v-for="(priceAnime, indexPrice) in formdata.animation.prices" v-if="person.numPrice == priceAnime.idPrice && priceAnime.value > 0">
												<p>{{ pmb.getMessage("animation", "animation_registration_price") }} : {{ priceAnime.value }}</p>
											</template>
										</div>
										<div class="row">
											<customfields :customfields="person.personCustomsFields" customprefixe="price_type" :img="formdata.img" :pmb="pmb" :index="indexPerson"></customfields>
										</div>
									</template>
									<template v-else>
										<template v-for="(cp, idAnim) in person.animations">
											<hr />
											<div class="row" :id="'animation_' + idAnim + '_prices'">
												<label for="" class='etiquette'> {{ getAnimationName(idAnim) }} </label>
							 					<select v-model="cp.numPrice" @change="getcustomFieldAnimation(indexPerson, idAnim)">
													<option v-for="(price, indexPrice) in getAnimationPrices(idAnim)" :value="price.idPrice">
														{{ price.name }}
													</option>
												</select>
												<template v-for="(priceAnime, indexPrice) in getAnimationPrices(idAnim)" v-if="cp.numPrice == priceAnime.idPrice">
													{{ pmb.getMessage("animation", "animation_registration_price") }} : {{ priceAnime.value }}
												</template>
											</div>
											<div class="row" :id="'animation_' + idAnim + '_cp'">
												<customfields :customfields="cp.personCustomsFields" customprefixe="price_type" :img="formdata.img" :pmb="pmb" :index="indexPerson + '_' + idAnim"></customfields>
											</div>
										</template>
									</template>
								</td>
							</tr>
						</tbody>
					</template>
				</table>
			</div>
			<!-- Boutons -->
			<hr />
			<div class="row registration_form_registration_captcha">
				<div class="row registration_form_registration_captcha_container">
					<table>
						<tr>
							<td></td>
							<td>
								<div class="captcha_container" v-html="captchaTemplate"></div>
							</td>
						</tr>
						<tr>
							<td>{{ pmb.getMessage("animation", "animation_registration_verifcode") }} <sup>*</sup> :</td>
							<td>
								<input type='text' class='subsform' id='verifcode' name='captcha_code' v-model="captcha_code" value='' required/>
							</td>
						</tr>
					</table>
				</div>
				<div class="row registration_form_registration_user_confirmation">
					<input id="user_confirmation" name="user_confirmation" type="checkbox" v-model="user_confirmation" required/>
					<label for="user_confirmation">
						{{ pmb.getMessage("animation", "animation_registration_user_confirmation") }}
						<sup>*</sup>
					</label>
					<br />
					<br />
				</div>
				<div class="row" v-if="userWaitingList">
					<label>{{ pmb.getMessage("animation", "animation_registration_waiting_list_active") }}</label>
					<br />
					<br />
				</div>
			</div>
			<div class="row registration_form_registration_button">
				<div class="left">
					<button class="bouton" type="button" @click="cancel">
					    {{ pmb.getMessage('animation', 'animation_registration_cancel') }}
					</button>
					<button class="bouton" type="submit">
					    {{ pmb.getMessage('animation', 'animation_registration_save') }}
					</button>
				</div>
			</div>
			<input type="hidden" value="" name="data" v-model="sendData"/>
		</form>
	</div>
</template>

<script>
	import customfields from "../../../common/customFields/form/customFields.vue";
	import animations from "../../../animations/animations/components/animations.vue";

	export default {
		props : [
			"pmb",
			"formdata",
			"registration"
		],

		data: function() {
			return {
				sendData: "",
				captcha_code: "",
				captchaTemplate: $data.captchaTemplate,
				user_confirmation: false,
				contact: {
				    is_add: false,
				    index: null,
				},
				userWaitingList: false
			}
		},

		components : {
			customfields,
			animations,
		},

		created: function () {
			if (typeof this.registration.numAnimation == 'undefined') {
				alert(this.pmb.getMessage('animation', 'animation_registration_animation_check'));
				history.go(-1);
			}
		},

		mounted(){
			window.addEventListener('load', () => {
				new SecurimageAudio({
					audioElement: 'captcha_image_audio',
					controlsElement: 'captcha_image_audio_controls'
				});
			})
        },

		computed: {
			can_add_contact: function () {

			    if (this.contact_edited()) {
			    	return true;
			    }

			    if (this.contact.is_add) {
			    	return false;
			    }

				if (this.registration.barcode != "" && this.registration.nbRegisteredPersons != "" && this.registration.nbRegisteredPersons != 0) {
					for (let personIndex in this.registration.registrationListPerson) {
					    if (this.registration.registrationListPerson[personIndex].barcode == this.registration.barcode) {
					        this.contact.index = personIndex;
					        this.contact.is_add = true;
					    	return false;
					    }
					}
				}

		    	return true;
			},

			animationsSelected: function () {
				var animationsSelected = []
				for (const idAnimation of this.formdata.animationsSelected) {
					for (var i = 0; i < this.formdata.listDaughters.length; i++) {
						if (idAnimation == this.formdata.listDaughters[i].id) {
							animationsSelected.push(this.formdata.listDaughters[i]);
						}
					}
				}
				return animationsSelected;
			},

			animationsPricesTypesPerso: function () {
				var cp = {};
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

			contact_edited : function() {
			    if (this.contact.index != null) {
				    var contact = this.registration.registrationListPerson[this.contact.index];
				    if (contact) {
					    if (!contact.barcode && !this.registration.barcode) {
					        if (contact.name != this.registration.name) {
						        // L'utilisateur � modifier le nom
						        return true;
						    }
					    } else {
						    if (contact.barcode != this.registration.barcode) {
						    	// code barre diff�rents ce n'est plus la personne de contact
						        return true;
						    }
						    if (contact.barcode == this.registration.barcode && contact.name != this.registration.name) {
						        // L'utilisateur � modifier le nom (on fait rien)
						        return false;
						    }
					    }
				    }
			    }
		        return false;
			},

			cancel : function() {
				history.go(-1);
			},

			cloneObject : function (object) {
				var newObject = new Object();
				for (let index in object) {
					if (typeof object[index] != 'object') {
						newObject[index] = object[index];
					} else {
						if (Array.isArray(object[index])) {
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
				}
				return newObject;
			},

			addPerson: function () {
				// On verifie s'il reste des places disponibles
				// Si la liste d'attente est activ�e, on impose aucune limite

				var quotasRestant = this.checkQuotas();
				if (!this.formdata.animation.allowWaitingList) {
					if (!quotasRestant) {
						alert(this.pmb.getMessage('animation', 'animation_registration_max_registered'));
						return;
					}
				} else {
					this.userWaitingList = false;
					if (!quotasRestant) {
						this.userWaitingList = true;
					}
				}

				// On verifie s'il reste des places disponibles sur les animations selectionn�es
				if (this.formdata.animationsSelected && this.formdata.animationsSelected.length > 0){
					var quotas = this.getAnimationsSelectedQuotas();
					for (let quota of quotas) {
						if (quota.places == 0) {
							var msg = this.pmb.getMessage('animation', 'animation_selected_registration_max_registered');
							msg = msg.replace('%s', quota.animation);
							alert(msg);
							return;
						}
					}
				}

				this.registrationPushNewPerson({
					numRegistration : 0,
					numEmpr : 0,
					barcode : '',
					name : '',
				});
			},

			addContactToRegisteredPersons: function () {

				//on verifie s'il reste des places disponibles
				var quotasRestant = this.checkQuotas();
				if (!this.formdata.animation.allowWaitingList) {
					if (!quotasRestant) {
						alert(this.pmb.getMessage('animation', 'animation_registration_max_registered'));
						return;
					}
				} else {
					this.userWaitingList = false;
					if (!quotasRestant) {
						this.userWaitingList = true;
					}
				}

				// On verifie s'il reste des places disponibles sur les animations selectionn�es
				if (this.formdata.animationsSelected && this.formdata.animationsSelected.length > 0){
					var quotas = this.getAnimationsSelectedQuotas();
					for (let quota of quotas) {
						if (quota.places == 0) {
							var msg = this.pmb.getMessage('animation', 'animation_selected_registration_max_registered');
							msg = msg.replace('%s', quota.animation);
							alert(msg);
							return;
						}
					}
				}

				this.registrationPushNewPerson({
					numRegistration : 0,
					numEmpr : this.registration.numEmpr,
					barcode : this.registration.barcode,
					name : this.registration.name,
				});
				this.contact.is_add = true;
				this.contact.index = this.registration.registrationListPerson.length-1;
			},

			deletePerson : function(index) {

			    if (typeof this.contact == "object" && this.contact.is_add) {
				    if (this.contact.index == index || this.contact.index == 0) {
				        this.contact.index = null;
				        this.contact.is_add = false;
				    } else {
				        this.contact.index--;
				    }
			    }

			    var quotasRestant = this.checkQuotas();
				if (this.formdata.animation.allowWaitingList) {
					this.userWaitingList = false;
					if (!quotasRestant) {
						this.userWaitingList = true;
					}
				}

				this.registration.registrationListPerson.splice(index, 1);
				this.registration.nbRegisteredPersons--;
			},

			isValidPhone : function() {
				var tempPhone = this.registration.phoneNumber ? this.registration.phoneNumber.replace(/[\W\s]/gm, '') : "";
				if (isNaN(tempPhone)) {
					return false;
				}
				return true;
			},

			isValidContact : function() {

			    // On check le code barre
                if (this.formdata.params.animations_only_empr && this.registration.barcode == "") {
                    alert(this.pmb.getMessage('animation', 'animation_registration_error_contact_barcode'));
                    if (document.getElementById('barcode')) {
                        document.getElementById('barcode').focus()
                    }
                    return false;
                }

				if ('' === this.registration.name) {
					alert(this.pmb.getMessage('animation', 'animation_registration_error_contact_name'));
					if (document.getElementById('name')) {
					    document.getElementById('name').focus()
					}
					return false;
				}

				if ('' == this.registration.email || !is_valid_mail(this.registration.email)) {
					alert(this.pmb.getMessage('animation', 'animation_registration_error_contact_mail'));
					if (document.getElementById('email')) {
					    document.getElementById('email').focus()
					}
					return false;
				}

				if (this.registration.phoneNumber && !this.isValidPhone()) {
					alert(this.pmb.getMessage('animation', 'animation_registration_error_contact_phone'));
					if (document.getElementById('phoneNumber')) {
					    document.getElementById('phoneNumber').focus()
					}
					return false;
				}


				return true;
			},


			isCustomFieldEmpty : function(customField) {
				let flag = true;
				let value = customField.customValues[0].value;

				switch (true) {
					case (Array.isArray(value) && value.length != 0) :
					case (typeof value == 'string' && value == '') :
					case (typeof value == 'number') :
						flag = false;
						break;
				}

				return flag;
			},

			isValidPersons : function() {

				if (this.registration.nbRegisteredPersons != "" && this.registration.nbRegisteredPersons != 0) {
					for (let personIndex in this.registration.registrationListPerson) {

						var person = this.registration.registrationListPerson[personIndex];

						// On check le code barre
						if (this.formdata.params.animations_only_empr && person.barcode == "") {
							alert(this.pmb.getMessage('animation', 'animation_registration_error_barcode'));
							if (document.getElementById('person.barcode_'+personIndex)) {
								document.getElementById('person.barcode_'+personIndex).focus()
							}
							return false;
						}

						// On check le name
						if(person.name == ""){
							alert(this.pmb.getMessage('animation', 'animation_registration_error_person_name'));
							if (document.getElementById('person.name_'+personIndex)) {
								document.getElementById('person.name_'+personIndex).focus()
							}
							return false;
						}

						// On check les champs perso
					    if (this.animationsSelected && this.animationsSelected.length) {
							// inscription multiple, on v�rifie pour chaque animations
							for (var indexAnimation in person.animations) {
								for (var indexCp in person.animations[indexAnimation].personCustomsFields) {
								    let cpField = person.animations[indexAnimation].personCustomsFields[indexCp];
									if (cpField.customField.mandatory == '1' && cpField.customField.opacShow == '1') {
										if (!this.isCustomFieldEmpty(cpField)) {
											let msg = this.pmb.getMessage('animation', 'animation_registration_error_cp');
											msg = msg.replace('%s', cpField.customField.titre);
											alert(msg);
											return false;
										}
									}
								}
							}
						} else {
							// inscription simple
							for (let index in person.personCustomsFields) {
								if (person.personCustomsFields[index].customField.mandatory == '1' && person.personCustomsFields[index].customField.opacShow == '1') {
									if (!this.isCustomFieldEmpty(person.personCustomsFields[index])) {
										let msg = this.pmb.getMessage('animation', 'animation_registration_error_cp');
										msg = msg.replace('%s', person.personCustomsFields[index].customField.titre);
										alert(msg);
										return false;
									}
								}
							}
						}
					}

					return true;
				}

				alert(this.pmb.getMessage('animation', 'animation_registration_error_any_persons'));
				return false;
			},

			save: function (event) {
				event.preventDefault();

				if (this.isValidContact() && this.isValidPersons()) {

				  //on verifie s'il reste des places disponibles sur les animation selectionner
					if (this.formdata.animationsSelected && this.formdata.animationsSelected.length > 0){
						var quotas = this.getAnimationsSelectedQuotas();
						for (let quota of quotas) {
							if (quota.places <= 0) {
								var msg = this.pmb.getMessage('animation', 'animation_selected_registration_max_registered');
								msg = msg.replace('%s', quota.animation);
								alert(msg);
								return;
							}
						}
					}

					var dataContent = this.registration;
					dataContent.animationsSelected = this.formdata.animationsSelected;
					this.sendData = JSON.stringify(dataContent);

					// On v�rifie s'il y a eu un probl�me lors du JSON.stringify ?
					if (this.sendData == "" || typeof this.sendData == "undefined") {
						event.preventDefault();
						console.error(this.pmb.getMessage('animation', 'animation_registration_error_send'));
						return false;
					}

					if (!this.user_confirmation) {
						return false;
					}

					var data = new FormData();
					data.append('ajax_data', this.sendData);
					data.append('captcha_code', this.captcha_code);

					fetch("./ajax.php?module=animations&categ=registration&action=save", {
						method: 'POST',
						body: data
					}).then((response)=> {
						if (response.ok) {
							response.text().then((result)=> {
							    result = JSON.parse(result);
							    if (result.success) {
									document.location = './index.php?lvl=registration&action=view';
							    } else {
							        if (result.message && result.message != "") {
										alert(result.message);
							        } else {
										alert(this.pmb.getMessage('animation', 'animation_registration_error'));
							        }

							        // Si on a le captcha on l'actualise
							        var captcha_image = document.getElementById('captcha_image');
							        if (typeof window.captcha_image_audioObj !== 'undefined') {
							            captcha_image_audioObj.refresh();
							        }
							        if (captcha_image) {
							            captcha_image.src = './includes/securimage/securimage_show.php?' + Math.random();
							        }
							        if (document.getElementById('verifcode')) {
							            document.getElementById('verifcode').value = "";
										document.getElementById('verifcode').focus()
									}
							    }
						    });
						} else {
							console.error(this.pmb.getMessage('animation', 'animation_error_no_response'));
						}
					}).catch((error) => {
						console.error(this.pmb.getMessage('animation', 'animation_error_fetch') + error.message);
					});
				}
			},

			checkQuotas : function(){
				//Si on a pas limit� les inscriptions, on continue
				if (this.formdata.animation.allQuotas.animationQuotas.global == 0) {
					return true;
				}

				var quotasRestant = 0;

				if (this.formdata.animation.allQuotas.availableQuotas.global && this.formdata.animation.allQuotas.availableQuotas.internet == 0){
					quotasRestant = this.formdata.animation.allQuotas.availableQuotas.global - this.registration.registrationListPerson.length;
				}

				if (this.formdata.animation.allQuotas.availableQuotas.internet > 0){
					quotasRestant = this.formdata.animation.allQuotas.availableQuotas.internet - this.registration.registrationListPerson.length;
				}

				return quotasRestant;
			},

			getcustomField : function(indexPerson) {
				for (let price of this.formdata.animation.prices) {
					if (price.idPrice == this.registration.registrationListPerson[indexPerson].numPrice) {
						this.registration.registrationListPerson[indexPerson].personCustomsFields = this.cloneObject(price.priceType.customFields);
					}
				}
			},

			getcustomFieldAnimation : function(indexPerson, idAnimation) {
			    if (this.formdata.animationsSelected && this.formdata.animationsSelected.length) {
					var person = this.registration.registrationListPerson[indexPerson];
					if (this.animationsPricesTypesPerso[idAnimation]) {
						var prices = this.animationsPricesTypesPerso[idAnimation];
						if (prices[person.animations[idAnimation].numPrice]) {
							person.animations[idAnimation].personCustomsFields = this.cloneObject(prices[person.animations[idAnimation].numPrice])
						}
					}
				}
			},

			barcodeNotAlreadyUse : function(indexPerson) {
			    if (this.registration.nbRegisteredPersons != "" && this.registration.nbRegisteredPersons != 0 && this.registration.registrationListPerson[indexPerson].barcode != "") {
					for (let index in this.registration.registrationListPerson) {
					    if (index == indexPerson) {
					        continue;
					    }

					    if (this.registration.registrationListPerson[indexPerson].barcode == this.registration.registrationListPerson[index].barcode) {
					        this.registration.registrationListPerson[indexPerson].barcode = "";
							alert(this.pmb.getMessage('animation', 'animation_registration_barcode_already_use'));
					    }
					}
				}
			},

			changeAnimationsSelected : function(animationsSelected) {
			    if (animationsSelected) {

			        for (var i = 0; i < this.registration.registrationListPerson.length; i++) {

			            var backup = this.cloneObject(this.registration.registrationListPerson[i]);
						var animations = {};

						for (const idAnim of animationsSelected) {
							for (const idAnimBackup in backup.animations) {
							    if (idAnimBackup == idAnim) {
							    	animations[idAnim] = backup.animations[idAnim];
							    }
							}

						    if (!animations[idAnim]) {
						        var prices = this.getAnimationPrices(idAnim);
						        if (prices) {
								    animations[idAnim] = {
										numAnimation: idAnim,
										numPrice: prices[0].idPrice,
										personCustomsFields: this.cloneObject(prices[0].priceType.customFields)
									};
						        }
						    }
						}

						this.registration.registrationListPerson.splice(i, 1, {
							numRegistration : 0,
							animations: animations,
							numEmpr : backup.numEmpr,
							barcode : backup.barcode,
							name : backup.name,
						});
					}

				    this.formdata.animationsSelected = animationsSelected;
			    }
			},

			registrationPushNewPerson : function (newPerson) {
				if (this.formdata.animationsSelected && this.formdata.animationsSelected.length) {
					newPerson.animations = {};
					for (var i = 0; i < this.animationsSelected.length; i++) {
						newPerson.animations[this.animationsSelected[i].id] = {
							numAnimation: this.animationsSelected[i].id,
							numPrice: this.animationsSelected[i].prices[0].idPrice,
							personCustomsFields: this.cloneObject(this.animationsSelected[i].prices[0].priceType.customFields)
						};
					}
					this.registration.registrationListPerson.push(newPerson);
				} else {
					newPerson.personCustomsFields = this.cloneObject(this.formdata.animation.prices[0].priceType.customFields);
					newPerson.numPrice = this.formdata.animation.prices[0].idPrice;
					this.registration.registrationListPerson.push(newPerson);
				}
				this.registration.nbRegisteredPersons++;
			},

			getAnimationPrices: function (idAnimation) {
			    for (const animation of this.formdata.listDaughters) {
			        if (idAnimation == animation.id) {
			            return animation.prices;
			        }
			    }
			},

			getAnimationName: function (idAnimation) {
			    for (const animation of this.formdata.listDaughters) {
			        if (idAnimation == animation.id) {
			            return animation.name;
			        }
			    }
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
									quotasRestant = this.formdata.listDaughters[i].allQuotas.availableQuotas.global - this.registration.registrationListPerson.length;
								}
								if (this.formdata.listDaughters[i].allQuotas.availableQuotas.internet > 0){
									quotasRestant = this.formdata.listDaughters[i].allQuotas.availableQuotas.internet - this.registration.registrationListPerson.length;
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