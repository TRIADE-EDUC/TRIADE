<template>
	<span>
		<!-- sélecteur d'autorité -->
		<template v-if="field.options[0].autorite[0].value == 'yes'">
			<template v-for="(customValue, index) in values">
				{{ customValue.displayLabel }}
				<template v-if="values.length > 1 && index < values.length - 1"> / </template>
			</template>
		</template>
		
		<!-- choix multiple -->
		<template v-else-if="field.options[0].multiple[0].value == 'yes'">
			<template v-for="(customValue, valuesIndex) in values[0].value">
				<template v-for="(label, index) in field.listValues">
					<template v-if="customValue == index">
						{{ label }}
						<template v-if="values[0].value.length > 1 && valuesIndex < values[0].value.length - 1"> / </template>
					</template>
				</template>
			</template>
		</template>
		
		<!-- choix unique -->
		<template v-else>
			{{ field.listValues[values[0].value] }}
		</template>
	</span>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>