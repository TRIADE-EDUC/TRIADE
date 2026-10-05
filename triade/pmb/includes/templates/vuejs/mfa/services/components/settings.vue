<template>
    <form @submit.prevent="submit($event)">
		<div v-for="(service, index) in services" :key="index" class="mfa-services row">
			<h2>{{ service.context }}</h2>
			<div class="form-contenu" :id="'mfa-form-services-' + index">
				<div class="mfa-service-active colonne2">
					<div class="row">
						<input class="switch"
							   type="checkbox"
							   :name="'application-active-' + index"
							   :id="'application-active-' + index"
							   v-model="service.application" />
						<label :for="'application-active-' + index">{{ messages.get("mfa", "service_totp") }}</label>
					</div>
		
					<div class="row">
						<input class="switch"
							   type="checkbox"
							   :name="'mail-active-' + index"
							   :id="'mail-active-' + index"
							   v-model="service.mail" />
						<label :for="'mail-active-' + index">{{ messages.get("mfa", "service_mail") }}</label>
					</div>
		
					<div class="row" v-show="index != 'gestion'">
						<input class="switch"
							   type="checkbox"
							   :name="'sms-active-' + index"
							   :id="'sms-active-' + index"
							   :disabled="!service.application"
							   v-model="service.sms" />
						<label :for="'sms-active-' + index">{{ messages.get("mfa", "service_sms") }}</label>
					</div>
					<br>
					<div class="row">
						<input class="switch"
							   type="checkbox"
							   :name="'required-active-' + index"
							   :id="'required-active-' + index"
							   :disabled="!service.application"
							   v-model="service.required" />
						<label :for="'required-active-' + index">{{ messages.get("mfa", "service_required") }}</label>
					</div>
				</div>
	
				<div class="mfa-service-suggest colonne2">
					<h3>{{ messages.get("mfa", "service_suggest_msg") }}</h3>
					<textarea v-model="service.suggestMessage"
						:name="'suggest_message_' + index" 
						:id="'suggest_message_' + index" 
						data-translation-fieldname="suggest_message"
						rows="7" cols="64">
					</textarea>
				</div>
				<hr v-if="isLastService(service)">
			</div>
		</div>
		<div class="row">
			<input type="submit" class="bouton" :value="messages.get('common', 'submit')">
		</div>
	</form>
</template>

<script>
    export default {
		props: ["services"],
		watch: {
			"services.gestion.application": function(newVal) {
				this.services.gestion.mail = newVal;
				if(!newVal) {
					this.services.gestion.sms = newVal
					this.services.gestion.required = newVal
				}
			},
			"services.opac.application": function(newVal) {
				this.services.opac.mail = newVal;
				if(!newVal) {
					this.services.opac.sms = newVal
					this.services.opac.required = newVal
				}
			},
			"services.gestion.mail": function(newVal) {
				this.services.gestion.application = newVal;
				if(!newVal) {
					this.services.gestion.sms = newVal
					this.services.gestion.required = newVal
				}
			},
			"services.opac.mail": function(newVal) {
				this.services.opac.application = newVal;
				if(!newVal) {
					this.services.opac.sms = newVal
					this.services.opac.required = newVal
				}
			}
		},
		methods: {
			isLastService: function(service) {
				return Object.values(this.services)[Object.values(this.services).length-1].context != service.context;
			},
			submit: async function(e) {
				let response = await this.ws.post('MFAServices', 'save', this.formattedFormData(e));
				this.notif.info(this.messages.get('common', 'success_save'));
			},
			formattedFormData: function(e) {
				const formData = new FormData(e.target);

				let object = {};
				formData.forEach((value, key) => object[key] = value);

				return { objects: this.services, formData: object };
			}
		}
	}
</script>