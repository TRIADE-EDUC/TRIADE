<template>
	<div id="animationView">
		<h3 class="section-sub-title">
			<!-- Boutons collapse/expand -->
			{{ animation.name }} ({{ animation.event.startDate }}
			<template v-if="!animation.event.duringDay"> {{ pmb.getMessage("animation", "animation_au") }} {{ animation.event.endDate }})</template>
			<template v-else>)</template>
		</h3>
		<table>
			<tr>
				<td>
						<!-- EVENEMENT -->
						<div class="row">
							<b>{{ pmb.getMessage("animation", "animation_date") }} :</b>
							{{ animation.event.startDate }} 
							<template v-if="'00:00' !== animation.event.startHour">{{ animation.event.startHour}}</template>
							<template v-if="!animation.event.duringDay">
								{{ pmb.getMessage("animation", "animation_au") }} {{ animation.event.endDate }} 
							<template v-if="'00:00' !== animation.event.endHour">{{ animation.event.endHour}}</template>
							</template>
						</div>
						
						<!-- QUOTA -->
						<div class="row" v-if="animation.globalQuota >= 0 && !this.animation.hasChildrens || animation.internetQuota >=0 && !this.animation.hasChildrens">
							<div class="parent">
								<b>{{ pmb.getMessage("animation", "list_animation_quota") }} :</b>
									<span v-if="typeof animation.allQuotas.availableQuotas.global !='undefined' && animation.globalQuota >= 0">
									{{ pmb.getMessage("animation", "list_animation_quota_available") }} : 
										<template v-if="animation.allQuotas.animationQuotas.global || animation.allQuotas.animationQuotas.global  ">
										{{ animation.allQuotas.availableQuotas.global }} / {{ animation.globalQuota }} | 
									</template>
									<template v-else>
											{{ pmb.getMessage("animation", "form_search_illimited_quotas") }} | 
									</template>
								</span>
								<span v-if="typeof animation.allQuotas.availableQuotas.internet !='undefined' && animation.internetQuota >= 0">
									<template v-if="animation.allQuotas.animationQuotas.internet != 0 ">
										{{ pmb.getMessage("animation", "list_animation_quota_available_on_internet") }} : 
										{{ animation.allQuotas.availableQuotas.internet }} / {{ animation.internetQuota }}
									</template>
									<template v-else>
											{{ pmb.getMessage("animation", "form_search_illimited_quotas") }} 
									</template>
								</span>
							</div>
							<div v-if="animation.allowWaitingList && typeof animation.allQuotas.waitingList.global != 'undefined'">
								<b>{{ pmb.getMessage("animation", "animation_waiting_list") }} :</b>
								{{ animation.allQuotas.waitingList.global }}
							</div>
						</div>
						
						<!-- STATUT -->
						<div v-if="animation.status.label" class="row">
							<b>{{ pmb.getMessage("animation", "view_animation_status") }} :</b>
							{{ animation.status.label }}
						</div>
	
						<!-- TYPE -->
						<div v-if="animation.status.label" class="row">
							<b>{{ pmb.getMessage("animation", "view_animation_types") }} :</b>
							{{ animation.type.label }}
						</div>
	
						<!-- CALENDAR -->
						<div v-if="animation.calendar.name" class="row">
							<b>{{ pmb.getMessage("animation", "view_animation_calendar") }} :</b>
							{{ animation.calendar.name }}
						</div>
						
						<!-- ANIMATION PARENT -->
						<div class="row" v-if="animation.parent.name">
							<b>{{ pmb.getMessage("animation", "view_animation_parentAnimation") }} :</b>
							<a :href="'animations.php?categ=animations&action=view&id='+animation.parent.id">{{ animation.parent.name }}</a>
						</div>
						
						<!-- CONCEPTS -->
						<div class="row" v-if="animation.concepts.length">
							<div class="parent">
								<b>{{ pmb.getMessage("animation", "update_add_animation_concepts") }} : </b>
									<span v-for="(concept,index) in animation.concepts">
										<a :href="'./autorites.php?categ=see&sub=concept&id=' + concept.id">{{ concept.displayLabel }}</a>
										<template v-if="animation.concepts.length > 1 && index < animation.concepts.length - 1"> / </template>
									</span>
							</div>
						</div>
						
						<!-- CATEGORIES -->
						<div class="row" v-if="animation.categories.length">
							<div class="parent">
								<b>{{ pmb.getMessage("animation", "update_add_animation_categories") }} :</b>
								<span v-for="(categ,index) in animation.categories">
									<a :href="'./autorites.php?categ=see&sub=category&id=' + categ.id">{{ categ.displayLabel }}</a>
									<template v-if="animation.categories.length > 1 && index < animation.categories.length - 1"> / </template>
								</span>
							</div>
						</div>
	
						<!-- LOCALISATIONS -->
						<div class="row" v-if="animation.location.length">
							<div class="parent">
								<b>{{ pmb.getMessage("animation", "animation_locations") }} :</b>
								<span v-for="(loc, index) in animation.location">
									{{ loc.locationLibelle }}
									<template v-if="animation.location.length > 1 && index < animation.location.length - 1"> / </template>
								</span>
							</div>
						</div>
						
						<!-- TARIFS -->
						<div class="row" v-if="animation.prices.length">
							<div class="parent">	
								<b>{{ pmb.getMessage("animation", "list_animation_list_price") }} :</b>
								<span v-for="(price, index) in animation.prices">
									{{ price.name }} : {{ price.value }} {{ formdata.globals.pmbDevise }}
									<template v-if="animation.prices.length > 1 && index < animation.prices.length - 1"> / </template>
								</span>
							</div>
						</div>
						
						<!-- NOTES -->
						<div class="row" v-if="animation.description || animation.comment" >
							<div class="row">
								<div v-if="animation.comment" class="row">
									<b>{{ pmb.getMessage("animation", "animation_comment") }} :</b>
									<div v-html="animation.comment"></div>
								</div>
								<div v-if="animation.description" class="row">
									<b>{{ pmb.getMessage("animation", "animation_description") }} :</b>
									<div v-html="animation.description"></div>
								</div>
							</div>
						</div>
						
						<!-- CHAMPS PERSOS -->
						<div class="row" v-if="animation.customFields.length && animation.gotCustomFieldsValues">
							<div v-for="(field, index) in animation.customFields" class="row">
					            <template v-if="field.customField.values.length">
				                	<b>{{ field.customField.titre }} : </b>
									<customfields :field="field.customField" :values="field.customValues" customprefixe="animation" :img="img" :pmb="pmb"></customfields>
					            </template>
							</div>
						</div>
	
						<!-- Logo -->
						<div class="row" v-if="animation.logo">
							<div class="row">
								<b>{{ pmb.getMessage("animation", "animation_logo") }} :</b>
								<span>
									{{ animation.logo.filename }}
								</span>
							</div>
						</div>
						
						<!-- INFORMATION PLUGINS EDITORIAL -->
						<template v-if="plugin.info_editorial">
							<div class="row" v-html="plugin.info_editorial">
							</div>
						</template>
					</td>
					<td>
						<!-- BOUTONS -->
						<div class="row">
							<div class="right" v-if="animation.logo">
								<img :src="formdata.urlBase + '/animations_vign.php?animationId=' + animation.idAnimation" :alt="animation.logo.alt">
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="row">
							<div class="left">
								<div class="row">
									<input @click="edit" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_modif')"/>
									<template v-if="animation.allQuotas.animationQuotas.global == 0 || animation.allQuotas.availableQuotas.global > 0 || animation.allowWaitingList == 1">
										<input @click="addRegistration" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_add_registration')"/>
									</template>
									<template v-else>
										<input @click="addRegistration" disabled="" class="bouton disabled" type="button" :value="pmb.getMessage('animation', 'animation_add_registration')"/>
									</template>
									
									<input @click="duplicatRegistration" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_duplicate')"/>
									<template v-if="plugin.inputs">
										<!-- BOUTONS PLUGINS -->
										<div class="display-inline" v-html="plugin.inputs"></div>
									</template>
								</div>
								<div class="row">
									<input @click="printRegistrationList" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_print_registration_list')"/>
									<input @click="exportPrint('excel')"  class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_export_excel')" />
								</div>
							</div>
						</div>
					</td>
					<td>
						<div class="right">
							<input @click="mailing" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_mailing')"/>
							<input @click="delAnim" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_delete')"/>
						</div>
					</td>
				</tr>
		</table>
	</div>
