<template>
	<div class="portal-modal" @click.stop="$emit('closeContextForm', $event)">
	    <div class="portal-modal-container portal-context-modal-container" ref="modal_context" @click.stop="">
	        <div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
	            <h3 class="portal-modal-title">{{ $cms.getMessage("context_form_title") }}</h3>  
	            <button class="bouton close-form cursor-pointer" @click.stop="$emit('closeContextForm', $event)">x</button>
	        </div>
	        <div class="portal-modal-content" @click.stop="">
				<div class="portal-context" v-for="(context, index) in contextsList" :key="`context_${index}`">
		        	<form class="portal-form-context" @submit.prevent='saveContext(index)'>
						<div class="portal-context-info">
							<div v-if="!editedContexts.includes(index)" class="context-name">
								<i :class="getClassesFav(index)" aria-hidden="true" @click="changeBookmark(index)"></i>
								<label :for="`context_${index}`" class="cursor-pointer">
									{{ context.name }}
								</label>
							</div>
							<div v-else class="context-name">
								<input class="context-edited-name" type="text" :ref="`context_input_${index}`" v-model="context.name" v-on:keyup.enter="$event.form.submit()"  required>
							</div>
							<div class="context-menu">
								<button type="submit" v-show="editedContexts.includes(index)" class="edit-context cursor-pointer bouton">
									<i class="fa fa-save" aria-hidden="true" :title="$cms.getMessage('save')"></i>
								</button>
								<button type="button" v-if="!editedContexts.includes(index)" class="edit-context cursor-pointer bouton" @click="editContext(index)">
									<i class="fa fa-pencil" aria-hidden="true" :title="$cms.getMessage('cms_page_edit')"></i>
								</button>
								<button type="button" class="edit-context cursor-pointer bouton delete" @click="removeContext(index)">
									<i class="fa fa-trash" aria-hidden="true" :title="$cms.getMessage('delete')"></i>
								</button>
							</div>
							<div class="context-others">
								<button type="button" class="edit-context cursor-pointer bouton" @click="sendContext(index)">
									<i class="fa fa-arrow-right" aria-hidden="true" :title="$cms.getMessage('portal_send_preview')"></i>
								</button>
							</div>
						</div>
					</form>
				</div>
	        </div>
	    </div>
	</div>
</template>

<script>

	export default {
		props: ["contexts", "bookmarkContext"],
	    data: function () {
	        return {
	        	name: "",
	        	editedContexts: [],
	            dragging: false,
            	x: 0,
	            y: 0,
	            startX: 0,
	            startY: 0,
	            dragStyle: "",
	            contextsList: []
	        }
	    },
	    mounted: function() {
	    	window.addEventListener('mouseup', this.stopDrag);
	    	window.addEventListener('mousemove', this.doDrag);

	    	this.contextsList = this.$cms.cloneObject(this.contexts);
	    },
	    methods: {
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
			    	
	        		this.$refs.modal_context.style.position = "absolute";
	        		this.$refs.modal_context.style.left = (this.$refs.modal_context.offsetLeft - this.x) + "px";
	        		this.$refs.modal_context.style.top = (this.$refs.modal_context.offsetTop - this.y) + "px";
			    }
			},
			editContext: function(index) {
				this.editedContexts.push(index);
				this.$nextTick(() => {
					this.$refs[`context_input_${index}`][0].focus();
				})
			},
			saveContext: function(index) {
				if(this.contexts[index].name != this.contextsList[index].name) {
	  			  	this.$emit('saveContext', index, this.contextsList[index]);
				}
				this.unEditContext(index);
			},
			removeContext: function(index) {
				if(confirm(this.$cms.getMessage('confirm_remove_context'))) {
	  			  	this.unEditContext(index);
	  			  	this.contextsList.splice(index, 1);
	  			  	this.$emit('removeContext', index);
				}
			},
			sendContext: function(index) {
				this.$emit("sendContext", index);
			},
			unEditContext: function(index) {
				const findIndex = this.editedContexts.findIndex(contextIndex => contextIndex == index);
  			  	this.editedContexts.splice(findIndex, 1);
			},
			getClassesFav: function(index) {
			    return (this.isFavContext(index)) ? 'fav fa fa-star' : 'fav fa fa-star-o';
			},
			isFavContext: function(index) {
			    return this.bookmarkContext == index;
			},
			changeBookmark: function(index) {
  			  	this.$emit('changeBookmark', index);
			}
	    }
	}
</script>