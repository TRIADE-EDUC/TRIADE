<template>
	<span>
		<template v-for="(customValue, index) in values" class="row">
			{{ customValue.displayLabel }} 
			<a :href="customValue.value" target="_blank">
				<img id="url_link" :src="img.url_link" border="0"/>
			</a>
			<template v-if="values.length > 1 && index < values.length - 1"> / </template>
		</template>
	</span>
</template>

<script>
	export default {
		props : ["values", "img"],
	}
</script>