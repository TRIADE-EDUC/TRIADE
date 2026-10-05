<template>
	<form class="form-admin" onsubmit="return false;">
		<h3 v-if="'edit' == action">{{ pmb.getMessage("animation", "admin_edit_calendar") }}</h3>
		<h3 v-else>{{ pmb.getMessage("animation", "admin_add_calendar") }}</h3>
		<div class='form-contenu'>
			<!-- Informations générales -->
			<div class='row'>
				<div id="el0Child_0">
					<div id="el0Child_0a" class="row uk-clearfix">
						<label :for="calendar.name" class='etiquette'>{{ pmb.getMessage("animation", "admin_calendar_name") }} :</label>
					</div>							
					<div id="el0Child_0b" class="row uk-clearfix">
						<input type="text"  v-model="calendar.name" class='saisie-50em' required>
					</div>							
				</div>
				<div id="el0Child_1">
					<div id="el0Child_1a" class="row uk-clearfix">
						<label :for="calendar.name" class='etiquette'>{{ pmb.getMessage("animation", "admin_calendar_color") }} :</label>
					</div>							
					<div id="el0Child_1b" class="row uk-clearfix">
						<input type="color"  v-model="calendar.color" class='saisie-10em' required> 
						<span v-if="calendar.color">{{ pmb.getMessage("animation", "admin_color") }} : {{ calendar.color }}</span>
					</div>			
				</div>
			</div>
			<div class='row'>
				<br />
				<div class="left">
				    <input  @click="cancel" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_calendar_cancel')"/>
				    <input  @click="checkCalendarExist" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_calendar_save')"/>
			    </div>
			    <template v-if="'edit' == action && calendar.id != 1">
				    <div class="right">
					    <input @click="del" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_calendar_delete')"/>
				    </div>
			    </template>
			</div>
	    </div>
    </form>
</template>

<script>
	import customfields from "../../../common/customFields/form/customFields.vue";

	export default {
		
		props : ["pmb","calendar", "action", "img"],
		components : {
			customfields
		},
		data: function () {
			return {
				backupcalendar: {}   
			}
		},
		created: function () {
		    var newObject = new Object();
		    for (let index in this.calendar) {
	            newObject[index] = this.calendar[index];
		    }
		    this.backupcalendar = newObject;
		},
		methods:{
			save : function() {
				var data = new FormData();
				data.append('data', JSON.stringify(this.calendar));
				
				var url = "./ajax.php?module=admin&categ=animations&sub=calendar&action=save";
				fetch(url,{
					method: 'POST',
					body: data
				}).then(function(response) {
					if (response.ok) {
						document.location = './admin.php?categ=animations&sub=calendar&action=list';
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}) 
				.catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				});
			},
			del : function() {
				if(1 == this.calendar.idCalendar){
					alert(this.pmb.getMessage('animation', 'admin_animation_no_delete_status'));
					return false;
				}
				if(true == this.calendar.hasAnimations){
					alert(this.pmb.getMessage('animation', 'admin_animation_no_delete_animations_exists'));
					return false;				
				}
				var resultat = window.confirm(this.pmb.getMessage('animation', 'admin_animation_confirm_del_calendar'));
				if (resultat == 0) {
					event.preventDefault();
				} else {
					var data = new FormData();
					
					data.append('data', JSON.stringify({id:this.calendar.id}));
					
					var url = './ajax.php?module=admin&categ=animations&sub=calendar&action=delete'; 

					fetch(url,{
					method: 'POST',
					body: data,
						}).then(function(response) {
						if (response.ok) {
							document.location = './admin.php?categ=animations&sub=calendar&action=list';
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
				document.location = './admin.php?categ=animations&sub=calendar&action=list';
			},
			checkCalendarExist : function() {
			    
			    if (this.calendar.id && this.calendar.id != 0 && this.backupcalendar.name === this.calendar.name) {
					this.save();
			    } else {
					let data = new FormData();
					data.append('data', JSON.stringify({name : this.calendar.name}));
					let url = './ajax.php?module=admin&categ=animations&sub=calendar&action=check';
					
					fetch(url, {
						method : 'POST',
						body : data,
					}).then((response) => {
						if (response.ok) {
							response.text().then((flag) => {
								if (flag) {
									alert(this.pmb.getMessage('animation', 'animation_calendar_already_used_alert'));
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