<template>
	<div>
		<h2>{{ data.messages.name }}</h2>
		<p>{{ data.messages.description }}</p>
		
		<form class="form-admin" action="" method="POST" @submit.prevent="submit">
			<h3>{{ data.messages.form_parameters }}</h3>
			<div class="form-contenu">

               <div class="row">
                    <div class="colonne3">
                        <label class="etiquette" for="user">{{ data.messages.user }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-20em" type="text" name="user" id="user" required autocomplete="off" v-model="user" />
                    </div>
                </div>
                
				<div class="row">
				    <div class="colonne3">
					   <label class="etiquette" for="client_id">{{ data.messages.client_id }}</label>
				   </div>
				   <div class="colonne_suite">
					   <input class="saisie-20em" type="text" name="client_id" id="client_id" required autocomplete="off" v-model="client_id"/>
					</div>
				</div>
	
				<div class="row">
				    <div class="colonne3">
					   <label class="etiquette" for="client_secret">{{ data.messages.client_secret }}</label>
					</div>
					<div class="colonne_suite">
					   <input class="saisie-50em" type="text" name="client_secret" id="client_secret" required autocomplete="off" v-model="client_secret" />
					</div>
				</div>
				
				<div class="row">
                    <div class="colonne3">
                        <label class="etiquette" for="curl_timeout">{{ data.messages.form_curl_timeout }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input type="number" name="curl_timeout" id="curl_timeout" v-model="curl_timeout" />
                    </div>
                </div>
	
				<div class="row">
					<button class="bouton btnCancel" type="button" @click="cancel">
						{{ messages.get("common", "cancel") }}
					</button>
					<button class="bouton" type="submit">
						{{ messages.get("common", "submit") }}
					</button>
				</div>
			</div>
		</form>
		
	</div>
</template>

<script>
	export default {
		props : ["data"],
		data: function () {
			return {
			     user : "",
			     client_id : "",
			     client_secret : "",
			     access_token : "",
			     curl_timeout : 5,
			}
		},
		created: function() {
            this.user = this.helper.cloneObject(this.data.parameters.user);
            this.client_id = this.helper.cloneObject(this.data.parameters.client_id);
            this.client_secret = this.helper.cloneObject(this.data.parameters.client_secret);
            if (this.data.parameters.curl_timeout) {
                this.curl_timeout = this.helper.cloneObject(this.data.parameters.curl_timeout);
            }
        },
		methods: {
		    cancel: function () {
		        this.$emit('cancel');
		    },
		    submit: async function () {
                let values = [{
                    "user": this.user,
                    "client_id": this.client_id,
                    "client_secret": this.client_secret,
                    "curl_timeout": this.curl_timeout,
                    }]; 
                let response = await this.ws.post(this.data.entityType, "electre/save", {values: values});
                if (response.error) {
                    if (response.errorMessage) {
                        console.error(response.errorMessage);
                    }
                    this.notif.error(this.messages.get("common", "failed_save"));
                } else {
                    this.notif.info(this.messages.get("common", "success_save"));
                }
            }
		}
	}
</script>