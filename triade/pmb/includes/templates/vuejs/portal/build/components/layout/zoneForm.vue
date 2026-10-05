<template>
	<div class="portal-modal" @click="$emit('closeZoneForm', $event, false)">
	    <div class="portal-modal-container" ref="modal_zone">
	        <div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
	            <h3 class="portal-modal-title">{{ $cms.getMessage("zone_form_title") }}</h3>  
	            <button class="bouton close-form cursor-pointer" @click="$emit('closeZoneForm', $event, false)">
	            	<i class="fa fa-times" aria-hidden="true"></i>
	            </button>
	        </div>
	        <div class="portal-modal-content">
	            <form class="portal-form" @submit.prevent='submit'>
            	   	<div class="portal-form-group" v-if="errorMessage">
						<alert_error :message="errorMessage" @close="errorMessage = ''"></alert_error>
					</div>
	        		<div class="portal-form-group">
						<label for="zone-name" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("zone_name") }}</label>
						<input id="zone-name" ref="zone_input_name" name="zone_name" type="text" v-model.trim="name" required>
					</div>
					
					<div class="portal-form-group">
						<label for="zone-parent" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("zone_parent") }}</label>
						<select id="zone-parent" name="zone_parent" v-model="parent" required>
							<option v-for="(parent, key) in $cms.zoneList" :key="key" :value="parent.semantic.id_tag">{{ parent.name }}</option>
						</select>
					</div>
					
					<semantics
						@change="updateSemantic" 
						@update="updateSemantic"
						:semantic="semantic">
					</semantics>
				
					<div class="portal-form-buttons">
						<button class="bouton" type="submit">{{ $cms.getMessage("save") }}</button>
					</div>
				</form>
	        </div>
	    </div>
	</div>
</template>


<script>
	import alert_error from "../alertError.vue";
	import semantics from "../semantics/semantics.vue";

	export default {
		props: ["parentAuto"],
	    components: {
	        alert_error,
	        semantics
	    },
	    data: function () {
	        return {
	        	name: "",
	        	parent: "",
	        	semantic: {
	        	    id: 0,
	        	    "class": "",
					classes: [],
	        	    id_tag: "",
	        	    tag: ""
	        	},
		        errorMessage: "",
	            dragging: false,
            	x: 0,
	            y: 0,
	            startX: 0,
	            startY: 0,
	            dragStyle: ""
	        }
	    },
	    mounted: function() {
	    	window.addEventListener('mouseup', this.stopDrag);
	    	window.addEventListener('mousemove', this.doDrag);
	    	this.parentAuto ? this.parent = this.parentAuto : this.parent = this.$cms.zoneList[0].semantic.id_tag;
        	// Focus sur le premier input du formulaire
            this.$nextTick(() => {
            	this.$refs.zone_input_name.focus();
			});
	    },
	    destroyed : function(){
			window.removeEventListener('mouseup', this.stopDrag);
			window.removeEventListener('mousemove', this.doDrag);
		},
	    methods: {
			getPageOrGabarit() {
				return this.$root.container.data.item.class.includes('PagePortalModel') ? 'page_id' : 'gabarit_id';
			},
	        submit: function(event) {
	            var item = this.getPageOrGabarit()
	            
	            let promise = this.$cms.addElementlayout({
	            	name: this.name,
					parent: this.parent,
					class: "Pmb\\CMS\\Models\\ZoneCMSModel",
					semantic: this.semantic,
					[item]: this.$root.container.data.item.id
	            }, "layout");
	            
	            promise.then((result) => {
	                if (!result.error) {
	                	this.$emit('submitForm', event, true);
	                }
	            });
	        },
	        updateSemantic: function(semanticUpdated) {
	            this.semantic = semanticUpdated;
	        },
	        startDrag: function(event) {
	        	this.dragging = true;
	        	this.dragStyle = "cursor: grabbing;";
	        	this.startX = event.clientX;
        		this.startY = event.clientY;
	        },
			stopDrag: function(event) {
				this.dragging = false;
	        	this.dragStyle = "";
				this.x = this.y = 0;
			},
			doDrag: function(event) {
				if (this.dragging) {
			    	this.x = this.startX - event.clientX
			    	this.y = this.startY - event.clientY
			    	
		        	this.startX = event.clientX;
	        		this.startY = event.clientY;
			    	
	        		this.$refs.modal_zone.style.position = "absolute";
	        		this.$refs.modal_zone.style.left = (this.$refs.modal_zone.offsetLeft - this.x) + "px";
	        		this.$refs.modal_zone.style.top = (this.$refs.modal_zone.offsetTop - this.y) + "px";
			    }
			}
	    }
	}
</script>