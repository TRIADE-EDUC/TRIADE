<template>
	<div class="portal-layout">
		<share_layout></share_layout>
		<zone_form v-if="showZoneForm" 
			@closeZoneForm="closeZoneForm" 
			@submitForm="zoneCreated" 
			:parentAuto="parent">
		</zone_form>
		<frame_form v-if="showFrameForm"
			@closeFrameForm="closeFrameForm" 
			:parentAuto="parent"
			view="layout">
		</frame_form> 
		<div class="menu">
			<div class="button-group">
				<button type="button" class="bouton cursor-pointer" @click="openZoneForm">{{ $cms.getMessage('add_zone') }}</button>
				<button type="button" class="bouton cursor-pointer" @click="openFrameForm">{{ $cms.getMessage('add_frame') }}</button>
			</div>
			<div class="show-hidden">
				<label for="show-hidden" class="cursor-pointer">{{ $cms.getMessage("show_hidden") }}</label>
				<input id="show-hidden" name="show-hidden" type="checkbox" v-model="showHidden" value="1">			
			</div>
		</div>
		<div :class="containerCSS">
			<zone root="1" parent=""
			 	v-if="container" 
			 	@update="container = $event"
			 	@openFrameForm="openFrameForm($event)"
			 	@openZoneForm="openZoneForm($event)"
			 	@openAttributeForm="openAttributeForm($event)"
				:zone="container" 
				:parentHidden="data.item.is_hidden" 
				:showHidden="showHidden">
			</zone>
		</div>
	</div>
</template>

<script>
	import zone from "./zone.vue";
	import zone_form from "./zoneForm.vue";
	import frame_form from "./frameForm.vue";
	import page_heritage from '../page/pageHeritage.vue';
	import share_layout from "./shareLayout.vue";
	
	export default {
		props: ['data'],
		components: {
			zone,
			page_heritage,
			zone_form,
			frame_form,
			share_layout
		},
	    data: function () {
	        return {
	        	container: {
					"class": "",
					id: 0,
					is_hidden: false,
					is_edited: false,
					name: "",
					children: [],
					semantic: {
						"class": "",
						classes: [],
						id: 0,
						id_tag: "",
						tag: "",
						attributes : []
					},
					tree: []
				},
	        	zoneList: [],
	        	showHidden: false,
	        	showZoneForm: false,
	        	showFrameForm: false,
	        	showAttributeForm: false,
	        	parent: null,
	        	shareLayoutResult: {}
	        }
	    },
	    watch: {
	        "data" : function(newValue, oldValue) {
	            this.load();
	        }
	    },
	    created: function() {
			this.load();
	    },
	    computed: {
	        isPage: function() {
	            return (this.data.item['class'] == "Pmb\\CMS\\Models\\PagePortalModel");
	    	},
	        containerCSS: function() {
	            let classeCss = "portal-layout-container";
	            if (this.data.item.legacy_layout) {
	                classeCss += " has-parent-layout";
	            }
	            return classeCss;
	    	}
	    },
	    methods: {
	    	load: async function() {
	        	if(this.data.item == undefined){
	        		this.data.item = {}
	        	}

				if (this.data.item && this.data.item.id != 0 && this.data.item.tree.length == 0) {
					this.data.item.tree = await this.$cms.fecthLayout(this.data.item);
				}
	        	if (this.data.item.tree) {
			        this.container = this.$cms.cloneObject(this.data.item.tree);
	        	}
	        	this.$cms.refreshZones(this.$root.container.data.item);
	    	},
	        closeZoneForm: function(event, submit) {
	        	var classList = event.target.classList;
	        	if(submit || classList.contains("portal-modal") || classList.contains("close-form") || classList.contains("fa-times")) {
	        		this.showZoneForm = false;
	        	}
	        },
	        closeFrameForm: function(event, submit) {
	        	var classList = event.target.classList;
	        	if(submit || classList.contains("portal-modal") || classList.contains("close-form") || classList.contains("fa-times")) {
	        		this.showFrameForm = false;
	        		this.parent = null;
	        	}
	        },
	        zoneCreated: function(event, submit) {
	            this.closeZoneForm(...arguments);
	            this.$cms.refreshZones(this.$root.container.data.item);
	        },
	        openFrameForm: function(parent) {
	        	if(typeof parent === 'string') {
	        		this.parent = parent; 
	        	}
	        	this.showFrameForm = true;
	        },
	        openZoneForm: function(parent) {
	        	if(typeof parent === 'string') {
	        		this.parent = parent; 
	        	}
	        	this.showZoneForm = true;
	        }
	    }
	}
</script>