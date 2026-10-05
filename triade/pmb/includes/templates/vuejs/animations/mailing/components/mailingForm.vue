<template>
	<div>
		<form class="form-admin">
			<h3>
				<template v-if="'edit' == action">
					{{ pmb.getMessage("animation", "admin_animations_mailing_edit") }}
				</template>
				<template v-else>
					{{ pmb.getMessage("animation", "admin_animations_mailing_add") }}
				</template>
			</h3>
			<div class='form-contenu'>
				<!-- Boutons collapse/expand -->
				<div class="row">
					<a href="#" onClick="expandAll(); return false;">
						<img id="expandAll" :src="img.expandAll" border="0"/>
					</a>
					<a href="#" onClick="collapseAll(); return false;">
						<img id="collapseAll" :src="img.collapseAll" border="0"/>
					</a>
				</div>
				
				<!-- Informations générales -->
				<div id="el0Parent" class="parent">
					<h3>
						<img id="el0Img" class="img_plus" name="imEx" :src='img.minus' onClick="expandBase('el0', true); return false;">
						{{ pmb.getMessage("animation", "update_add_animation_general_informations") }}
					</h3>
				</div>
				<div id="el0Child" class="child" style="display: block;">
					<div id="el0Child_0">
						<div id="el0Child_0a" class="row uk-clearfix">
							<label for="mailingstypes.name" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_label") }} :</label>
						</div>							
						<div id="el0Child_0b" class="row uk-clearfix">
							<input id="mailingstypes.name" type="text" name="name" v-model="mailingstypes.name" class='saisie-50em' required>
						</div>									
						<div id="el0Child_0e" class="row uk-clearfix">
							<label class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_send") }} :</label>
						</div>							
						<div id="el0Child_0f" class="row uk-clearfix">
						<template v-if="'inscriptions'== type">
							<template v-if="typecomisset.registration == 0 || this.nativePeriodicity == 3 ">
								<input id="mailingstypes.registration" name="periodicity" type="radio"  v-model="mailingstypes.periodicity" value="3" required> 
								<label for="mailingstypes.registration" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_registration") }}</label>
							</template>
							<template v-if="typecomisset.confirmation == 0 || this.nativePeriodicity == 4 ">
								<input id="mailingstypes.confirmation" name="periodicity" type="radio"  v-model="mailingstypes.periodicity" value="4" required> 
								<label for="mailingstypes.confirmation" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_confirmation") }}</label>
							</template>
							<template v-if="typecomisset.annulation == 0 || this.nativePeriodicity == 5 ">
								<input id="mailingstypes.annulation" name="periodicity" type="radio"  v-model="mailingstypes.periodicity" value="5" required> 
								<label for="mailingstypes.annulation" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_annulation") }}</label>
							</template>
							<template v-if="typecomisset.sendtobibli == 0 || this.nativePeriodicity == 6 ">
								<input id="mailingstypes.sendtobibli" name="periodicity" type="radio"  v-model="mailingstypes.periodicity" value="6" required> 
								<label for="mailingstypes.sendToBibli" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_sendtobibli") }}</label>
							</template>
						</template>
						<template v-else>
							<input id="mailingstypes.periodicity" name="periodicity" type="radio"  v-model="mailingstypes.periodicity"  value="1" required> 
							<label for="mailingstypes.periodicity" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_beforeAnim") }}</label>
							<input id="mailingstypes.afterAnim" name="periodicity" type="radio"  v-model="mailingstypes.periodicity" value="2" required> 
							<label for="mailingstypes.afterAnim" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_afterAnim") }}</label>
						</template>
						</div>					
						<div id="el0Child_0g" class="row uk-clearfix">
							<label for="mailingstypes.numTemplate" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_template") }} :</label>
						</div>							
						<div id="el0Child_0h" class="row uk-clearfix">
							<select id="mailingstypes.numTemplate" name="numTemplate" v-model="mailingstypes.numTemplate" required>
								<option v-for="mailtpl in mailtplList" :value="mailtpl.idMailtpl">{{ mailtpl.mailtplName }}</option>
							</select>
						</div>
						<template v-if="mailingstypes.periodicity == 1 || mailingstypes.periodicity == 2">
							<div id="el0Child_0c" class="row uk-clearfix">
								<label for="mailingstypes.delay" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_delay") }} :</label>
							</div>							
							<div id="el0Child_0d" class="row uk-clearfix">
								<input id="mailingstypes.delay" type="number" name="delay"  v-model.number="mailingstypes.delay" class='saisie-50em' required min="0">
							</div>								
							<div id="el0Child_0i" class="row uk-clearfix">
								<input id="mailingstypes.autoSends" name="autoSend" type="checkbox" v-model="mailingstypes.autoSend"  value="1" > 
								<label for="mailingstypes.autoSends" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_type") }}</label>
							</div>
							<div id="el0Child_0j" class="row uk-clearfix">
								<input id="mailingstypes.campaign" name="campaign" type="checkbox" v-model="mailingstypes.campaign"  value="0" > 
								<label for="mailingstypes.campaign" class='etiquette'>{{ pmb.getMessage("animation", "mailing_associated_campaign") }}</label>
							</div>
						</template>	
						<div id="el0Child_0g" class="row uk-clearfix">
							<label for="mailingstypes.numSender" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_sender") }} :</label>
						</div>							
						<div id="el0Child_0k" class="row uk-clearfix">
							<select id="mailingstypes.numSender" name="numSender" v-model="mailingstypes.numSender" required>
								<option v-for="sender in senders" :value="sender.id">{{ sender.name }}</option>
							</select>
						</div>							
					</div>
				</div>
				<div class='row'>
					<br />
					<div class="left">
					    <input  @click="cancel" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_cancel')"/>
					    <input  @click="save" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_save')"/>
				    </div>
				    <template v-if="'edit' == action">
					    <div class="right">
						    <input @click="checkMailTypeUse" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_delete')"/>
					    </div>
				    </template>
				</div>
		    </div>
	    </form>
	</div>
