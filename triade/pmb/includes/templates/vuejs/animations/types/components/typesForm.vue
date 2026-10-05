<template>
	<form class="form-admin" onsubmit="return false;">
		<h3 v-if="'edit' == action">{{ pmb.getMessage("animation", "admin_edit_types") }}</h3>
		<h3 v-else>{{ pmb.getMessage("animation", "admin_add_types") }}</h3>
		<div class='form-contenu'>
			<!-- Informations générales -->
			<div class='row'>
				<div id="el0Child_0">
					<div id="el0Child_0a" class="row uk-clearfix">
						<label :for="types.label" class='etiquette'>{{ pmb.getMessage("animation", "admin_type_label") }} :</label>
					</div>							
					<div id="el0Child_0b" class="row uk-clearfix">
						<input type="text"  v-model="types.label" class='saisie-50em' required>
					</div>							
										
				</div>
			</div>
			<div class='row'>
				<br />
				<div class="left">
				    <input  @click="cancel" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_cancel')"/>
				    <input  @click="checkTypeExist" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_save')"/>
			    </div>
			    <template v-if="'edit' == action && types.id != 1">
				    <div class="right">
					    <input @click="del" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_delete')"/>
				    </div>
			    </template>
			</div>
	    </div>
    </form>
</template>

<script>
	import customfields from "../../../common/customFields/form/customFields.vue";

	export default {
		
		props : ["pmb","types", "action", "img"],
		components : {
			customfields
		},
		data: function () {
			return {
				backupTypes: {}   
			}
		},
		created: function () {
		    var newObject = new Object();
		    for (let index in this.types) {
	            newObject[index] = this.types[index];
		    }
		    this.backupTypes = newObject;
		},
		methods:{
			save : function() {
				var data = new FormData();
				data.append('data', JSON.stringify(this.types));
				
				var url = "./ajax.php?module=admin&categ=animations&sub=types&action=save";
				fetch(url,{
					method: 'POST',
					body: data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=animations&sub=types&action=list';
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}) 
				.catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			del : function() {
				if(1 == this.types.idType){
					alert(this.pmb.getMessage('animation', 'admin_animation_no_delete_status'));
					return false;
				}
				if(true == this.types.hasAnimations){
					alert(this.pmb.getMessage('animation', 'admin_animation_no_delete_animations_exists'));
					return false;				
				}
				var resultat = window.confirm(this.pmb.getMessage('animation', 'admin_animation_confirm_del_status'));
				if (resultat == 0) {
					event.preventDefault();
				} else {
					var data = new FormData();
					
					data.append('data', JSON.stringify({id:this.types.id}));
					
					var url = './ajax.php?module=admin&categ=animations&sub=types&action=delete'; 

					fetch(url,{
					method: 'POST',
					body: data,
						}).then(function(response) {
						if (response.ok) {
							document.location = './admin.php?categ=animations&sub=types&action=list';
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					}) 
					.catch(function(error) {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
				}
			},
			cancel : function() {
				document.location = './admin.php?categ=animations&sub=types&action=list';
			},
			checkTypeExist : function() {
			    
			    if (this.types.id && this.types.id != 0 && this.backupTypes.label === this.types.label) {
					this.save();
			    } else {
					let data = new FormData();
					data.append('data', JSON.stringify({label : this.types.label}));
					let url = './ajax.php?module=admin&categ=animations&sub=types&action=check';
					
					fetch(url, {
						method : 'POST',
						body : data,
					}).then((response) => {
						if (response.ok) {
							response.text().then((flag) => {
								if (flag) {
									alert(this.pmb.getMessage('animation', 'animation_type_already_used_alert'));
								} else {
									this.save();
								}
							})
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					}).catch((error) => {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
			    }
			}
		}
	}
</script>