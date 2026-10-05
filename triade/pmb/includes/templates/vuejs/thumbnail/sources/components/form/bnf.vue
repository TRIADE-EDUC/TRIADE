<template>
	<div>
		<h2>{{ data.messages.name }}</h2>
		<p>{{ data.messages.description }}</p>
		
		<form class="form-admin" action="" method="POST" @submit.prevent="submit">
			<h3>{{ data.messages.form_parameters }}</h3>
			<div class="form-contenu">

               <div class="row">
                    <div class="colonne3">
                        <label class="etiquette" for="curl_timeout">{{ data.messages.form_curl_timeout }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input type="number" name="curl_timeout" id="curl_timeout" v-model="curl_timeout" />
                    </div>
                </div>
               <div class="row">
                    <div class="colonne3">
                        <label class="etiquette" for="using_default_img">{{ data.messages.using_bnf_default_image }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input type="checkbox" name="using_default_img" id="using_default_img" v-model="using_default_img" />
                    </div>
                </div>
                
               <div class="row">
	               <img src="https://catalogue.bnf.fr/couverture?&appName=NE&idArk=&couverture=1" alt="bnf_default_thumbnail"/>
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
				using_default_img : 0,
				curl_timeout : 5,
			}
		},
		created: function() {
            this.using_default_img = this.helper.cloneObject(this.data.parameters.using_default_img);
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
                    "using_default_img": this.using_default_img,
                    "curl_timeout": this.curl_timeout,
                }]; 
                let response = await this.ws.post(this.data.entityType, "bnf/save", {values: values});
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