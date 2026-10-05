<template>
	<div id="sms_channel">
        <div class="dsi-form-group">
            <label class="etiquette" for="parameter">{{ messages.get('dsi', 'channel_sms_parameter') }}</label>
			<div class="dsi-form-group-contents">
				<span id="parameter">
					{{ messages.get('dsi', 'channel_sms_parameter_description') }}
				</span>
			</div>
		</div>
	</div>
</template>

<script>
	export default {}
</script>