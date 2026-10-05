<template>
    <div>
		<emptyAssociatedItem :conditions="settings.conditions" :context="context"></emptyAssociatedItem>
    </div>
</template>

<script>
	import emptyAssociatedItem from "./emptyAssociatedItemConditions.vue";

    export default {
        props: ["settings", "context"],
		components: {
			emptyAssociatedItem
        },
        created: async function () {
			if (!this.settings.conditions) {
            	this.$set(this.settings, "conditions", {});
			}
        },
        watch: {
            "settings.conditions": function() {
                if (!this.settings.conditions) {
            	    this.$set(this.settings, "conditions", {});
			    }
            }
        
        }
    }
</script>