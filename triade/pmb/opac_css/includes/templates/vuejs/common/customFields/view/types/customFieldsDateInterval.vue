<template>
	<span>
		<template v-for="(customValue, index) in values" class="row">
			{{ pmb.getMessage('date', 'date_begin') }}
			{{ customValue.value }}
			{{ customValue.valueTime }}
			<template v-if="customValue.value1">
				{{ pmb.getMessage('date', 'date_end') }}
				{{ customValue.value1 }}
				{{ customValue.value1Time }}
			</template>
			<template v-if="values.length > 1 && index < values.length - 1"> / </template>
		</template>
	</span>
</template>

<script>
	export default {
		props : ["values", "pmb"]
	}
</script>