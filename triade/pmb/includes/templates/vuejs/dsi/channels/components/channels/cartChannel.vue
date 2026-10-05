<template>
	<div id="cart_channel">
        <div class="dsi-form-group">
            <label class="etiquette" for="parameter">{{ channelType.msg.settings_cart }}</label>
			<div class="dsi-form-group-contents">
				<span id="parameter">{{channelType.msg.setting_detail}}</span>
			</div>
		</div>
        <div class="dsi-form-group">
            <label class="etiquette" for="parameter_empty">{{ channelType.msg.setting_empty_cart }}</label>
			<div class="dsi-form-group-contents">
				<input id="parameter_empty" type="checkbox" v-model="channel.settings.emptyCart" />
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props : ['channelType', 'channel'],
		created : function() {
			if(! this.channel.settings.emptyCart) {
				this.$set(this.channel.settings, "emptyCart", false);
			}
		}
	}
</script>