</template>

<script>
	import customfields from "../../../common/customFields/form/customFields.vue";

	export default {
		
		props : ["pmb", "mailingstypes", "action", "img", "senders", "type", "typecomisset"],
		components : {
			customfields
		},
		data: function () {
			return {
			    "mailtplList": $data.mailtplList,
			    "nativePeriodicity": ''
			}  
		},
		created: function() {
		    if (this.action == "add") {
		        this.mailingstypes.autoSend = 1;

		        if (this.type == "inscriptions"){
		        	if (this.typecomisset.registration == 0){
				        this.mailingstypes.periodicity = 3;
		        	} else if (this.typecomisset.confirmation == 0){
				        this.mailingstypes.periodicity = 4;
		        	} else if (this.typecomisset.annulation == 0){
				        this.mailingstypes.periodicity = 5;
		        	} else if (this.typecomisset.sendtobibli == 0){
				        this.mailingstypes.periodicity = 6;
		        	}
		        } else {
			        this.mailingstypes.periodicity = 1;
		        }
		        this.mailingstypes.numTemplate = this.mailtplList[0].id;
		        for (let i = 0; i<this.senders.length; i++)
		        {
		        	if (this.senders[i].selected == "selected"){
				        this.mailingstypes.numSender = this.senders[i].id;
		        	}
		        }
		    } else {
			    this.nativePeriodicity = this.mailingstypes.periodicity;
		    }
		},
		methods:{
			save : function() {
		        for (let i = 0; i<this.senders.length; i++)
		        {
		        	if (this.senders[i].id == this.mailingstypes.numSender){
						if (this.senders[i].mail == "" || typeof this.senders[i].mail == "undefined" || !this.checkMail(this.senders[i].mail)) {
							alert(this.pmb.getMessage("animation", "animation_mailing_sender_no_mail"))
							return;
						}
		        	}
		        }
		        
		        if (!this.mailingstypes.numTemplate){
					alert(this.pmb.getMessage("animation", "animation_mailing_no_template"))
					return;
		        }
				
				var data = new FormData();
				data.append('data', JSON.stringify(this.mailingstypes));
				
				var url = "./ajax.php?module=admin&categ=animations&sub=mailing&action=save";
				
				fetch(url, {
					method : 'POST',
					body : data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=animations&sub=mailing&action=list';
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}).catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			
			del : function() {
				var resultat = window.confirm(this.pmb.getMessage('animation', 'admin_animation_confirm_del_mailing'));
				if (resultat == 0) {
					event.preventDefault();
				} else {
					var data = new FormData();
					data.append('data', JSON.stringify({id:this.mailingstypes.id}));
					
					var url = './ajax.php?module=admin&categ=animations&sub=mailing&action=delete'; 

					fetch(url, {
						method : 'POST',
						body : data,
					}).then(function(response) {
						if (response.ok) {
							document.location = './admin.php?categ=animations&sub=mailing&action=list';
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					}).catch(function(error) {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
				}
			},
			
			checkMailTypeUse : function() {
				let data = new FormData();
				data.append('data', JSON.stringify({id : this.mailingstypes.id}));
				let url = './ajax.php?module=admin&categ=animations&sub=mailing&action=checkTypeMail';
				
				fetch(url, {
					method : 'POST',
					body : data,
				}).then((response) => {
					if (response.ok) {
						response.text().then((flag) => {
							if (flag) {
								alert(this.pmb.getMessage('animation', 'animation_mail_type_used_alert'));
							} else {
								this.del();
							}
						})
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}).catch((error) => {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			
			cancel : function() {
				document.location = './admin.php?categ=animations&sub=mailing&action=list';
			},
			
			checkMail : function(mail) {
				var patt = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				var res = patt.test(mail);
				return res;
			}
		}
	}
</script>