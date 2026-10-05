<template>
	<div id="editSignature">
		<form class="form-admin">
			<h3>
				{{ pmb.getMessage("digital_signature", "admin_signature_edit_management") }}
			</h3>
			<div class='row' v-if="signdata.uploadFolder == 0">
				<div class="row uk-clearfix">
					<span class="msg-perio" >{{ pmb.getMessage('digital_signature', 'admin_digital_signature_erreur_folder') }}</span>
				</div>
			</div>			
			<div class='form-contenu'>
				<div class='row'>
					<div class="row uk-clearfix">
						<label for="signature_label" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_signature_label") }}</label>
					</div>	
					<div class="row uk-clearfix">
						<input id="signature_label" type="text"  v-model="signdata.signature.name" class='saisie-50em' required>
					</div>	
				</div>
				<div class='row'>
					<div class="row uk-clearfix">
						<label for="signature_cert" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_signature_cert") }} </label>
					</div>
					<div class="row uk-clearfix">
						<select id="signature_cert" class='saisie-50em' v-model="signdata.signature.numCert">
							<option v-for="cert in signdata.certificateList" :value="cert.id">{{ cert.name }}</option>
						</select>
					</div>	
				</div>
				<div class='row'>
					<div class="row uk-clearfix">
						<label for="signature_type" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_signature_type") }} </label>
					</div>	
					<div class="row uk-clearfix">
						<select id="signature_type" class='saisie-50em' v-model="signdata.signature.type">
							<option v-for="(type, index) in signdata.types" :value="index">{{ type }}</option>
						</select>
					</div>	
				</div>
				<hr/>
				
				<div class='row' v-if="signdata.signature.type == 11">
					<fieldsdocnumform :pmb="pmb" :action="action" :img="img" :tab="tab" @updateField="feedSignDataFields($event)"></fieldsdocnumform>
				</div>
				
				<div class='row'>
					<div class="left">
					    <input  @click="cancel" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'admin_digital_signature_cancel')"/>
					    <input  @click="save" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'admin_digital_signature_save')"/>
				    </div>
					<div class="right" v-if="signdata.signature.id != 0">
					    <input  @click="del" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'admin_digital_signature_delete')"/>
				    </div>
				</div>
		    </div>
	    </form>
	</div>
</template>

<script>
	import fieldsdocnumform from "./fieldsDocnumForm.vue";
	
	export default {
		props : ["pmb", "action", "signdata", "img"],
		
		data: function () {
			return {
				fields : null,
				tab : this.signdata.signature.fields ?? []
			}
		},
		components : {
			fieldsdocnumform,
		},
		methods: {
			del :function(){
				if(confirm(this.pmb.getMessage('digital_signature', 'admin_digital_signature_ask_del'))){
					document.location = './admin.php?categ=digital_signature&sub=signature&action=delete&id=' + this.signdata.signature.id;
				}
				
			},			
			cancel : function() {
				document.location = './admin.php?categ=digital_signature&sub=signature';
			},
			save : function() {
				var data = new FormData();
				if(this.tab){
					this.signdata.signature.fields = JSON.stringify(this.tab); 
				}
				
				data.append('data', JSON.stringify(this.signdata.signature));
				
				var url = "./ajax.php?module=admin&categ=digital_signature&sub=signature&action=save";
				
				fetch(url, {
					method : 'POST',
					body : data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=digital_signature&sub=signature';
					} else {
						console.log(this.pmb.getMessage('digital_signature', 'admin_digital_signature_no_response'));
					}
				}).catch(function(error) {
					console.log(this.pmb.getMessage('digital_signature', 'admin_digital_signature_error_fetch') + error.message);
				});
			},
			
			feedSignDataFields : function(event){
				this.tab.push(event);
			},
		}
	}
</script>