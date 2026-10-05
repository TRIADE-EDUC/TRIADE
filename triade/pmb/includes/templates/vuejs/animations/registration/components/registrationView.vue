<template>
	<div id="registrationView">
		<form class="form-admin">
			<h3>{{ pmb.getMessage("animation", "view_animation_viewRegistration") }}</h3>
			<table>
				<tr>
					<td>
						<div v-if="!isEmpty(registrationlist.name)">
							{{ pmb.getMessage("animation", "view_animation_startDate") }} : {{ animations.event.startDate }}
						</div>
					</td>
				</tr>
			
			</table>
			<div class="left">
				<input @click="cancel" class="bouton" type="button"
					:value="pmb.getMessage('animation', 'animation_return')" /> 
					
				<input @click="edit" class="bouton" type="button"
					:value="pmb.getMessage('animation', 'animation_modif')" />
			</div>
			<div class="right">
				<input @click="delAnim" class="bouton" type="button"
					:value="pmb.getMessage('animation', 'animation_delete')" />
			</div>
		</form>
	</div>
</template>



<script>
	export default {
		props : ["registrationlist","pmb"],
		methods: {
			cancel : function() {
				document.location = './animations.php?categ=registration&action=list';
			},
			delAnim : function() {
				var resultat = window.confirm(this.pmb.getMessage('animation', 'animation_confirm_del_registration'));
				if (resultat == 0) {
					event.preventDefault();
				} else {
					var data = new FormData();
					data.append('data', JSON.stringify({id:this.animations.idAnimation}));

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
				document.location = './animations.php?categ=animations&action=edit&id=' + this.animations.id;
			}
		},
	}
</script>