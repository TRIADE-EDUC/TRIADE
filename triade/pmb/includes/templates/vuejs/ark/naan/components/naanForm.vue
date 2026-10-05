<template>
	<div>
		<form class="form-admin">
			<h3>
				{{ pmb.getMessage("ark", "admin_ark_naan_management") }}
			</h3>
			<div class='form-contenu'>
				<div class='row'>
					<template v-for="(naanVal, index) in naan.list">
						<div class="colonne3">
							<label :for="'naan'+index" class='etiquette'>{{ pmb.getMessage("ark", "admin_ark_naan_label") }} :</label>
						</div>	
						<div class="colonne_suite">
							<input :id="'naan'+index" type="text"  v-model="naan.list[index]" class='saisie-50em' required>
						</div>
					</template>						
				</div>
				<div class='row'>
					<br />
					<div class="left">
					    <input  @click="cancel" class="bouton" type="button" :value="pmb.getMessage('ark', 'admin_ark_cancel')"/>
					    <input  @click="save" class="bouton" type="button" :value="pmb.getMessage('ark', 'admin_ark_save')"/>
				    </div>
				</div>
		    </div>
	    </form>
	</div>
</template>

<script>

	export default {
		
		props : ["pmb", "action", "img", "naan"],
		components : {
		},
		methods:{
			save : function() {
				var data = new FormData();
				data.append('data', JSON.stringify(this.naan));
				
				var url = "./ajax.php?module=admin&categ=ark&sub=naan&action=save";
				
				fetch(url, {
					method : 'POST',
					body : data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=ark';
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}).catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			cancel : function() {
				document.location = './admin.php?categ=ark';
			},
		}
	}
</script>