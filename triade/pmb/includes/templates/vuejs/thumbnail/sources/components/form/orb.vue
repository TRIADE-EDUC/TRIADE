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
					   <label class="etiquette" for="client_id">{{ data.messages.api_key }}</label>
				   </div>
				   <div class="colonne_suite">
					   <input class="saisie-20em" type="text" name="api_key" id="api_key" required autocomplete="off" v-model="api_key"/>
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
			     api_key : "",
			     curl_timeout : 0,
			}
		},
		created: function() {
            this.user = this.helper.cloneObject(this.data.parameters.user);
            this.api_key = this.helper.cloneObject(this.data.parameters.api_key);
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
                    "api_key": this.api_key,
                    "curl_timeout": this.curl_timeout,
                }]; 
                let response = await this.ws.post(this.data.entityType, "orb/save", {values: values});
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