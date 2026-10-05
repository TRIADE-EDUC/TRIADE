<template>
	<div id="list">
		<h3 v-if="!numanimation">
			{{ pmb.getMessage("animation", "list_registration_title") }}
		</h3>
		<div class="row">
			<span>
				{{ pmb.getMessage("animation", "animation_search_participant_reservation") }}
			</span>
			<input id="search" v-model="search" type="text" />
		</div>
		<div class="row" v-if="!numanimation">
			<div id="filtersChild">
				<div class="row">
					<div class="colonne2">
						<div class="row">
							<label for="search_status">
								{{ pmb.getMessage("animation", "animations") }}
							</label>
						</div>
						<select v-model="animSelected">
							<option value="0" selected>
								{{ pmb.getMessage("animation", "animation_all_animations") }}
							</option>
							<option v-for="(anim, index) in animationlist" :value="anim.id">
								{{ anim.name }}
							</option>
						</select>
					</div>
					<div class="colonne_suite">
						<div class="row">
							<label for="search_localisation">
								{{ pmb.getMessage("animation", "form_search_status") }}
							</label>
						</div>
						<select v-model="statusSelected">
							<option value="0" selected>
								{{ pmb.getMessage("animation", "animation_all_registration_status") }}
							</option>
							<option v-for="(status, index) in statuslist" :value="status.id_registration_status">
								{{ status.name }}
							</option>
						</select>
					</div>
				</div>
				<div class="row"></div>
				<div class="row">
					<div class="colonne2">
						<div class="row">
							<label for="search_types">
								{{ pmb.getMessage("animation", "form_search_location") }}
							</label>
						</div>
						<select v-model="locSelected">
							<option value="0" selected>Toutes les localisation</option>
							<option v-for="(loc, index) in localisationlist" :value="loc.idlocation">{{ loc.location_libelle
							}}</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<table>
			<tr>
				<th>
					<div class="center">
						<i class="fa fa-plus-square" style='cursor: pointer' @click="checkAll"></i> &nbsp; <i
							class="fa fa-minus-square" style='cursor: pointer' @click="uncheckAll"></i>
					</div>
				</th>
				<th style="cursor: pointer;" @click="setSort('name')">
					{{ pmb.getMessage("animation", "list_registration_name") }}
					<i :class="getClassSort('name')"></i>
				</th>
				<th style="cursor: pointer;" @click="setSort('email')">
					{{ pmb.getMessage("animation", "list_registration_email") }}
					<i :class="getClassSort('email')"></i>
				</th>
				<th>
					{{ pmb.getMessage("animation", "list_registration_phone") }}
				</th>
				<th style="cursor: pointer;" @click="setSort('nbRegisteredPersons')">
					{{ pmb.getMessage("animation", "registration_nb_persons") }}
					<i :class="getClassSort('nbRegisteredPersons')"></i>
				</th>
				<th v-if="!numanimation">
					{{ pmb.getMessage("animation", "animations") }}
				</th>
				<th>{{ pmb.getMessage("animation", "animation_locations") }}</th>
				<th style="cursor: pointer;" @click="setSort('rawDate')">
					{{ pmb.getMessage("animation", "incription_date") }}
					<i :class="getClassSort('rawDate')"></i>
				</th>
				<th>{{ pmb.getMessage("animation", "list_animation_status") }}</th>
				<th v-if="!numanimation"></th>
			</tr>
			<template v-if="filteredRegistration.length > 0">
				<tr v-for="(registration, index) in filteredRegistration" :key="index"
					style='cursor: pointer' @mouseover="hover = index" @mouseout="hover = -1"
					@click="view(registration.idRegistration)"
					:class="[index % 2 == 0 ? 'odd' : 'even', index == hover ? 'surbrillance' : '']">
					<td @click.stop="" style="cursor: default">
						<div class="center">
							<input type="checkbox" v-model="registrationsSelected" :value="registration.id" />
						</div>
					</td>
					<td>{{ registration.name }}</td>
					<td>{{ registration.email }}</td>
					<td>{{ registration.phoneNumber }}</td>
					<td>{{ registration.nbRegisteredPersons }}</td>
					<td v-if="!numanimation">{{ registration.animation.name }}</td>
					<td>
						<template v-if="registration.animation.location.length">
							<span v-for="(loc, index) in registration.animation.location">
								{{ loc.locationLibelle }} <br
									v-if="!(index == registration.animation.location.length - 1)">
							</span>
						</template>
						<template v-else>
							{{ pmb.getMessage("animation", "form_search_no_location") }}
						</template>
					</td>
					<td>
						<template v-if="registration.date">
							{{ registration.date }}
						</template>
					</td>
					<td>
						<template v-if="registration.registrationStatus.name">
							{{ registration.registrationStatus.name }}
						</template>
					</td>
					<!-- A voir si on le garde ou pas car un bouton a ete creer pour faire une validation en lot  -->
					<!--
					<td v-if="numanimation" class="center" :title="pmb.getMessage('animation', 'anim_validate_registration')" >
						<button v-if="!registration.validated" :disabled="!checkValidation(index)" @click.prevent="validateRegistration(registration.id, index)"><i class="fa fa-check"></i></button>
					</td>
					-->
				</tr>
			</template>
			<template v-else>
				<tr>
					<td colspan="9" style='text-align: center'>
						{{ pmb.getMessage("animation", "list_registration_empty") }}
					</td>
				</tr>
			</template>
		</table>
		<div class="row">
			<div class="left">
				<input @click="validatedRegistrationList" class="bouton" type="button"
					:value="pmb.getMessage('animation', 'incription_validated_list')" />
			</div>
			<div class="right">
				<input @click="delRegistration" class="bouton" type="button"
					:value="pmb.getMessage('animation', 'incription_delete_list')" />
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: ["registrationlist", 'animationlist', "pmb", "numanimation", "statuslist", "selectedstatus", "localisationlist"],
	data: function () {
		return {
			animSelected: 0,
			statusSelected: 0,
			locSelected: 0,
			search: '',
			hover: -1,
			registrationsSelected: [],
			filter: {
				name: '',
				sort: '',
				status: ['0'],
				locations: ['0']
			},
			map: {
				'': ' |-|_|\'|"|\\.|:|;',
				'a': 'á|à|ã|â',
				'e': 'é|è|ê',
				'i': 'í|ì|î',
				'o': 'ó|ò|ô|õ',
				'u': 'ú|ù|û|ü',
				'c': 'ç',
				'n': 'ñ',
			}
		}
	},
	created: function () {
		if (this.selectedstatus) {
			this.statusSelected = this.selectedstatus
		}

		// On tri par défaut sur la date décroissante
		this.filter.sort = "asc";
		this.setSort("rawDate");
	},
	computed: {
		filteredRegistration: function () {

			let d = this.registrationlist;
			let s = this.formatText(this.search);

			let filteredTab = d.filter((registration) => {

				//On test sur le nom du participant
				let regist = this.formatText(registration.name)
				if (-1 != regist.indexOf(s)) {
					return true;
				}

				//On test sur le email du participant
				regist = this.formatText(registration.email);
				if (-1 != regist.indexOf(s)) {
					return true;
				}

				//On test sur le phoneNumber du participant
				regist = this.formatText(registration.phoneNumber);
				return (-1 != regist.indexOf(s));
			}, s);

			// On test les localisation
			filteredTab = filteredTab.filter((registration) => {
				if (0 == this.locSelected) {
					return true;
				}

				for (let i = 0; i < registration.animation.location.length; i++) {
					if (registration.animation.location[i].id == this.locSelected) {
						return true;
					}
				}
			});

			// On test les animations et les status
			filteredTab = filteredTab.filter((registration) => {
				if (
					(registration.animation.id == this.animSelected || this.animSelected == 0) &&
					(registration.numRegistrationStatus == this.statusSelected || this.statusSelected == 0)
				) {
					return true;
				}
			});

			return filteredTab;
		},
	},
	methods: {
		formatText: function (string) {
			let s = string.toLowerCase();
			for (let pattern in this.map) {
				s = s.replace(new RegExp(this.map[pattern], 'g'), pattern);
			}
			return s;
		},

		view: function (id) {
			document.location = './animations.php?categ=registration&action=edit&id=' + id;
		},

		delRegistration: function () {
			if (!this.registrationsSelected.length) {
				alert(this.pmb.getMessage('animation', 'animation_confirm_del_no_registration'));
				return;
			}

			let msg = this.pmb.getMessage('animation', 'animation_confirm_del_registration');
			if (this.registrationsSelected.length >= 1) {
				msg = this.pmb.getMessage('animation', 'animation_confirm_del_multiple_registration');
			}

			if (window.confirm(msg)) {
				let url = "./ajax.php?module=animations&categ=registration&action=delete";
				let data = new FormData();
				let ids = this.registrationsSelected.join(',');
				data.append('data', JSON.stringify({ id: ids }));

				fetch(url, {
					method: 'POST',
					body: data
				}).then((response) => {
					if (response.ok) {
						if (this.numanimation) {
							document.location = './animations.php?categ=animations&action=view&id=' + this.numanimation;
						} else {
							for (let i = 0; i < this.registrationsSelected.length; i++) {
								for (let j = 0; j < this.registrationlist.length; j++) {
									if (this.registrationlist[j].id == this.registrationsSelected[i]) {
										this.registrationlist.splice(j, 1);
									}
								}
							}
						}

						// On vide la liste des inscriptions selectionnees
						this.uncheckAll();
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				})
				.catch((error) => {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			}
		},
		validatedRegistrationList: function () {
			if (!this.registrationsSelected.length) {
				alert(this.pmb.getMessage('animation', 'animation_confirm_list_no_registration'));
				return;
			}

			let msg = this.pmb.getMessage('animation', 'animation_confirm_list_no_registration');
			if (this.registrationsSelected.length >= 1) {
				msg = this.pmb.getMessage('animation', 'animation_confirm_validated_multiple_registration');
			}

			// on verifie si les quotas sont suffisants
			for(let idRegistration of this.registrationsSelected){
				let registration = this.registrationlist.find(reg => reg.id == idRegistration);
				let animation = this.animationlist.find(anim => anim.id == registration.numAnimation);

				if(registration == undefined || animation == undefined){
					continue;
				}

				let quotasGlobal = animation.allQuotas.availableQuotas.global;
				if (quotasGlobal == 0) {
					alert(this.pmb.getMessage('animation', 'animation_confirm_list_no_quota'));
				}
			}

			if (window.confirm(msg)) {
				let url = "./ajax.php?module=animations&categ=registration&action=validateListRegistration";
				let data = new FormData();
				let ids = this.registrationsSelected.join(',');
				data.append('data', JSON.stringify({ id: ids }));

				fetch(url, {
					method: 'POST',
					body: data
				}).then((response) => {
					if (response.ok) {
						if (this.numanimation) {
							document.location = './animations.php?categ=animations&action=view&id=' + this.numanimation;
							return;
						}
						document.location = './animations.php?categ=registration&action=list';
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				})
				.catch((error) => {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			}
		},
		validateRegistration: function (id, index) {
			let resultat = window.confirm(this.pmb.getMessage('animation', 'anim_confirm_validate_registration'));

			if (resultat == 0) {
				event.preventDefault();
			} else {
				let url = "./ajax.php?module=animations&categ=registration&action=validate";
				let data = new FormData();
				data.append('data', JSON.stringify({ id: id }));

				fetch(url, {
					method: 'POST',
					body: data
				}).then((response) => {
					if (response.ok) {
						document.location = './animations.php?categ=animations&action=view&id=' + this.registrationlist[index].numAnimation;
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				})
				.catch((error) => {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			}
		},

		checkAll: function () {
			for (let i = 0; i < this.filteredRegistration.length; i++) {
				if (!this.registrationsSelected.includes(this.filteredRegistration[i].id)) {
					this.registrationsSelected.push(this.filteredRegistration[i].id);
				}
			}
		},

		uncheckAll: function () {
			this.registrationsSelected = [];
		},

		checkValidation: function (index) {
			const registration = this.registrationlist[index];
			return (!registration.validated && registration.animation.allQuotas.availableQuotas.global >= registration.nbRegisteredPersons)
		},
		setSort: function (sortName) {
			this.filter.name = sortName;

			if (this.filter.sort == "asc") {
				this.filter.sort = "desc";
			} else {
				this.filter.sort = "asc";
			}

			let sortTab = sortName.split('.');

			// TODO : Arriver a gerer le filter pour aller chercher dans des sous objets
			let sort = this.filter.sort;
			this.registrationlist.sort(function (a, b) {
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
				if (a == b) {
					return 0;
				}
				if (sort == "asc") {
					return (a < b) ? - 1 : 1;
				} else {
					return (a > b) ? - 1 : 1;
				}
			})
		},
		getClassSort: function (name) {
			let className = "fa fa-sort";

			if (this.filter.name == name) {
				if (this.filter.sort == "asc") {
					className += "-asc";
				} else {
					className += "-desc";
				}
			}
			return className;
		},
	}
}
</script>
