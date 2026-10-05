<template>
	<form class="form-admin" onsubmit="return false;">
		<h3 v-if="'edit' == action">{{ pmb.getMessage("animation", "admin_edit_status") }}</h3>
		<h3 v-else>{{ pmb.getMessage("animation", "admin_add_status") }}</h3>
		<div class='form-contenu'>
			<!-- Informations générales -->
			<div class='row'>
				<div id="el0Child_0">
					<div id="el0Child_0a" class="row uk-clearfix">
						<label :for="status.label" class='etiquette'>{{ pmb.getMessage("animation", "admin_type_label") }} :</label>
					</div>							
					<div id="el0Child_0b" class="row uk-clearfix">
						<input type="text"  v-model="status.label" class='saisie-50em' required>
					</div>							
										
				</div>
				<div id="el0Child_1">
					<div id="el0Child_1a" class="row uk-clearfix">
						<label :for="status.label" class='etiquette'>{{ pmb.getMessage("animation", "admin_status_color") }} :</label>
					</div>							
					<div id="el0Child_1b" class="row uk-clearfix">
						<input type="color"  v-model="status.color" class='saisie-10em' required> <span v-if="status.color">{{ pmb.getMessage("animation", "admin_color") }} : {{ status.color }}</span>
					</div>			
				</div>
			</div>
			<div class='row'>
				<br />
				<div class="left">
				    <input  @click="cancel" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_cancel')"/>
				    <input  @click="checkStatusExist" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_save')"/>
			    </div>
			    <template v-if="'edit' == action && status.id != 1">
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
		
		props : ["pmb","status", "action", "img"],
		components : {
			customfields
		},
		data: function () {
			return {
				backupStatus: {}   
			}
		},
		created: function () {
		    var newObject = new Object();
		    for (let index in this.status) {
	            newObject[index] = this.status[index];
		    }
		    this.backupStatus = newObject;
		},
		methods:{
			save : function() {
				var data = new FormData();
				data.append('data', JSON.stringify(this.status));
				
				var url = "./ajax.php?module=admin&categ=animations&sub=status&action=save";
				fetch(url,{
					method: 'POST',
					body: data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=animations&sub=status&action=list';
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}) 
				.catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			del : function() {
				if(1 == this.status.idStatus){
					alert(this.pmb.getMessage('animation', 'admin_animation_no_delete_status'));
					return false;
				}
				if(true == this.status.hasAnimations){
					alert(this.pmb.getMessage('animation', 'admin_animation_no_delete_animations_exists'));
					return false;				
				}
				var resultat = window.confirm(this.pmb.getMessage('animation', 'admin_animation_confirm_del_status'));
				if (resultat == 0) {
					event.preventDefault();
				} else {
					var data = new FormData();
					
					data.append('data', JSON.stringify({id:this.status.id}));
					
					var url = './ajax.php?module=admin&categ=animations&sub=status&action=delete'; 

					fetch(url,{
					method: 'POST',
					body: data,
						}).then(function(response) {
						if (response.ok) {
							document.location = './admin.php?categ=animations&sub=status&action=list';
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
				document.location = './admin.php?categ=animations&sub=status&action=list';
			},
			checkStatusExist : function() {
			    
			    if (this.status.id && this.status.id != 0 && this.backupStatus.label === this.status.label) {
					this.save();
			    } else {
					let data = new FormData();
					data.append('data', JSON.stringify({label : this.status.label}));
					let url = './ajax.php?module=admin&categ=animations&sub=status&action=check';
					
					fetch(url, {
						method : 'POST',
						body : data,
					}).then((response) => {
						if (response.ok) {
							response.text().then((flag) => {
								if (flag) {
									alert(this.pmb.getMessage('animation', 'animation_statut_already_used_alert'));
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