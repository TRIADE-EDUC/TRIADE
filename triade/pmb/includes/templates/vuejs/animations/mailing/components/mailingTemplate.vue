<template>
	<div id="mailingTemplateView">
		<div v-if="'mailingTemplate' == action">
			<div class="form-mailingTempalte" id="formMalingTemplate">
				<form action="">
					<h2 class="section-sub-title">
						{{ animation.name }}
					</h2>
					
					<!-- Liste des participants -->
					<div id="el0Parent" class="parent">
						<h3>
							<img id="el0Img" class="img_plus" name="imEx" :src='img.plus' onClick="expandBase('el0', true); return false;">
							{{ pmb.getMessage("animation", "mailing_list_registred") }}
						</h3>
					</div>
					<div id="el0Child" class="child" style="display: none;">
						<div id="el0Child_0">
							<div id="el0Child_0a" class="row uk-clearfix">
								<table>
									<tr>
										<td>{{ pmb.getMessage("animation", "list_registration_name") }}</td>
										<td>{{ pmb.getMessage("animation", "list_registration_email") }}</td>
										<td>{{ pmb.getMessage("animation", "list_registration_phone") }}</td>
										<td>{{ pmb.getMessage("animation", "mailing_nb_registered") }}</td>
									</tr>
									<tr v-for="registred in registration">
										<td>{{ registred.name }}</td>
										<td v-if="registred.email">
											{{ registred.email }}
										</td>
										<td v-else>
											<span style="color:red">{{ pmb.getMessage("animation", "no_email") }}</span>
										</td>
										<td>{{ registred.phoneNumber }}</td>
										<td>{{ registred.nbRegisteredPersons }}</td>
									</tr>
								</table>
							</div>
						</div>
					</div>	
						
					<!-- Paramétrage mail -->
					<div id="el1Parent" class="parent">
						<h3>
							<img id="el1Img" class="img_plus" name="imEx" :src='img.minus' onClick="expandBase('el1', true); return false;">
							{{ pmb.getMessage('animation', 'mailing_send_email') }}
						</h3>
					</div>
					<div id="el1Child" class="child" style="display: block;">
						<template v-if="formdata.mailingTemplate">
							<div id="el1Child_4">
								<div id="el1Child_4a">
									<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_admin_mailtpl") }} :</label>
								</div>
								<div id="el1Child_4b">
									<select id="mailingTemplate" v-model="tplSelected">
										<option v-for="(mailingTemplate, index) in formdata.mailingTemplate" :value="index">{{ mailingTemplate.mailtplName }}</option>
									</select>
									<input @click="insertTemplate()" :title="pmb.getMessage('animation', 'mailing_mailtpl_insert')" class="bouton" type="button" :value="pmb.getMessage('animation', 'mailing_mailtpl_insert')"/>
								</div>					
							</div>
						</template>
						<div id="el1Child_0">
							<div id="el1Child_0a">
								<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_object_mail") }} :</label>
							</div>
							<div id="el1Child_0b">
								<input id="mailingObject" type="text" class='saisie-40em'>
							</div>					
						</div>
						<div id="el1Child_5">
							<div id="el1Child_5a">
								<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_attachment_mail") }} :</label>
							</div>
							<div id="el1Child_5b">
								<input id="mailingAttachment" name="mailingAttachment" type="file" @change="setAttachmentFilename($event)">
							</div>					
						</div>
						<div id="el1Child_1">
							<div id="el1Child_1a">
								<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_body_mail") }} :</label>
							</div>
							<div id="el1Child_1b">
								<textarea id="mailingBody" class='saisie-40em' rows="25"></textarea>
							</div>					
						</div>
						<div id="el1Child_2">
							<div id="el1Child_2a">
								<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_mailtpl_form_selvars") }} :</label>
							</div>
							<div id="el1Child_2b">
								<select id="mailingSelVars" v-model="selVarsSelected">
									<template v-for="(selVar, key) in formdata.selVars">
										<optgroup :label="pmb.getMessage('animation', 'selvar_animation_group')">
											<option v-for="(sVar, index) in selVar" :value="index">{{ sVar }}</option>
										</optgroup>
									</template>
								</select>
								<input @click="insertSelVars()" :title="pmb.getMessage('animation', 'mailing_mailtpl_insert')" class="bouton" type="button" :value="pmb.getMessage('animation', 'mailing_mailtpl_insert_selvars')"/>
							</div>					
						</div>
						<div id="el1Child_3">
							<div id="el1Child_3a">
								<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_associated_campaign") }} :</label>
								<input
								type='checkbox'
								id="mailingAssociatedCampaign"
								name="mailingAssociatedCampaign"
								:value="dataMail.mailingAssociatedCampaign"
								v-model="dataMail.mailingAssociatedCampaign"/>
							</div>
						</div>
						<div id="el1Child_4">
							<div id="el1Child_4a">
								<label for="mailingSenders" class='etiquette'>{{ pmb.getMessage("animation", "animation_mailing_sender") }} :</label>
							</div>
							<div id="el1Child_4b">
								<select id="mailingSenders" v-model="senderSelected">
										<option v-for="sender in formdata.senders" :value="sender">{{ sender.name }}</option>
								</select>
							</div>					
						</div>
					</div>
					<div class="row">
						<!-- Boutons -->
						<div class="left">
							<input @click="cancel" class="bouton btnCancel" type="button" :value="pmb.getMessage('animation', 'animation_cancel')"/>
						</div>
						<div class="right">
							<input @click="send" class="bouton btnSave" type="button" :value="pmb.getMessage('animation', 'mailing_animation_send')"/>
						</div>			
					</div>
				</form>
			</div>
		</div>
		<div v-else>
			<h2 class="section-sub-title">
				{{ animation.name }}
			</h2>
			<!-- Liste des participants -->
			<div id="el0Parent" class="parent">
				<h3>
					<img id="el0Img" class="img_plus" name="imEx" :src='img.plus' onClick="expandBase('el0', true); return false;">
					{{ pmb.getMessage("animation", "mailing_list_send_mail") }}
				</h3>
			</div>
			<div id="el0Child" class="child" style="display: none;">
				<div id="el0Child_0">
					<div id="el0Child_0a" class="row uk-clearfix">
						<table>
							<tr>
								<td>{{ pmb.getMessage("animation", "list_registration_name") }}</td>
								<td>{{ pmb.getMessage("animation", "list_registration_email") }}</td>
								<td>{{ pmb.getMessage("animation", "mailing_status_succes_fail") }}</td>
							</tr>
							<template v-for="list in mailingdetail.listPersons">
								<tr v-for="person in list">
									<td>{{ person.NAME }}</td>
									<td v-if="person.EMAIL">
										{{ person.EMAIL }}
									</td>
									<td v-else>
										<span style="color:red">{{ pmb.getMessage("animation", "no_email") }}</span>
									</td>
									<td v-if="person.SUCCESS">
										{{ pmb.getMessage("animation", "mailing_succes_mail") }}
									</td>
									<td v-else>
										<span style="color:red">{{ pmb.getMessage("animation", "mailing_fail_mail") }}</span>
									</td>
								</tr>
							</template>
						</table>
					</div>
				</div>
			</div>
			<div id="el1Child_0">
				<div id="el1Child_0a">
					<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_object_mail") }} :</label>
				</div>
				<div id="el1Child_0b">
					<input id="mailingObject" type="text" class='saisie-40em' :value="mailingdetail.mailtplObjet" disabled>
				</div>					
			</div>
			<div id="el1Child_1">
				<div id="el1Child_1a">
					<label class='etiquette'>{{ pmb.getMessage("animation", "mailing_body_mail") }} :</label>
				</div>
				<div id="el1Child_1b">
					<textarea id="mailingBodyDisabled" class='saisie-40em' rows="25" :value="mailingdetail.mailtplTpl" disabled></textarea>
				</div>					
			</div>
			<div class="row">
				<!-- Boutons -->
				<div class="left">
					<input @click="cancel" class="bouton btnCancel" type="button" :value="pmb.getMessage('animation', 'mailing_list_back')"/>
				</div>
			</div>
		</div>
	</div>
		
