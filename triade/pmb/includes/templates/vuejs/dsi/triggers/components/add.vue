<template>
	<div id="add">
        <formEvent :event="event" :types="types" :is_model="true"></formEvent>
	</div>
</template>

<script>
	import formEvent from "./formEvent.vue";
	export default {
		props : ["event", "types", "is_model"],
		components : {
		    formEvent,
		},
		created: function() {
			this.event.model = this.is_model;
		}
    }
</script>