<template>
    <div class="grid-2-col">
		<div id="sourcesList">
		    <sourcesform :action="action" :manifests_list="manifests_list" @selected="showDesc($event)"></sourcesform>
		</div>
	    <div id="sourceDetails">
	        <component :is="sourceName" @cancel="cancel" :data="data" v-if="showComponent"></component>
	        <div v-if="!showComponent && sourceName">
	            <img :src="images.get('patience.gif')" :alt="messages.get('common', 'wait')" :title="messages.get('common', 'wait')">
	        </div>
	    </div>
    </div>
</template>

<script>
    import sourcesform from "./sourcesForm.vue";

    export default {
        props : ["action", "manifests_list"],
        data: function () {
            return {
                showComponent: false,
                sourceName: null,
                data: {
                }
            }
        },
        components : {
        	sourcesform,
        },
        methods: {
            cancel: function(event) {
                this.sourceName = null;
            },
            showDesc: async function(event) {
                this.showComponent = false;
                this.sourceName = event.sourceName ?? null;
                if (this.sourceName !== null) {
                    this.data = await this.ws.get(event.sourceName, "showDesc");
                }
                this.data.sourceName = event.sourceName;
                this.showComponent = true;
            }
        }
    }
</script>