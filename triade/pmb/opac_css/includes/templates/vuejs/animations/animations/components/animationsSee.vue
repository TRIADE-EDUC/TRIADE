<template>
	<div id="animation_see" v-html="animation.render">
	</div>
</template>

<script>
	export default {
		props : ["animation"]
	}
</script>