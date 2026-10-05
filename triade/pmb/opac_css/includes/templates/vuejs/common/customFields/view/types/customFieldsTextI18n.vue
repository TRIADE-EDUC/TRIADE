<template>
	<span>
		<template v-for="(customValue, index) in values">
			{{ field.msg.langSelect }} : {{customValue.displayLang}}  {{customValue.value}}
			<template v-if="values.length > 1 && index < values.length - 1"> / </template>
		</template>
	</span>
</template>

<script>
	export default {
		props : ["field", "values"],
	}
</script>