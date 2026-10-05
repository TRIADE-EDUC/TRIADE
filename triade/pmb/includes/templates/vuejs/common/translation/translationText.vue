<template>
	<div class="row">
		<input 
		type="text" 
		:name="code + '_' + nodeName" 
		:id="code + '_' + nodeName" 
		v-bind="attributes"
		v-model="translatedData[code]"
		@input="$emit('input', translatedData)" />
	</div>
</template>

<script>
	export default {
	    props: ["nodeName", "attributes", "code", "data"],
	    data: function () {
	        return {
				translatedData : this.data,
	        }
	    },
	}
</script>