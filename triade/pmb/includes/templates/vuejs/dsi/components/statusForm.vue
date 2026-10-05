<template>
	<form class="form-admin" @submit.prevent="save">
		<h3>{{ messages.get("dsi", "status") }}</h3>
		<div class='form-contenu'>
			<div class='row'>
				<div id="el0Child_0">
					<div id="el0Child_0a" class="row uk-clearfix">
						<label for="name" class='etiquette'>{{ messages.get("dsi", "status_form_name") }}</label>
					</div>							
					<div id="el0Child_0b" class="row uk-clearfix">
						<input type="text"  v-model="status.name" name="name" id="name" class='saisie-50em' required>
					</div>							
										
				</div>
				<div id="el0Child_1">
					<div id="el0Child_1a" class="row uk-clearfix">
						<label class="etiquette" for="active">{{ messages.get('dsi', 'status_form_active') }}</label>
					</div>							
					<div id="el0Child_1b" class="row uk-clearfix">
						<input type="checkbox" class="switch" name="active" id="active" v-model="status.active">
						<label for="active">&nbsp;</label>
					</div>			
				</div>
			</div>
			<div class='row'>
				<br />
				<div class="left">
				    <input @click="cancel" class="bouton" type="button" :value="messages.get('common', 'cancel')"/>
				    <input class="bouton" type="submit" :value="messages.get('common', 'submit')"/>
			    </div>
			    <template v-if="status.id != 1 && status.id != 0">
				    <div class="right">
					    <input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')"/>
				    </div>
			    </template>
			</div>
	    </div>
    </form>
</template>

<script>
	export default {
		props : ["status", "categ"],
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
		methods: {
			save : function() {
				let response = this.ws.post(this.helper.camelize(this.categ), 'save', this.status);
		        response.then((result) => {
		            if (result.error) {
		                this.notif.error(this.messages.get('dsi', result.errorMessage));
		            } else {
						document.location = `./dsi.php?categ=${this.categ}`;
		            }
		        });
			},
			del : function() {
				if (1 == this.status.idStatus) {
					alert(this.messages.get('dsi', 'warn_delete_default_status'));
					return false;
				}
				
				if (confirm(this.messages.get('dsi', 'confirm_del'))) {
				    let response = this.ws.post(this.helper.camelize(this.categ), 'delete', this.status);
			        response.then((result) => {
			            if (result.error) {
			                this.notif.error(this.messages.get('dsi', result.errorMessage));
			            } else {
							document.location = `./dsi.php?categ=${this.categ}`;
			            }
			        });
				}
			},
			cancel : function() {
				document.location = `./dsi.php?categ=${this.categ}`;
			}
		}
	}
</script>