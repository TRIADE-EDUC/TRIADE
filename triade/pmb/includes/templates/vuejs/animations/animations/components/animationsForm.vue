<template>
	<div class="container">
		<form class="form-admin" id="formAdmin">
			<h3 v-if="animation.id">{{ pmb.getMessage("animation", "update_animation_title") }}</h3>
			<h3 v-else>{{ pmb.getMessage("animation", "add_animation_title") }}</h3>
			<div class="form-contenu">
			
				<!-- TYPE -->
				<select id="types" name="type" v-model="animation.numType">
					<option v-for="type in formdata.types" :value="type.id_type">{{ type.label }}</option>
				</select>
			
				<!-- Boutons collapse/expand -->
				<div class="row">
					<a href="#" onClick="expandAll(); return false;">
						<img id="expandAll" :src="formdata.img.expandAll" border="0"/>
					</a>
					<a href="#" onClick="collapseAll(); return false;">
						<img id="collapseAll" :src="formdata.img.collapseAll" border="0"/>
					</a>
				</div>
				
				<!-- Informations générales -->
				<div id="el0Parent" class="parent">
					<h3>
						<img id="el0Img" class="img_plus" name="imEx" :src='formdata.img.minus' onClick="expandBase('el0', true); return false;">
						{{ pmb.getMessage("animation", "update_add_animation_general_informations") }}
					</h3>
				</div>
				<div id="el0Child" class="child" style="display: block;">
					<div id="el0Child_0">
						<div id="el0Child_0a" class="row uk-clearfix">
							<label class='etiquette' :title="pmb.getMessage('animation', 'is_required')">{{ pmb.getMessage('animation', 'update_add_animation_name') }} <sup>*</sup></label>
						</div>
						<div id="el0Child_0b" class="row uk-clearfix">
							<input id="animation.name" type="text" class='saisie-40em' v-model="animation.name">
						</div>
					</div>
				</div>
			
				<!-- Événement -->
				<div id="el1Parent" class="parent">
					<h3>
						<img id="el1Img" class="img_plus" name="imEx" :src='formdata.img.minus' onClick="expandBase('el1', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_event') }}
					</h3>
				</div>
				<div id="el1Child" class="child" style="display: block;">
					<div id="el1Child_0">
						<input id="noEndDate" type="checkbox" v-model="animation.event.duringDay"/>
						<label for="noEndDate" class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_during_day") }}</label>
					</div>
					<div id="el1Child_1">
						<div id="el1Child_1a" class="row uk-clearfix">
							<label class='etiquette' :title="pmb.getMessage('animation', 'is_required')">{{ pmb.getMessage('animation', 'update_add_animation_startDate') }} <sup>*</sup></label>
						</div>
						<div id="el1Child_1b" class="row uk-clearfix">
							<input v-model="animation.event.startDate" type="date" @focus="changeEndDate()" @change="changeEndDate()"/>
							<input v-model="animation.event.startHour" type="time"/>
						</div>
					</div>
					<div id="el1Child_2" v-if="!animation.event.duringDay">
						<div id="el1Child_2a" class="row uk-clearfix">
							<label class='etiquette' :title="pmb.getMessage('animation', 'is_required')">{{ pmb.getMessage("animation", "update_add_animation_endDate") }} <sup>*</sup></label>
						</div>
						<div id="el1Child_2b" class="row uk-clearfix">
							<input v-model="animation.event.endDate" type="date" :min="animation.event.startDate" @change="changeDateInterval()"/>
							<input v-model="animation.event.endHour" type="time"/>
						</div>
					</div>
				</div>
				
				<!-- Types de prix -->
				<div id="el2Parent" class="parent">
					<h3>
						<img id="el2Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el2', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_price_types') }}
					</h3>
				</div>
				<div id="el2Child" class="child" style="display: none;">
					<div id="el2Child_0">
						<div id="el2Child_0a">
							<label class='etiquette' :title="pmb.getMessage('animation', 'is_required')">{{ pmb.getMessage("animation", "update_add_animation_addPrice") }} <sup>*</sup></label>
						</div>
			       		<div id="el2Child_0b">
							<template v-for="(price,index) in animation.prices" id="animation.prices">
								<div class="row" :key='index'>
									<label class='etiquette'> {{ pmb.getMessage("animation", "animation_type") }} </label>
				 					<select :id="'priceType_' + index" v-model="price.numPriceType" @change="changeValuePrice(index, price)">
										<option v-for="type in formdata.priceType" :value="type.idPriceType" :selected="price.numPriceType == type.idPriceType">
											{{type.name}}
										</option>
									</select>
									<label class='etiquette'> {{ pmb.getMessage("animation", "view_animation_name") }} </label>
									<input :id="'priceName_' + index" v-model="price.name" type="text" class='saisie-20em'>
									<label class='etiquette'> {{ pmb.getMessage("animation", "admin_type_price") }} ({{ formdata.globals.pmbDevise }})</label>
									<input :id="'priceValue_' + index" v-model.number="price.value" type="number" class='saisie-20em' step="0.05" min="0">
									<input v-if="index>0" @click="deleteMultipleField('prices', index)" :title="pmb.getMessage('animation', 'update_add_animation_deletePrice')" class="bouton" type="button" value="X"/>
									<input v-if="index == animation.prices.length - 1" @click="addPrice" :title="pmb.getMessage('animation', 'update_add_animation_addPrice')" class="bouton" type="button" value="+"/>
								</div>
							</template>
			       		</div>
					</div>
				</div>
				
				<!-- Animation parente -->
				<div id="el3Parent" class="parent">
					<h3>
						<img id="el3Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el3', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_linked') }}
					</h3>
				</div>
				<div id="el3Child" class="child" style="display: none;">
					<div id="el3Child_0">
						<div id="el3Child_0a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_parentAnimation") }}</label>
						</div>
						<div id="el3Child_0b">
							<input v-if="animation.parent.name" id="parentAnimation" v-model="animation.parent.name" class="saisie-30emr" type="text" completion="animations" autfield="parentAnimationId" autocomplete="off" :autexclude="animation.id"/>
							<input v-else id="parentAnimation" class="saisie-30emr" type="text" completion="animations" autfield="parentAnimationId" autocomplete="off" :autexclude="animation.id"/>
							<input id="parentAnimationId" class="hide" type="text" v-model="animation.numParent" @change="animation.numParent = parseInt($event.target.value, 10)" />
							<input @click="deleteParentAnimation()" :title="pmb.getMessage('animation', 'update_add_animation_parentAnimation')" class="bouton" type="button" value="X"/>
						</div>
					</div>
				</div>

				<!-- Statut -->
				<div id="el4Parent" class="parent">
					<h3>
						<img id="el4Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el4', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_status') }}
					</h3>
				</div>
				<div id="el4Child" class="child" style="display: none;">
					<div id="el4Child_0">
						<div id="el4Child_0a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_status") }}</label>
						</div>
						<div id="el4Child_0b">
							<select id="numStatus" v-model="animation.numStatus">
								<option v-for="stat in formdata.status" :value="stat.id_status">{{stat.label}}</option>
							</select>
						</div>
					</div>
				</div>
				
				<!-- Localisation -->
				<div id="el5Parent" class="parent">
					<h3>
						<img id="el5Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el5', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_location') }}
					</h3>
				</div>
				<div id="el5Child" class="child" style="display: none;">
					<div id="el5Child_0">
						<div id="el5Child_0a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_location") }}</label>
						</div>
						<div id="el5Child_0b">
							<select id="location" v-model="animation.location" multiple>
								<option value="0">{{ pmb.getMessage("animation", "update_add_animation_noLocation") }}</option>
								<option v-for="loc in formdata.locations" :value="loc.idlocation">{{loc.location_libelle}}</option>
							</select>
						</div>
					</div>
				</div>
				
				<!-- Indexation -->
				<div id="el6Parent" class="parent">
					<h3>
						<img id="el6Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el6', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_indexation') }}
					</h3>
				</div>
				<div id="el6Child" class="child" style="display: none;">
					<div id="el6Child_0">
						<div id="el6Child_0a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_categories") }}</label>
						</div>
						<div id="el6Child_0b">
							<template v-for="(categ, index) in animation.categories">
								<div class="row">
									<input :id="'categories' + index" v-model="categ.displayLabel" class="saisie-30emr" type="text" completion="categories_mul" :autfield="'categoriesId' + index" autocomplete="off"/>
									<input :id="'categoriesId' + index" type="hidden" @change="changeMultipleField('categories', index, $event)"/>
									<input @click="deleteMultipleField('categories', index)" :title="pmb.getMessage('animation', 'update_add_animation_categories')" class="bouton" type="button" value="X"/>
									<input v-if="index == animation.categories.length - 1" @click="addMultipleField('categories', index, $event)" :title="pmb.getMessage('animation', 'update_add_animation_categories')" class="bouton" type="button" value="+"/>
								</div>
							</template>
						</div>
					</div>
					<div id="el6Child_1" v-if="formdata.globals.conceptsActive == 1">
						<div id="el6Child_1a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_concepts") }}</label>
						</div>
						<div id="el6Child_1b">
							<template v-for="(concept, index) in animation.concepts">
								<div class="row">
									<input :id="'concepts' + index" v-model="concept.displayLabel" class="saisie-30emr" type="text" completion="onto" :autfield="'conceptsId' + index" param2="1" autocomplete="off" att_id_filter="http://www.w3.org/2004/02/skos/core#Concept"/>
									<input :id="'conceptsId' + index" type="hidden" @change="changeMultipleField('concepts', index, $event)"/>
									<input @click="deleteMultipleField('concepts', index)" :title="pmb.getMessage('animation', 'update_add_animation_concepts')" class="bouton" type="button" value="X"/>
									<input v-if="index == animation.concepts.length - 1" @click="addMultipleField('concepts', index, $event)" :title="pmb.getMessage('animation', 'update_add_animation_concepts')" class="bouton" type="button" value="+"/>
								</div>
							</template>
						</div>
					</div>
				</div>
				
				<!-- Notes -->
				<div id="el7Parent" class="parent">
					<h3>
						<img id="el7Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el7', true); return false;">
						{{ pmb.getMessage('animation', 'animation_notes') }}
					</h3>
				</div>
				<div id="el7Child" class="child" style="display: none;">
					<div id="el7Child_0">
						<div id="el7Child_0a">
							<label class='etiquette'>{{ pmb.getMessage('animation', 'animation_comment') }}</label>
						</div>
						<div id="el7Child_0b">
							<textarea id="animation.comment" class='saisie-40em' rows="5" v-model="animation.comment"></textarea>
						</div>
					</div>
					<div id="el7Child_1">
						<div id="el7Child_1a">
							<label class='etiquette'>{{ pmb.getMessage('animation', 'animation_description') }}</label>
						</div>
						<div id="el7Child_1b">
							<textarea id="animation.description" type="text" class='saisie-40em' rows="5" v-model="animation.description"></textarea>
						</div>
					</div>
				</div>
				
				<!-- Quotas -->
				<div id="el8Parent" class="parent">
					<h3>
						<img id="el8Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el8', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_quotas') }}
					</h3>
				</div>
				<div id="el8Child" class="child" style="display: none;">
					<div><i>{{ pmb.getMessage("animation", "add_animation_quotas_info_message_illimited") }}</i></div>
					<div id="el8Child_0">
						<div id="el8Child_0a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_globalQuota") }}</label>
						</div>
						<div id="el8Child_0b">
							<input @change="checkQuotas" @blur="checkQuotas" id="animation.globalQuota" v-model="animation.globalQuota" type="number"  class='saisie-20em'>
							<input @click="animation.globalQuota = 0" :title="pmb.getMessage('animation', 'update_add_animation_deleteQuota')" class="bouton" type="button" value="X"/>
						</div>
						
					</div>
					<div id="el8Child_1">
						<div id="el8Child_1a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_internetQuota") }}</label>
						</div>
						<div id="el8Child_1b">
							<input @change="checkQuotas" @blur="checkQuotas" id="animation.internetQuota" v-model="animation.internetQuota" type="number"  class='saisie-20em'>
							<input @click="animation.internetQuota = 0" :title="pmb.getMessage('animation', 'update_add_animation_deleteQuota')" class="bouton" type="button" value="X"/>
							<span v-if="internetQuotaTooHigh">{{ pmb.getMessage("animation", "animation_internet_quota_higher") }}</span>
						</div>
					</div>
					
					<div id="el8Child_2">
						<div id="el8Child_2a">
						 	<input id="allowWaitingList" type="checkbox" v-model="animation.allowWaitingList"/>
							<label for="allowWaitingList" class='etiquette'>{{ pmb.getMessage("animation", "anim_allow_waiting_list") }}</label>
						</div>
					</div>
					
					<div id="el8Child_3">
						<div id="el8Child_3a">
						 	<input id="autoRegistration" type="checkbox" v-model="animation.autoRegistration"/>
							<label for="autoRegistration" class='etiquette'>{{ pmb.getMessage("animation", "anim_auto_registration") }}</label>
						</div>
					</div>
					
					<div id="el8Child_4">
						<div id="el8Child_4a">
						 	<input id="onlyContactRegistred" type="checkbox" v-model="animation.uniqueRegistration"/>
							<label for="onlyContactRegistred" class='etiquette'>{{ pmb.getMessage("animation", "anim_only_contact_registred") }}</label>
						</div>
					</div>
				</div>
				
				<!-- Types de communication -->
				<div id="el9Parent" class="parent">
					<h3>
						<img id="el9Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el9', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_mailing') }}
					</h3>
				</div>
				<div id="el9Child" class="child" style="display: none;">
					<div id="el9Child_0">
						<div id="el9Child_0a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_mailing") }}</label>
						</div>
						<div id="el9Child_0b">
							<select id="location" v-model="animation.mailingType" multiple>
								<option value="0">{{ pmb.getMessage("animation", "update_add_animation_noMailing") }}</option>
								<option v-for="mailingType in formdata.mailingTypes" :value="mailingType.idMailingType">{{ mailingType.name }}</option>
							</select>
						</div>
					</div>
				</div>
				
				<!-- CPs -->
				<template v-if="animation.customFields.length">
					<div id="el10Parent" class="parent">
						<h3>
							<img id="el10Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el10', true); return false;">
							{{ pmb.getMessage('animation', 'update_add_animation_custom_fields') }}
						</h3>
					</div>
					<div id="el10Child" class="child" style="display: none;">
						<customfields :customfields="animation.customFields" customprefixe="animation" :img="formdata.img" :pmb="pmb" :csrftokens="csrftokens"></customfields>
					</div>
				</template>
		    </div>
		    
		    <!-- CALENDRIER -->
				<div id="el11Parent" class="parent">
					<h3>
						<img id="el11Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el11', true); return false;">
						{{ pmb.getMessage('animation', 'update_add_animation_calendar') }}
					</h3>
				</div>
				<div id="el11Child" class="child" style="display: none;">
					<div id="el11Child_0">
						<div id="el11Child_0a">
							<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_calendar") }}</label>
						</div>
						<div id="el11Child_0b">
							<select id="numCalendar" v-model="animation.numCalendar">
								<option v-for="calendar in formdata.calendar" :value="calendar.id_calendar">{{calendar.name}}</option>
							</select>
						</div>
					</div>
				</div>

		    <!-- Logo -->
			<div id="el12Parent" class="parent">
				<h3>
					<img id="el12Img" class="img_plus" name="imEx" :src='formdata.img.plus' onClick="expandBase('el12', true); return false;">
					{{ pmb.getMessage('animation', 'update_add_animation_logo') }}
				</h3>
			</div>
			<div id="el12Child" class="child" style="display: none;">
				<div id="el12Child_0">
					<div id="el12Child_0a">
						<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_logo_choice") }}</label>
					</div>
					<div id="el12Child_0b">
						<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_logo_folder") }}</label>
					</div>
					<div id="el12Child_0c">
						<select id="numUploadFolder" v-model="animation.logo.uploadFolder">
							<option value="0" selected>{{ pmb.getMessage("animation", "update_add_animation_logo_choice") }}</option>
							<option v-for="uploadFolder in formdata.uploadFolder" :value="uploadFolder.repertoire_id">{{uploadFolder.repertoire_nom}}</option>
						</select>
					</div>
				</div>
				<div id="el12Child_1">
					<div id="el12Child_1a">
						<label class='etiquette' v-if="'' != animation.logo.filename"  v-model="animation.logo.filename">{{ animation.logo.filename }}</label>
					</div>
					<div id="el12Child_1b">
						<input type="file" id="logoFilename" name="logoFilename" @change="setLogoFilename($event)"/>
					</div>
				</div>
				<div id="el12Child_2">
					<div id="el12Child_2a">
						<label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_logo_alt") }}</label>
					</div>
					<div id="el12Child_2b">
						<input id="logoAlt" name="logoAlt" type="text" class='saisie-40em' v-model="animation.logo.alt">
					</div>
				</div>
			</div>
				
		    <component is="script" src="./javascript/ajax.js"></component>
		    
			<div class="row">
				<!-- Boutons -->
				<div class="left">
					<input @click="cancel" class="bouton btnCancel" type="button" :value="pmb.getMessage('animation', 'animation_cancel')"/>
					<input @click="save" class="bouton btnSave" type="button" :value="pmb.getMessage('animation', 'animation_save')"/>
				</div>
				<div class="right">
					<input v-if="animation.id" @click="delAnim(animation.id)" class="bouton btnDelete" type="button" :value="pmb.getMessage('animation', 'animation_delete')"/>
				</div>			
			</div>
   		</form>
	</div>
