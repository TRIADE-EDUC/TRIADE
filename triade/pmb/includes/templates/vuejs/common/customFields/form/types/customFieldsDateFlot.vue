<template>
	<div class="container">
		<select v-model="values[0].type">
			<option v-for="(label, index) in field.listValues" :value="index">{{ label }}</option>
		</select>
		<span>
			<label v-if="values[0].type == '4'">{{ field.msg.durationBegin }}</label>
			<input v-model="values[0].value" class="saisie-15em" type="text" placeholder="JJ/MM/AAAA"/>
		</span>
		<span v-if="values[0].type == '4'">
			<label>{{ field.msg.durationEnd }}</label>
			<input v-model="values[0].value1" class="saisie-15em" type="text" placeholder="JJ/MM/AAAA"/>
		</span>
		<span>
			<label>{{ field.msg.durationComment }}</label>
			<input v-model="values[0].comment" class="saisie-30em" type="text"/>
		</span>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>