</template>



<script>
	import customfields from "../../../common/customFields/view/customFields.vue";
	
	export default {
		props : ["animation", "pmb", "img", "formdata", "registrationlist"],
		components : {
			customfields
		},
		data: function () {
			return {
				hover : -1,
				plugin : $data.plugin,
			}
		},
		created : function() {
			if(this.animation.logo && "" != this.animation.logo){
				this.animation.logo = JSON.parse(this.animation.logo);
			}
		},
		methods: {
			isEmpty : function(value) {
				if (null == value && undefined == value){
					return true;
				}
				return false;
			},
			delAnim : function() {
				var resultat = window.confirm(this.pmb.getMessage('animation', 'animation_confirm_del_animation'));
				if (resultat == 0) {
					event.preventDefault();
				} else {
					
					var delChildrens = false;
					if (this.animation.hasChildrens) {
						var resultat = window.confirm(this.pmb.getMessage('animation', 'animation_confirm_del_animation_child'));
						if (resultat == 0) {
							event.preventDefault();
							return;							
						} else {
							delChildrens = true;
						}
					}
					
					var data = new FormData();
					data.append('data', JSON.stringify({
						id:this.animation.idAnimation,
						delChildrens: delChildrens
					}));
					
					let url = "./ajax.php?module=animations&categ=animations&action=delete";
					fetch(url,{
						method: 'POST',
						body: data
					}).then(function(response) {
						if (response.ok) {
							document.location = './animations.php?categ=animations&action=list';
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					})
					.catch(function(error) {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
				}
			},
			
			edit : function() {
				document.location = './animations.php?categ=animations&action=edit&id=' + this.animation.id;
			},
			
			addRegistration : function() {
				document.location = './animations.php?categ=registration&action=add&numAnimation=' + this.animation.id;
			},
			
			printRegistrationList : function() {
				window.open('./pdf.php?pdfdoc=animations&action=printRegistrationList&id='+ this.animation.id);
			},
			
			duplicatRegistration : function(){
				document.location = './animations.php?categ=animations&action=duplicate&id=' + this.animation.id;
			},
			
			mailing : function() {
				//on teste s'il y a des inscrits
				if (this.registrationlist.length){
					document.location = './animations.php?categ=animations&action=mailing&id=' + this.animation.id;
				} else {
					alert(this.pmb.getMessage('animation', 'animation_mailing_no_registred_persons'));
				}				
			},

			getLogo : function() {
				var data = new FormData();
				data.append('data', JSON.stringify({
					id:this.animation.idAnimation,
				}));
				
				let url = "./ajax.php?module=animations&categ=animations&action=logo";
				fetch(url,{
					method: 'POST',
					body: data
				}).then(function(response) {
					if (response.ok) {
						return response;
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				})
				.catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			
			exportPrint: function(exportType) {
				let url = "./ajax.php?module=animations&categ=animations&action=export&data="+JSON.stringify({id:this.animation.idAnimation,exportType: exportType});
				window.open(url, "_blank");
			}
		},
	}
</script>
