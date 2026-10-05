<template>
	<div>
		<form class="form-admin">
			<h3>
				<template v-if="'edit' == action">
					{{ pmb.getMessage("animation", "admin_animations_priceTypes_edit") }}
				</template>
				<template v-else>
					{{ pmb.getMessage("animation", "admin_animations_priceTypes_add") }}
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
							<label :for="pricetypes.label" class='etiquette'>{{ pmb.getMessage("animation", "admin_type_label") }} :</label>
						</div>							
						<div id="el0Child_0b" class="row uk-clearfix">
							<input :id="pricetypes.label" type="text"  v-model="pricetypes.name" class='saisie-50em' required>
						</div>							
						<div id="el0Child_0c" class="row uk-clearfix">
							<label :for="pricetypes.prix" class='etiquette'>{{ pmb.getMessage("animation", "admin_type_price") }} :</label>
						</div>							
						<div id="el0Child_0d" class="row uk-clearfix">
							<input :id="pricetypes.prix" type="number" step="0.05" min="0" v-model.number="pricetypes.defaultValue" class='saisie-20em'>
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
						    <input @click="checkPriceTypeUse" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_type_delete')"/>
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
		
		props : ["pmb","pricetypes", "action", "img"],
		components : {
			customfields
		},
		methods:{
			save : function() {
				var data = new FormData();
				data.append('data', JSON.stringify(this.pricetypes));
				
				var url = "./ajax.php?module=admin&categ=animations&sub=priceTypes&action=save";
				
				fetch(url, {
					method : 'POST',
					body : data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=animations&sub=priceTypes&action=list';
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}).catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			
			delType : function() {
				if (1 == this.pricetypes.id) {
					alert(this.pmb.getMessage('animation', 'admin_animation_no_delete_price_type'));
					return false;
				}
				var resultat = window.confirm(this.pmb.getMessage('animation', 'admin_animation_confirm_del_priceType'));
				if (resultat == 0) {
					event.preventDefault();
				} else {
					var data = new FormData();
					data.append('data', JSON.stringify({id:this.pricetypes.id}));
					
					var url = './ajax.php?module=admin&categ=animations&sub=priceTypes&action=delete'; 

					fetch(url, {
						method : 'POST',
						body : data,
					}).then(function(response) {
						if (response.ok) {
							document.location = './admin.php?categ=animations&sub=priceTypes&action=list';
						} else {
							console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
						}
					}).catch(function(error) {
						console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
					});
				}
			},
			
			cancel : function() {
				document.location = './admin.php?categ=animations&sub=priceTypes&action=list';
			},
			
			checkPriceTypeUse : function() {
				let data = new FormData();
				data.append('data', JSON.stringify({id : this.pricetypes.id}));
				let url = './ajax.php?module=admin&categ=animations&sub=priceTypes&action=check';
				
				fetch(url, {
					method : 'POST',
					body : data,
				}).then((response) => {
					if (response.ok) {
						response.text().then((flag) => {
							if (flag) {
								alert(this.pmb.getMessage('animation', 'animation_price_type_used_alert'));
							} else {
								this.delType();
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
</script>