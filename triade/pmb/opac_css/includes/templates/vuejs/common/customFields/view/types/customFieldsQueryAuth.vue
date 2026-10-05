<template>
	<span>
        <template v-for="(customValue, index) in values">
			<a :href="'./autorites.php?categ=see&sub=' + field.authInfos.sub + '&id=' + customValue.value">{{ customValue.displayLabel }}</a>
			<template v-if="values.length > 1 && index < values.length - 1"> / </template>
		</template>
	</span>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>