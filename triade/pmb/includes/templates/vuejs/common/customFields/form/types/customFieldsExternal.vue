<template>
	<div class="container">
		<div v-for="(customValue, index) in values" class="row">
			<input :id="field.name + index" v-model="customValue.value" type="text" readonly="readonly" :size="field.options[0].size[0].value" :maxlength="field.options[0].maxsize[0].value"/>
			<input v-if="field.options[0].delete[0].value" @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
			<input type="button" class="bouton" :value="field.options[0].buttontext[0].value" @click=""/>
			<!-- Bouton non fonctionnel, laissé de côté pour la livraison du 07/10/2020 -->
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>