<template>
	<span>
		<template v-if="field.options[0].autorite[0].value == 'yes'">
			<template v-for="(customValue, index) in values">
				{{ customValue.displayLabel }}
				<template v-if="values.length > 1 && index < values.length - 1"> / </template>
			</template>
		</template>
		
		<template v-else v-for="(customValue, index) in values[0].value">
			<template v-for="(tabValue, tabIndex) in field.listValues.table">
				<template v-if="tabIndex == customValue">
					{{ tabValue }}
					<template v-if="values[0].value.length > 1 && index < values[0].value.length - 1"> / </template>
				</template>
			</template>
		</template>
	</span>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>