<template>
	<span>
		<template v-for="(label, index) in field.listValues">
			<template v-if="index == values[0].type">{{ label }}</template>
		</template>
		<template v-if="values[0].type == '4'">
			{{ field.msg.durationBegin }} 
		</template>
		{{values[0].value}}
		<template v-if="values[0].type == '4'">
			{{ field.msg.durationEnd }} {{values[0].value1}}
		</template>
	 
		<template v-if="values[0].comment">
			({{ values[0].comment }})
		</template>
	</span>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>