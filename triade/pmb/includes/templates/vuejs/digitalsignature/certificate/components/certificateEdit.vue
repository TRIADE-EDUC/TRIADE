<template>
	<div id="editCertificate">
		<form class="form-admin">
			<h3>
				{{ pmb.getMessage("digital_signature", "admin_certifate_edit_management") }}
			</h3>
			<div class='form-contenu'>
				<div class='row'>
					<div class="row uk-clearfix">
						<label for="certificate_label" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_certifate_label") }} </label>
					</div>	
					<div class="row uk-clearfix">
						<input id="certificate_label" type="text"  v-model="certificate.name" class='saisie-50em' required>
					</div>	
					<div class="row uk-clearfix">
						<label for="certificate_cert" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_certifate_cert") }} </label>
					</div>	
					<div class="row uk-clearfix">
						<input id="certificate_cert" type="text"  v-model="certificate.cert" class='saisie-50em' placeholder='/home/admin/mycert.crt' required>
					</div>	
					<div class="row uk-clearfix">
						<label for="certificate_private_key" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_certifate_private_key") }} </label>
					</div>	
					<div class="row uk-clearfix">
						<input id="certificate_private_key" type="text"  v-model="certificate.privateKey" class='saisie-50em' placeholder='/home/admin/myprivatekey.key' required>
					</div>	
				</div>
				<div class='row'>
					<br />
					<div class="left">
					    <input  @click="cancel" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'admin_digital_signature_cancel')"/>
					    <input  @click="save" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'admin_digital_signature_save')"/>
				    </div>
					<div class="right" v-if="certificate.id">
					    <input  @click="del" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'admin_digital_signature_delete')"/>
				    </div>
				</div>
		    </div>
	    </form>
	</div>
</template>

<script>
	export default {
		props : ["pmb", "action", "certificate", "img"],
		
		data: function () {
			return {
			}
		},
		methods: {
			del :function(){
				if(confirm(this.pmb.getMessage('digital_signature', 'admin_certifate_ask_del'))){
					document.location = './admin.php?categ=digital_signature&sub=certificate&action=delete&id=' + this.certificate.id;
				}
			},			
			cancel : function() {
				document.location = './admin.php?categ=digital_signature&sub=certificate';
			},
			save : function() {
				var data = new FormData();
				data.append('data', JSON.stringify(this.certificate));
				
				var url = "./ajax.php?module=admin&categ=digital_signature&sub=certificate&action=save";
				
				fetch(url, {
					method : 'POST',
					body : data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=digital_signature&sub=certificate';
					} else {
						console.log(this.pmb.getMessage('digital_signature', 'admin_digital_signature_no_response'));
					}
				}).catch(function(error) {
					console.log(this.pmb.getMessage('digital_signature', 'admin_digital_signature_error_fetch') + error.message);
				});
			},
		}
	}
</script>