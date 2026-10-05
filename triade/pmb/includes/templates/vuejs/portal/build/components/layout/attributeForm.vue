<template>
	<div class="portal-modal"
		@click="$emit('closeAttributeForm', $event, false)">
		<div class="portal-modal-container" ref="modal_attribute">
			<div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
				<h3 class="portal-modal-title">
					{{ $cms.getMessage('attributes') }}
				</h3>
				<button class="bouton close-form cursor-pointer" @click="$emit('closeAttributeForm', $event, false)">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="portal-modal-content">
				<form id="portal_attributes_form" @submit.prevent="saveAttributes($event)">
					<div v-for="(attribute, index) in item.semantic.attributes" class="flex" :key="index">
						<label for="attribute_name">
							{{ $cms.getMessage('attribute') }}
						</label> 
						<input type="text" v-model="attribute.name" name="attribute_name" /> 
						
						<label for="attribute_value">{{ $cms.getMessage('value') }}</label> 
						<input type="text" v-model="attribute.value" name="attribute_value" />
						
						<button class="bouton" type="button" @click="removeAttribute(attribute.name)">
							<i class="fa fa-trash"></i>
						</button>
					</div>
					<div class="modal-buttons-container">
						<div class="modal-buttons-margin">
							<button class="bouton modal-add-button" @click.prevent="addAttribute">
								<i class="fa fa-plus" aria-hidden="true"></i>
							</button>
						</div>
						<div class="modal-buttons-margin">
							<button class="bouton" @click.prevent="saveAttributes($event)">
								{{ $cms.getMessage('save') }}
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props : ['parentAuto', 'item'],
	data : function(){
		return {
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
	},
	destroyed : function(){
		window.removeEventListener('mouseup', this.stopDrag);
		window.removeEventListener('mousemove', this.doDrag);
	},
	methods : {
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
		    	
        		this.$refs.modal_attribute.style.position = "absolute";
        		this.$refs.modal_attribute.style.left = (this.$refs.modal_attribute.offsetLeft - this.x) + "px";
        		this.$refs.modal_attribute.style.top = (this.$refs.modal_attribute.offsetTop - this.y) + "px";
		    }
		},
		removeAttribute : function(name) {
			var i = this.item.semantic.attributes.findIndex((a) => a.name == name);
			this.item.semantic.attributes.splice(i, 1);
		},
		addAttribute : function() {
			this.item.semantic.attributes.push({name : "", value : ""});
		},
		saveAttributes : function(e) {
			let parentClass = this.item.class;
			if(parentClass.toLowerCase().includes('frame')){
				this.$cms.addFrameAttributes(this.item, this.item.semantic.attributes);
			} else {
				this.$cms.addZoneAttributes(this.item, this.item.semantic.attributes);
			}
            this.$emit('update', this.item);
            this.$emit('closeAttributeForm', e, true);
		}
	}
}
</script>