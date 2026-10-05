<template>
	<div class="container-pivot">
		<component v-if="componantEntity" :is="componantEntity"  :data="data"></component>
	</div>
</template>

<script>
	import record from "./record/record.vue";
	
	export default {
		props : ["action", "type", "data"],
		data: function () {
			return {
				componantEntity: null,
			}
		},
		components : {
		    record,
		},
		mounted : function() {
			if (this.type) {
				this.componantEntity = this.type;
			}
		}
	}
</script>