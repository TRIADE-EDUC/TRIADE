<template>
	<div id="mail_channel">
		<fieldset class="dsi-fieldset-setting dsi-form-channel-dynamic">
			<legend class="dsi-legend-setting">{{ messages.get('dsi', 'channel_form_settings') }}</legend>

			<div class="dsi-form-group">
				<label class="etiquette" for="MailAdrList">{{ messages.get('dsi', 'channel_form_mail_adr') }}</label>
				<div class="dsi-form-group-content">
					<select id="MailAdrList" name="MailAdrList" v-model="channel.settings.mail_selected" required>
						<option value="" disabled>{{ messages.get('dsi', 'channel_form_default_mail_adr') }}</option>
						<option v-for="(mail, index) in mailList" :key="index" :value="index">{{ mail }}</option>
					</select>
				</div>
			</div>

			<div class="dsi-form-group">
				<label class="etiquette" for="mail_obj">{{ messages.get('dsi', 'channel_form_mail_obj') }}</label>
				<div class="dsi-form-group-content">
					<textarea class="resize-horizontal" id="mail_obj" name="mail_obj" v-model="channel.settings.mail_object" required></textarea>
				</div>
			</div>

			<div class="dsi-form-group">
				<div class="dsi-form-group-content">
					<label class="etiquette" for="mail_simple_choice">{{ messages.get('dsi', 'channel_form_mail_simple_choice') }}</label>
					<input type="radio" id="mail_simple_choice" name="mail_choice" value="mail_simple" v-model="channel.settings.mail_choice" required>
				</div>
				<div class="dsi-form-group-content">
					<label class="etiquette" for="mail_attachments_choice">{{ messages.get('dsi', 'channel_form_mail_attachments_choice') }}</label>
					<input type="radio" id="mail_attachments_choice" name="mail_choice" value="mail_attachments" v-model="channel.settings.mail_choice" required>
				</div>
			</div>
			
			<!-- <template v-if="mailChoice === 'mail_attachments'">
				<div class="dsi-form-group">
					<div class="dsi-form-group-content">
						<label class="etiquette" for="mail_attachments">{{ messages.get('dsi', 'channel_form_mail_attachments_label') }}</label>
						<input type="file" id="mail_attachments" name="mail_attachments" @change="loadAttachments($event)" multiple required>
					</div>
				</div>
				<div class="dsi-form-group">
					<pre class="dsi-file-list" id="attachments" style="display: none;"></pre>
				</div>
			</template> -->
		</fieldset>
	</div>
</template>

<script>
	export default {
		props : ["channel"], 
		data: function () {
			return {
                mailList: []
			}
		},
        created: function() {
			if(Object.keys(this.channel.settings).length === 0) {
				this.$set(this.channel, "settings", {
					mail_selected: "",
					mail_object: "",
					mail_choice: ""
				});
            }

            this.mailList = this.getSourceList();
        },
		methods: {
			// displayAttachments: function() {
			// 	const input_attachments = document.getElementById("mail_attachments");
			// 	const attachments = document.getElementById("attachments");

			// 	if(input_attachments && input_attachments.files.length > 0) {
			// 		attachments.style.display = "block";
			// 		return;
			// 	}
			// 	attachments.style.display = "none";
			// },
			// loadAttachments: function(e) {
			// 	if(!this.checkFilesSize(e.target.files)) {
			// 		this.notif.error(this.messages.get("dsi", "channel_form_mail_attachments_max_size"));
			// 		e.target.value = "";
			// 		return;
			// 	}

			// 	const attachments = document.getElementById("attachments");
			// 	attachments.innerText = "";

			// 	for (const file of e.target.files) {
			// 		attachments.innerText += (e.target.files[0] !== file ? "\n" : "") + `${file.name}`;
			// 	}
			// 	this.displayAttachments();
			// },
			// checkFilesSize: function(filesList) {
			// 	const files = Array.from(filesList);
			// 	const maximumSize = 25 * 1024 * 1024; // 25 Mo

			// 	for(const file of files) {
			// 		if(file.size >= maximumSize) {
			// 			return false;
			// 		}
			// 	} 
			// 	return true;
			// },
			getSourceList: async function() {
				if(true) {
					let response = await this.ws.get("Channels", "getMailList");
					if (response.error) {
						this.notif.error(this.messages.get("dsi", response.errorMessage));
					} else {
						this.mailList = response;
					}
				}
			}
		}
	}
</script>