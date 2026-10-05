<template>
	<div id="animations_list" v-html="animations.render">
	</div>
</template>

<script>
	export default {
		props : ["animations"]
	}
</script>