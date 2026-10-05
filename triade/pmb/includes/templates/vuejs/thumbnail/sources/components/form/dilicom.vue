<template>
    <div>
        <h2>{{ data.messages.name }}</h2>
        <p>{{ data.messages.description }}</p>

        <form class="form-admin" action="" method="POST" @submit.prevent="submit">
            <h3>{{ data.messages.form_parameters }}</h3>
            <div class="form-contenu">

               <div class="row">
                    <div class="colonne3">
                       <label class="etiquette" for="client_gln">{{ data.messages.client_gln }}</label>
                   </div>
                   <div class="colonne_suite">
                       <input class="saisie-20em" type="text" name="client_gln" id="client_gln" required autocomplete="off" v-model="client_gln"/>
                    </div>
                </div>
                
                <div class="row">
                    <div class="colonne3">
                        <label class="etiquette" for="client_key">{{ data.messages.client_key }}</label>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-20em" type="text" name="client_key" id="client_key" required autocomplete="off" v-model="client_key" />
                    </div>
                </div>

                <div class="row">
                    <div class="colonne3">
                       <label class="etiquette" for="server_url">{{ data.messages.server_url }}</label>
                    </div>
                    <div class="colonne_suite">
                       <input class="saisie-50em" type="text" name="server_url" id="server_url" required v-model="server_url" />
                    </div>
                </div>
    
                <div class="row">
                    <div class="colonne3">
                       <label class="etiquette" for="api_key">{{ data.messages.api_key }}</label>
                    </div>
                    <div class="colonne_suite">
                       <input class="saisie-50em" type="text" name="api_key" id="api_key" required autocomplete="off" v-model="api_key" />
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
                 client_gln : "",
                 client_key : "",
                 server_url : "",
                 api_key : "",
                 curl_timeout : 5,
            }
        },
        created: function() {
            this.client_gln = this.helper.cloneObject(this.data.parameters[0].client_gln);
            this.client_key = this.helper.cloneObject(this.data.parameters[0].client_key);
            this.server_url = this.helper.cloneObject(this.data.parameters[0].server_url);
            this.api_key = this.helper.cloneObject(this.data.parameters[0].api_key);
            if (this.data.parameters[0].curl_timeout) {
                this.curl_timeout = this.helper.cloneObject(this.data.parameters[0].curl_timeout);
            }
        },
        methods: {
            cancel: function () {
                this.$emit('cancel');
            },
            submit: async function () {
                let values = [{
                    "client_gln": this.client_gln,
                    "client_key": this.client_key,
                    "server_url": this.server_url,
                    "api_key": this.api_key,
                    "curl_timeout": this.curl_timeout,
                    }]; 
                let response = await this.ws.post(this.data.entityType, "dilicom/save", {values: values});
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