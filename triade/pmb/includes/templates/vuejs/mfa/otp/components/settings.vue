<template>
    <div>
		<form id="mfa-form-otp" @submit.prevent="submit($event)">
			<div v-for="(setting, index) in settings" :key="index" class="mfa-otp row">
				<h2>{{ setting.context }}</h2>
				<div class="form-contenu" :id="'mfa-form-otp-' + index">
					<div class="row mfa-otp-row">
						<div class="mfa-otp-label colonne4">
							<label :for="'method_' + index">{{ messages.get("mfa", "otp_method") }} :</label>
						</div>
						<div class="mfa-otp-text colonne_suite">
							<select :name="'method_' + index" v-model="setting.hashMethod">
								<option v-for="method in methods" :value="method">
									{{ messages.get("mfa", "otp_method_" + method) }}
								</option>
							</select>
						</div>
					</div>
					<div class="row mfa-otp-row">
						<div class="mfa-otp-label colonne4">
							<label :for="'lifetime_' + index">{{ messages.get("mfa", "otp_lifetime") }} :</label>
						</div>
						<div class="mfa-otp-text colonne_suite">
							<input type="number" :name="'lifetime_' + index" v-model="setting.lifetime" />
						</div>
					</div>
					<div class="row mfa-otp-row">
						<div class="mfa-otp-label colonne4">
							<label :for="'length_code_' + index">{{ messages.get("mfa", "otp_length_code") }} :</label>
						</div>
						<div class="mfa-otp-text colonne_suite">
							<input type="number" :name="'length_code_' + index" v-model="setting.lengthCode" />
						</div>
					</div>
				</div>
				<hr v-if="isLastSetting(setting)">
			</div>
			<div class="row">
				<input type="submit" class="bouton" :value="messages.get('common', 'submit')">
			</div>
		</form>
	</div>
</template>

<script>
    export default {
		props: ["settings", "methods"],
		methods: {
			isLastSetting: function(setting) {
				return Object.values(this.settings)[Object.values(this.settings).length-1].context != setting.context;
			},
			submit: async function(e) {
				let response = await this.ws.post('MFAOtp', 'save', { objects: this.settings });
				this.notif.info(this.messages.get('common', 'success_save'));
			}
		}
	}
</script>