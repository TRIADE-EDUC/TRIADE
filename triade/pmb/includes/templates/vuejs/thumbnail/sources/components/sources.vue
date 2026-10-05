<template>
	<div class="grid-2-col">
		<div id="sourcesList">
			<sourcesform :action="action" :sources="sources" @selected="showForm($event)"></sourcesform>
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
	import electre from "./form/electre.vue";
	import url from "./form/url.vue";
	import noimage from "./form/noimage.vue";
	import docnum from "./form/docnum.vue";
	import externallinks from "./form/externallinks.vue";
	import dilicom from "./form/dilicom.vue";
	import orb from "./form/orb.vue";
	import bnf from "./form/bnf.vue";
	
	export default {
		props : ["action", "sources"],
		data: function () {
			return {
			    showComponent: false,
			    sourceName: null,
			    data: {
			        entityType: ""
			    }
			}
		},
		components : {
		    sourcesform,
		    electre,
		    url,
		    noimage,
		    docnum,
		    externallinks,
		    dilicom,
		    orb,
		    bnf
		},
		methods: {
		    cancel: function(event) {
				this.sourceName = null;
			},
			showForm: async function(event) {
				this.showComponent = false;
				this.sourceName = event.sourceName ?? null;
				if (this.sourceName !== null) {				    
					this.data = await this.ws.get(event.entityType, event.sourceName);
				}
				this.data.entityType = event.entityType;
				this.showComponent = true;
			}
		}
	}
</script>