<template>
	<div v-show="show" class="portal-modal" @click.self="hiddenModal">
	    <div class="portal-modal-container" ref="modal_zone">
	        <div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
	            <h3 class="portal-modal-title">{{ $cms.getMessage("share_layout_detail") }}</h3>  
	            <button class="bouton close-form cursor-pointer" @click.self="hiddenModal">
	            	<i class="fa fa-times" aria-hidden="true"></i>
	            </button>
	        </div>
	        <div class="portal-modal-content">
	        	<alert_error v-if="errorMessage" :message="errorMessage"></alert_error>
	        	<div v-for="(succes, label) in details">
	        		<p class="margin">
	        			{{ label }}
		        		<i v-if="succes" class="fa fa-check-circle succes" aria-hidden="true"></i>
		        		<i v-else class="fa fa-times remove" aria-hidden="true"></i>
	        		</p>
	        	</div>
	        </div>
	    </div>
	</div>
</template>


<script>
	import alert_error from "../alertError.vue";
	
	export default {
	    components: {
	        alert_error
	    },
	    data: function () {
	        return {
            	x: 0,
	            y: 0,
	            startX: 0,
	            startY: 0,
	            dragStyle: "",
	        	show: false,
	        	
	        	result: {
	        		detail: {},
	        		error: false,
	        		errorMessage: ""
	        	},
	        	
	        	open: (event) => {
	        		this.show = true;
	        		this.result = event.detail || {};
        		},
	        	hidden: (event) => { 
	        		this.show = false;
	        		this.result = {};
	        	}
	        }
	    },
	    computed: {
	    	errorMessage: function() {
	    		return (this.result && this.result.error) ? this.result.errorMessage : "";
	    	},
	    	details: function() {
	    		return (this.result && this.result.details) ? this.result.details : [];
	    	}
	    },
	    mounted: function() {
	    	window.addEventListener("showShareLayout", this.open.bind(this))
		    window.addEventListener("hiddenShareLayout", this.hidden.bind(this))
		    
	    	window.addEventListener('mouseup', this.stopDrag);
	    	window.addEventListener('mousemove', this.doDrag);
	    },
	    destroyed : function(){
	    	window.removeEventListener("showShareLayout", this.open.bind(this))
		    window.removeEventListener("hiddenShareLayout", this.hidden.bind(this))
		    
			window.removeEventListener('mouseup', this.stopDrag);
			window.removeEventListener('mousemove', this.doDrag);
		},
	    methods: {
	    	hiddenModal() {
        		this.show = false; 
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
	
	window.showShareLayout = (data) => {
		window.dispatchEvent(new CustomEvent("showShareLayout", {detail: {...data}}));
	};
	
	window.hiddenShareLayout = (data) => {
		window.dispatchEvent(new CustomEvent("hiddenShareLayout", {detail: {...data}}));
	};
	
</script>