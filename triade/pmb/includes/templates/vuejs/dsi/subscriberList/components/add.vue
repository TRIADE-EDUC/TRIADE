<template>
	<formSubscriberList 
		:subscriber-list="subscriberList" 
		:types="types" 
		:modelForm="true" 
		:id-entity="subscriberList.source.id"></formSubscriberList>
</template>

<script>
	import formSubscriberList from "./formSubscriberList.vue";
	export default {
		props : ["subscriberList", "types"],
		components : {
			formSubscriberList
		}
	}
</script>