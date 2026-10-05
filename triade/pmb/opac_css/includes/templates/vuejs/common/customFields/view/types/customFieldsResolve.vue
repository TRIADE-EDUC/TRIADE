<template>
	<span>
		<template v-for="(value, index) in values">
			<template v-for="(option, indexoption) in field.options[0].resolve">
				<template v-if="value.selectValue == option.id">
					{{ option.label }} : {{ value.value }}
					<a :href="option.value + value.value" target="_blank">
						<img id="resolve_link" :src="img.url_link" />
					</a>
					<template v-if="values.length > 1 && index < values.length - 1"> / </template>
				</template>
			</template>
		</template>
	</span>
</template>

<script>
	export default {
		props : ["field", "values", "img"],
	}
</script>