<template>
    <div class="mfa-init" @keydown.enter="initialize">
		<div class="row">
			<label>
				{{ pmb.getMessage('mfa', 'mfa_secret_code') }}
				<b>{{ empr.temp_secret_code }}</b>
			</label>
		</div>
		<div class="row">
			<label>
				{{ pmb.getMessage('mfa', 'mfa_base32_secret_code') }}
				<b>{{ empr.temp_base32_secret_code }}</b>
			</label>
		</div>
		<div class="row">&nbsp;</div>
		<div class="row">
			<div class="colonne3">
				<img :src="empr.url_qr_code">
			</div>
			<div class="colonne3">
				<span v-html="suggest"></span>
			</div>
		</div>
		<div class="row">&nbsp;</div>
		<div class="row">
			<div class="colonne3">
				<label for="mfa-otp-code">
					{{ pmb.getMessage('mfa', 'mfa_confirm_code') }}
				</label>
			</div>
			<div class="colonne2">
				<input type="text" id="mfa-otp-code" name="mfa-otp-code" v-model="inputCode" />
				<div>
					<input v-if="empr.empr_mail != ''"
						id="btn_send_mail"
						type="button"
						class="bouton"
						:value="pmb.getMessage('mfa', 'mfa_send_mail')"
						@click="sendMail($event)" />

					<input v-if="empr.empr_tel1 != '' && empr.empr_sms == 1 && sms_activate"
						id="btn_send_sms"
						type="button"
						class="bouton"
						:value="pmb.getMessage('mfa', 'mfa_send_sms')"
						@click="sendSMS($event)" />
				</div>
			</div>
		</div>
		<div class="row">&nbsp;</div>
		<div class="row">
			<input type="button"
				   class="bouton"
				   :value="pmb.getMessage('mfa', 'mfa_validate_button')"
				   @click="initialize" />
		</div>
	</div>
</template>

<script>
    export default {
		props: ["empr", "suggest", "sms_activate"],
		data : function() {
			return  {
				inputCode: "",
				mfa_counters: { mail: 30, sms: 30 },
				mfa_default_values: { mail: "", sms: "" }
			}
		},
		methods: {
			initialize: async function() {
				let req = new http_request();
				req.request('./ajax.php?module=ajax&categ=authentication&sub=initialization', 1,
							'&secret_code=' + encodeURIComponent(this.empr.temp_base32_secret_code) + 
							'&code=' + encodeURIComponent(this.inputCode));

				if(req.get_text() == 1) {
					this.notif.info(this.pmb.getMessage('mfa', 'mfa_initialization_success'));

					this.$set(this.empr, "mfa_secret_code", this.empr.temp_secret_code);
					this.$set(this.empr, "mfa_favorite", "app");

					this.$parent.init = false;
				} else {
					this.notif.error(this.pmb.getMessage('mfa', 'mfa_code_error'));
				}
			},
			mfa_counter: function(type) {
				let mfa_btn = document.getElementById('btn_send_' + type);
				
				if(mfa_btn) {
					if(this.mfa_counters[type]) {
						mfa_btn.value = this.mfa_default_values[type] + '(' + this.mfa_counters[type] + ')'
						this.mfa_counters[type] = this.mfa_counters[type] - 1;
				
						setTimeout(() => {
							this.mfa_counter(type);
						}, 1000);
				
					} else {
						this.mfa_counters[type] = 30;
				
						mfa_btn.value = this.mfa_default_values[type];
						mfa_btn.disabled = false;
					}
				}
			},
			sendMail: async function(e) {
				let req = new http_request();
				req.request('./ajax.php?module=ajax&categ=authentication&sub=send_mail', 1,
							'&secret_code=' + encodeURIComponent(this.empr.temp_secret_code));
				
				if(req.get_text() == 1) {
					this.notif.info(this.pmb.getMessage('mfa', 'mfa_success_mail'));

					this.mfa_default_values["mail"] = e.target.value;
					e.target.disabled = true;
                    this.mfa_counter("mail");

				} else {
					this.notif.error(this.pmb.getMessage('mfa', 'mfa_error_mail'));
				}
			},
			sendSMS: async function(e) {
				let req = new http_request();
				req.request('./ajax.php?module=ajax&categ=authentication&sub=send_sms', 1,
							'&secret_code=' + encodeURIComponent(this.empr.temp_secret_code));
				
				if(req.get_text() == 1) {
					this.notif.info(this.pmb.getMessage('mfa', 'mfa_success_sms'));

					this.mfa_default_values["sms"] = e.target.value;
					e.target.disabled = true;
                    this.mfa_counter("sms");
					
				} else {
					this.notif.error(this.pmb.getMessage('mfa', 'mfa_error_sms'));
				}
			}
		}
	}
</script>