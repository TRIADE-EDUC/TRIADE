<template>
    <div class="mfa-after-init">
		<div class="row">
			<label style="padding: 0;">
				{{ pmb.getMessage('mfa', 'mfa_secret_code') }}
				<b>{{ pmb.getMessage('mfa', 'mfa_already_init') }}</b>
			</label>
		</div>
		<div class="row">
			<input type="button"
				   class="bouton"
				   :value="pmb.getMessage('mfa', 'mfa_reset')"
				   @click="reset" />
		</div>
		<div class="row">&nbsp;</div>
		<div class="row">
			<div class="colonne5">
				<label style="padding: 0;" for="mfa-favorite-select">{{ pmb.getMessage('mfa', 'mfa_favorite') }}</label>
			</div>
			<div class="colonne2">
				<select name="mfa-favorite-select" id="mfa-favorite" style="margin: 0;" v-model="empr.mfa_favorite">
					<option value="app">{{ pmb.getMessage('mfa', 'mfa_favorite_app') }}</option>
					<option value="mail">{{ pmb.getMessage('mfa', 'mfa_favorite_mail') }}</option>
					<option v-if="sms_activate" value="sms">{{ pmb.getMessage('mfa', 'mfa_favorite_sms') }}</option>
				</select>
			</div>
		</div>
		<div class="row">
			<input type="button"
				   class="bouton"
				   :value="pmb.getMessage('mfa', 'mfa_favorite_save')"
				   @click="save" />
		</div>
	</div>
</template>

<script>
    export default {
		props: ["empr", "sms_activate"],
		methods: {
			reset: async function() {
				if(confirm(this.pmb.getMessage('mfa', 'mfa_reset_confirm'))) {
					let req = new http_request();
					req.request('./ajax.php?module=ajax&categ=authentication&sub=reset', 1);
	
					if(req.get_text() == 1) {
						window.location.href = "./empr.php?tab=mfa&lvl=mfa_initialization";
					}
				}
			},
			save: async function() {
				let req = new http_request();
				req.request('./ajax.php?module=ajax&categ=authentication&sub=save_favorite', 1,
							'&favorite=' + encodeURIComponent(this.empr.mfa_favorite));

				if(req.get_text() == 1) {
					this.notif.info(this.pmb.getMessage('common', 'success_save'));
				} else {
					this.notif.error(this.pmb.getMessage('common', 'failed_save'));
				}
			}
		}
	}
</script>