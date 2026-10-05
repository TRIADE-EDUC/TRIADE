<template>
    <div>
        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="provider_url">{{ data.messages["provider_url"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="provider_url" id="provider_url" v-model="modelParams.provider_url"/>
                <button class="bouton btnCancel" type="button" @click="clear('provider_url')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="client_id">{{ data.messages["client_id"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="client_id" id="client_id" v-model="modelParams.client_id"/>
                <button class="bouton btnCancel" type="button" @click="clear('client_id')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="client_secret">{{ data.messages["client_secret"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="client_secret" id="client_secret" v-model="modelParams.client_secret"/>
                <button class="bouton btnCancel" type="button" @click="clear('client_secret')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="authorization_endpoint">{{ data.messages["authorization_endpoint"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="authorization_endpoint" id="authorization_endpoint" v-model="modelParams.authorization_endpoint"/>
                <button class="bouton btnCancel" type="button" @click="clear('authorization_endpoint')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="token_endpoint">{{ data.messages["token_endpoint"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="token_endpoint" id="token_endpoint" v-model="modelParams.token_endpoint"/>
                <button class="bouton btnCancel" type="button" @click="clear('token_endpoint')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="logout_endpoint">{{ data.messages["logout_endpoint"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="logout_endpoint" id="logout_endpoint" v-model="modelParams.logout_endpoint"/>
                <button class="bouton btnCancel" type="button" @click="clear('logout_endpoint')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="userinfo_endpoint">{{ data.messages["userinfo_endpoint"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="userinfo_endpoint" id="userinfo_endpoint" v-model="modelParams.userinfo_endpoint"/>
                <button class="bouton btnCancel" type="button" @click="clear('userinfo_endpoint')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="redirect_uri">{{ data.messages["redirect_uri"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="redirect_uri" id="redirect_uri" v-model="modelParams.redirect_uri"/>
                <button class="bouton btnCancel" type="button" @click="clear('redirect_uri')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="redirect_uri_relay">{{ data.messages["redirect_uri_relay"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="redirect_uri_relay" id="redirect_uri_relay" v-model="modelParams.redirect_uri_relay"/>
                <button class="bouton btnCancel" type="button" @click="clear('redirect_uri_relay')">X</button>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="logout_redirect_uri">{{ data.messages["logout_redirect_uri"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="logout_redirect_uri" id="logout_redirect_uri" v-model="modelParams.logout_redirect_uri"/>
                <button class="bouton btnCancel" type="button" @click="clear('logout_redirect_uri')">X</button>
            </div>
        </div>
        
        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="logout_redirect_uri_relay">{{ data.messages["logout_redirect_uri_relay"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="logout_redirect_uri_relay" id="logout_redirect_uri_relay" v-model="modelParams.logout_redirect_uri_relay"/>
                <button class="bouton btnCancel" type="button" @click="clear('logout_redirect_uri_relay')">X</button>
            </div>
        </div>
        
        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="check_signatures">{{ data.messages["check_signatures"] }}</label>
            </div>
            <div class="colonne_suite">
                <input type="checkbox" name="check_signatures" id="check_signatures" v-model="modelParams.check_signatures" checked/>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="authentication_method">{{ data.messages["authentication_method"] }}</label>
            </div>
            <div class="colonne_suite">
                <select v-model="modelParams.authentication_method">
                    <option value="">{{ data.messages["choose"] }}</option>
                    <option value="client_secret_basic">{{ data.messages["client_secret_basic"] }}</option>
                    <option value="client_secret_jwt">{{ data.messages["client_secret_jwt"] }}</option>
                    <option value="private_key_jwt">{{ data.messages["private_key_jwt"] }}</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="colonne3">
                <label class="etiquette" for="scopes">{{ data.messages["scopes"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="scopes" id="scopes" v-model="modelParams.scopes"/>
                <button class="bouton btnCancel" type="button" @click="clear('scopes')">X</button>
            </div>
        </div>

        <!-- Claims utilise pour l'authentification -->
        <div class="row">
            <div class="colonne3">
               <label class="etiquette" for="claims">{{ data.messages["claims"] }}</label>
            </div>
            <div class="colonne_suite">
                <input class="saisie-40em" type="text" name="claims" id="claims" v-model="modelParams.claims"/>
                <button class="bouton btnCancel" type="button" @click.prevent="clear('claims')">X</button>
            </div>
        </div>

    </div>
</template>

<script>
export default {
        props : ["data"],
        data: function () {
            return {
            	modelParams: {}
            }
        },

        created: function() {
        	if(this.data.model) {
	            // Recuperation des parametres du model
	            let settings = JSON.parse(this.data.model.settings);
	
	            // On les assigne
	            this.$set(this, "modelParams", this.helper.cloneObject(settings.params));
        	}
        },

        methods: {
	        clear: function(param) {
	        	let input = document.getElementById(param);
	        	input.value = "";
	        }
        }
    }
</script>
