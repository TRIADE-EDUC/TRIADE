<template>
	<div id="mail_channel">
        <div class="dsi-form-group">
            <label class="etiquette" for="link">{{ messages.get('dsi', 'channel_page_link') }}</label>
			<div class="dsi-form-group-content">
				<a id="link" target="_blank" :href="link">{{ link }}</a>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["channel"],
		mounted : async function() {
			this.channel.settings.link = this.link;
		},
		computed: {
			link: function() {
				if (this.$root.diffusion.id) {
					return `${this.$root.opac_url_base}export.php?lvl=dsi&id=${this.$root.diffusion.id}`;
				}
				return "#";
			}
		}
	}
</script>