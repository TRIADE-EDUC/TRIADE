<template>
	<div id="mail_channel">
        <div class="dsi-form-group">
            <label class="etiquette" for="portalLink">{{ messages.get('dsi', 'channel_portal_page') }}</label>
			<div class="dsi-form-group-content">
				<a id="portalLink" target="_blank" :href="portalLink">{{portalLink}}</a>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["channel"], 
		data: function () {
			return {
				portalLink : "#"
			}
		},
		created : async function() {
			await this.getOpacURL();
			this.channel.settings.link = this.portalLink;
		},
		methods: {
			getOpacURL : async function() {
				let response = await this.ws.get("Channels", "portal/url/"+this.$root.diffusion.id + "/0");
				if (response.error) {
					this.notif.error(this.messages.get("dsi", response.errorMessage));
				} else {
					this.portalLink = response[0];
				}
			}
		}
	}
</script>