<template>
	<div class="portal-modal" @click="$emit('closePageForm', $event, false)">
	    <div class="portal-modal-container" ref="modal_frame">
	        <div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
	            <h3 class="portal-modal-title">{{ $cms.getMessage("gabarit_page_add") }}</h3>  
	            <button class="bouton close-form cursor-pointer" @click="$emit('closePageForm', $event, false)">x</button>
	        </div>
	        <div class="portal-modal-content">
	            <form class="portal-form" @submit='submit'>
	            
            	   	<div class="portal-form-group" v-if="errorMessage">
						<alert_error :message="errorMessage" @close="errorMessage = ''"></alert_error>
					</div>

					<div class="portal-form-group">
						<label for="frame-parent-heritage" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("gabarit_page_label") }}</label>
						<input class="portal-modal-filter" id="search_frame_form" ref="page_input_search" name="search" v-model="search" type="text" :placeholder="$cms.getMessage('frame_filter_placeholder')">
						<select id="frame-parent-heritage" ref="frameInputSelector" class="portal-modal-frame-list" name="frame_cadre_id" v-model="pageId" required>
							<option v-for="(page, key) in filteredList" :key="key" :value="page.id">{{ page.name }}</option>
						</select>
					</div>
					
					<div class="portal-form-buttons">
						<button class="bouton" type="submit">{{ $cms.getMessage("cms_page_add") }}</button>
					</div>
				</form>
	        </div>
	    </div>
	</div>
</template>


<script>
	import alert_error from "../alertError.vue";

	export default {
		props: ["pagesSelected"],
	    components: {
	        alert_error
	    },
	    data: function () {
	        return {
	        	pageId: "",
		        errorMessage: "",
		        search: "",
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
	    	
        	// Focus sur le premier input du formulaire
            this.$nextTick(() => {
            	this.$refs.page_input_search.focus();
			});
	    },
	    computed: {
	    	filteredList: function() {
	    		var pages = this.$cms.cloneObject(this.$cms.pages);
	    		
		        if (this.search != "") {
		            const regex = new RegExp(this.search.trim(), 'i');
		            pages = pages.filter(page => regex.exec(page.name.trim()));
		        }
		        pages = pages.filter(page => (!this.pagesSelected.includes(page.id)));
		        
		        if(pages.length > 0) { 
		        	this.pageId = pages[0].id;
		        }
		        return pages;
	    	}
	    },
	    methods: {
	        submit: function(event) {
	            event.preventDefault();
				this.$emit('addPage', this.pageId);        
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
			    	
	        		this.$refs.modal_frame.style.position = "absolute";
	        		this.$refs.modal_frame.style.left = (this.$refs.modal_frame.offsetLeft - this.x) + "px";
	        		this.$refs.modal_frame.style.top = (this.$refs.modal_frame.offsetTop - this.y) + "px";
			    }
			}
	    }
	}
</script>