</template>

<script>
	import customfields from "../../../common/customFields/form/customFields.vue";

	export default {
		props : ["animation", "pmb", "formdata", "csrftokens"],
		data : function(){
			return {
				internetQuotaTooHigh : false,
				interval : null
			}
		},
		created : function() {
			if (this.animation.prices.length == 0) {
				this.animation.prices.push({ id : 0, name : '', value : 0, numPriceType : 0 });
			}
			if (this.animation.location.length == 0) {
				this.animation.location.push('0');
			}
			if (this.animation.mailingType.length == 0) {
				this.animation.mailingType.push(0);
			}
			if (this.animation.categories.length == 0) {
				this.animation.categories.push({ id : 0, displayLabel : '' });
			}
			if (this.animation.concepts.length == 0) {
				this.animation.concepts.push({ id : 0, displayLabel : '' });
			}
			if (this.animation.prices[0].name == '') {
				this.animation.prices[0].numPriceType = this.formdata.priceType[0].idPriceType;
				this.animation.prices[0].name = this.formdata.priceType[0].name;
				this.animation.prices[0].value = this.formdata.priceType[0].defaultValue;
			}
			if (this.animation.globalQuota<this.animation.internetQuota){
				this.internetQuotaTooHigh = true;
			}
			
			if(this.animation.logo && "" != this.animation.logo){
				this.animation.logo = JSON.parse(this.animation.logo);
			}

			if(this.formdata.prefColorUser && 0 == this.animation.id){
				this.animation.numCalendar = this.formdata.prefColorUser;
			}

			this.animation.uniqueRegistration = parseInt(this.animation.uniqueRegistration);

			if(0 != this.formdata.prefUniqueRegistrationUser && 0 == this.animation.id){
				this.animation.uniqueRegistration = this.formdata.prefUniqueRegistrationUser;
			}

			if(0 != this.formdata.prefAutoRegistrationUser && 0 == this.animation.id){
				this.animation.autoRegistration = this.formdata.prefAutoRegistrationUser;
			}

			if(0 != this.formdata.prefWaitingListUser && 0 == this.animation.id){
				this.animation.allowWaitingList = this.formdata.prefWaitingListUser;
			}

			if(this.formdata.prefCommunicationTypeUser && 0 != this.formdata.prefCommunicationTypeUser && 0 == this.animation.id){
				for (const [key, mailingType] of Object.entries(this.formdata.mailingTypes)) {
				    if (mailingType.id == this.formdata.prefCommunicationTypeUser){
						this.animation.mailingType[0] = mailingType.id;
						break;
				    }
				}
				
			}
			
			window.addEventListener("load", function(event) {
				ajax_parse_elements();
			});
		},
		
		components : {
			customfields
		},
		
		methods : {
			save : function() {
				if (!this.animation.numType || this.animation.numType == 0) {
					alert(this.pmb.getMessage('animation', 'animation_error_type'));
					return false;
				}
				
				if (!this.animation.name || this.animation.name == '') {
					alert(this.pmb.getMessage('animation', 'animation_error_name'));
					return false;
				}
				
				if (((!this.animation.event.endDate || this.animation.event.endDate == '') && !this.animation.event.duringDay) || !this.animation.event.startDate || this.animation.event.startDate == '') {
					alert(this.pmb.getMessage('animation', 'animation_error_date'));
					return false;
				}

				if (this.animation.prices[0].name == '') {
					alert(this.pmb.getMessage('animation', 'animation_error_priceType'));
					return false;
				}
				
				if (this.internetQuotaTooHigh == true) {
					alert(this.pmb.getMessage('animation', 'animation_internet_quota_higher'));
					return false;
				} 

				if ((0 != this.animation.logo.uploadFolder && "" == this.animation.logo.filename) || (0 == this.animation.logo.uploadFolder && this.animation.logo.filename.length)) {
					alert(this.pmb.getMessage('animation', 'animation_check_logo_filename'));
					return false;
				}
				
				let msg = '';
				for (let i = 0; i < this.animation.customFields.length; i++) {
					if (this.animation.customFields[i].customField.mandatory == '1') {
						if (this.isCustomFieldEmpty(i)) {
							msg = this.pmb.getMessage('animation', 'animation_error_cp');
							msg = msg.replace('%s', this.animation.customFields[i].customField.titre);
							alert(msg);
							return false;
						}
					}
				}

				if(typeof tinyMCE !== "undefined"){
					var inst, contents = new Object();
					for (inst in tinyMCE.editors) {
					    if (tinyMCE.editors[inst].getContent){
					        contents[inst] = tinyMCE.editors[inst].getContent();
					    }
					}
					let keys = Object.keys(contents); 
					if(keys.length){
						if(keys.includes("animation.comment")){
							this.animation.comment = contents["animation.comment"];
						}
						if(keys.includes("animation.description")){
							this.animation.description = contents["animation.description"];
						}
					}
				}
				
				let url = "./ajax.php?module=animations&categ=animations&action=save";
				var formData = new FormData();
				formData.append('data', JSON.stringify(this.animation));
				
				var file = document.getElementById("logoFilename") ? document.getElementById("logoFilename").files[0] : "";
				formData.append('image', file); 
				
				fetch(url, {
					method: 'POST',
					body: formData
				}).then(function(response) {
					if (response.ok) {
						response.text().then(function(id) {
						  document.location = './animations.php?categ=animations&action=view&id=' + id;
					    });
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}).catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				}); 
			},

			cancel : function() {
				history.go(-1);
			},
			
			addPrice : function() {
			    
			    var newPrice = {
					id : 0,
					name : '',
					value : 0,
					numPriceType : 1
				};
			    
			    if (this.formdata.priceType[0]) {
			        newPrice.numPriceType = this.formdata.priceType[0].idPriceType;
			        newPrice.name = this.formdata.priceType[0].name;
			        newPrice.value = this.formdata.priceType[0].defaultValue;
			    };
			    
				this.animation.prices.push(newPrice);
			},
			
			delAnim : function(idAnim) {
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
					
					let url = "./ajax.php?module=animations&categ=animations&action=delete";
					var data = new FormData();
					data.append('data', JSON.stringify({
						id:this.animation.idAnimation,
						delChildrens: delChildrens
					}));
					
					fetch(url, {
						method: 'POST',
						body: data
					}).then(function(response) {
						if (response.ok) {
							document.location = './animations.php?categ=animations&action=list';
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					}).catch(function(error) {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
				}
			},

			changeEndDate : function() {
				if(this.interval === null) {
					this.changeDateInterval()
				}

				let newDate = new Date(this.animation.event.startDate);
				newDate.setDate(newDate.getDate() + this.interval);

				let formatDate = newDate.getFullYear() + '-' + ('0' + (newDate.getMonth()+1)).slice(-2) + '-' + ('0' + newDate.getDate()).slice(-2);
				this.animation.event.endDate = formatDate;

			},

			changeDateInterval: function() {
				const diffInMs = Math.abs(new Date(this.animation.event.endDate) - new Date(this.animation.event.startDate));
				const diffInDay = diffInMs / (1000 * 60 * 60 * 24);
				this.interval = diffInDay;
			},
			
			changeValuePrice : function(index) {
				for (let i = 0; i < this.formdata.priceType.length ; i++) {
					if (this.formdata.priceType[i].idPriceType == this.animation.prices[index].numPriceType) {
						this.animation.prices[index].value = this.formdata.priceType[i].defaultValue;
						this.animation.prices[index].name = this.formdata.priceType[i].name;
					}
				}
			},
			
			deleteParentAnimation : function() {
				document.getElementById('parentAnimation').value = '';
				this.animation.numParent = 0;
			},
			
			changeMultipleField : function(fieldName, index, event) {
				let libelle = document.getElementById(fieldName + index).value;
				let id = parseInt(event.target.value, 10);
				
				if (typeof this.animation[fieldName][index] !== 'undefined' || this.animation[fieldName][index] == 0) {
					this.animation[fieldName][index].id = id;
					this.animation[fieldName][index].displayLabel = libelle;
				} else {
					this.animation[fieldName].push({ id : id, displayLabel : libelle });
				}
			},
			
			addMultipleField : function(fieldName, index, event) {
				this.animation[fieldName].push({
					id : 0,
					displayLabel : ''
				});
				index = parseInt(index, 10) + 1;
				this.$nextTick(() => {
					let elt = document.getElementById(fieldName + index);
					ajax_pack_element_without_spans(elt, event);
				});
			},
			
			deleteMultipleField : function(fieldName, index) {
				if (this.animation[fieldName].length > 1) {
					this.animation[fieldName].splice(index, 1);
				} else {
					this.animation[fieldName][index].id = 0;
					this.animation[fieldName][index].displayLabel = '';
				}
				document.activeElement.blur();
			},
			
			checkQuotas : function() {
				this.internetQuotaTooHigh = false;
				if (parseInt(this.animation.globalQuota) < parseInt(this.animation.internetQuota)) {
					this.internetQuotaTooHigh = true;
				}
			},
			
			isCustomFieldEmpty : function(customFieldIndex) {
				let flag = true;
				let value = this.animation.customFields[customFieldIndex].customValues[0].value;
				
				switch (true) {
					case (Array.isArray(value) && value.length != 0) :
					case (typeof value == 'string' && value != '') :
					case (typeof value == 'number') :
						flag = false;
						break;
				}
				
				return flag;
			},
			
			setLogoFilename : function(event) {
				this.animation.logo.filename = event.target.files[0].name;
			},
		} 
	}
</script>