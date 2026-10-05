<template>
	<div id="dsi-form-diffusion-channel">
        <formChannel 
			:channel="diffusion.channel" 
			:channel_type_list="channelTypeList" 
			:is_model="false" 
			@saveDiffusionChannel="saveDiffusionChannel">
		</formChannel>
	</div>
</template>

<script>
    import formChannel from "@dsi/channels/components/formChannel.vue";
	export default {
		props : ["diffusion"],
        components: {
            formChannel
        },
		data: function () {
			return {
			    channelTypeList: [],
			}
		},
		created: function() {
			this.fetchData();
		},
		methods: {
			fetchData: async function() {
				if (!this.$root.types?.channels) {
					this.channelTypeList = await this.ws.get('channels', 'getTypeListAjax');
					return;
				}

				this.channelTypeList = JSON.parse(JSON.stringify(this.$root.types.channels));
			},
			saveDiffusionChannel: async function(idChannel) {
                this.diffusion.numChannel = idChannel;
				let response = await this.ws.post('diffusions', 'save', this.diffusion);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				}else {
					this.notif.info(this.messages.get('common', 'success_save'));
				}
			}
		},
	}
</script>