</template>


<script>
	export default {
		props : ["pmb", "animation", "action", "img", "registration", "formdata", "mailingdetail", "deflt_associated_campaign"],
		data:function(){
			return {
				dataMail:{
					mailingAssociatedCampaign : false
				},
				tplSelected : 0,
				selVarsSelected : 0,
				mailingTemplate : {},
				senderSelected : 0,
				hover:-1
			}
		},
		created:function(){
			if (this.action == "mailingTemplate") {
		        for (let i = 0; i<this.formdata.senders.length; i++)
		        {
		        	if (this.formdata.senders[i].selected == "selected"){
				        this.senderSelected = this.formdata.senders[i];
				        break;
		        	}
		        }
			}
			this.dataMail.mailingAssociatedCampaign = this.deflt_associated_campaign == '1';
		},
		methods:{
			insertTemplate: function(){
				let tpl = this.formdata.mailingTemplate[this.tplSelected];
				
				let objectMail = document.getElementById("mailingObject");
				let tplMail = document.getElementById("mailingBody");
				
				objectMail.value = tpl.mailtplObjet;
				if(!document.getElementById("mailingBody_ifr")){            
					tplMail.value = tpl.mailtplTpl;
		        }else{
		            tinyMCE_execCommand('mceInsertContent',false,tpl.mailtplTpl);
		        }
			},
			
			insertSelVars: function(){
				let selVars = "!!" + this.formdata.selVars.animation_group[this.selVarsSelected] + "!!";
				let tplMail = document.getElementById("mailingBody");
				
		        if(!document.getElementById("mailingBody_ifr")){            
		        	var start = tplMail.selectionStart;           
		            var start_text = tplMail.value.substring(0, start);
		            var end_text = tplMail.value.substring(start);
		            tplMail.value = start_text + selVars + end_text;
		        }else{
		            tinyMCE_execCommand('mceInsertContent',false,selVars);
		        }
			},
			
			send: function(){
				if (this.senderSelected.mail == "" || typeof this.senderSelected.mail == "undefined" || !this.checkMail(this.senderSelected.mail)) {
					alert(this.pmb.getMessage("animation", "animation_mailing_sender_no_mail"))
					return;
				}
				this.startPatience();
				let objectMail = document.getElementById("mailingObject");
				if (!document.getElementById("mailingBody_ifr")){
					var tplMail = document.getElementById("mailingBody").value;
				} else {
					let tinyNode = tinymce.get("mailingBody");
					var tplMail = tinyNode.getContent();
				}
				
				this.dataMail.template = {
					"mailtplObjet" : objectMail.value,
					"mailtplTpl" : tplMail
				}
				
				this.dataMail.numSender = this.senderSelected.id
				
				if(!this.dataMail.mailingAssociatedCampaign){
					this.dataMail.mailingAssociatedCampaign = 0;
				}
				this.dataMail.idAnimation = this.animation.id;

				let url = "./ajax.php?module=animations&categ=mailing&action=sendManualMail";
				var data = new FormData();
				
				var file = document.getElementById("mailingAttachment") ? document.getElementById("mailingAttachment").files[0] : "";
				data.append('attachmentFile', file); 
				
				data.append('data', JSON.stringify(this.dataMail));
				
				fetch(url, {
					method: 'POST',
					body: data
				}).then((response) => {
					if (response.ok) {
						response.text().then((id)=> {
						    document.location = './animations.php?categ=animations&action=view&id=' + id;
					    });
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}).catch((error) => {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			
			cancel : function() {
				history.go(-1);
			},
			
			startPatience : function(){
				let node = document.getElementById('mailing');
				node.classList.add("mailing_overlay_patience");
				
				var img = document.createElement('img');
				img.setAttribute("src", this.img.patience);
				img.setAttribute("class", "mailing_img_patience");
				document.getElementById('contenu').appendChild(img)
				 
			},
			
			checkMail : function(mail) {
				var patt = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				var res = patt.test(mail);
				return res;
			},
			
			setAttachmentFilename : function(event) {
				this.dataMail.filename = event.target.files[0].name;
			},
		}
	}
</script>