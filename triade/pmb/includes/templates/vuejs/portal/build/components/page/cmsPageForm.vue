<template>
	<div class="portal-modal" @click="$emit('closeCmsForm', $event, false)">
	    <div class="portal-modal-container portal-cms-modal-container" ref="modal_cms">
	        <div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
	            <h3 class="portal-modal-title">{{ title }}</h3>  
	            <button class="bouton close-form cursor-pointer" @click="$emit('closeFrameForm', $event, false)">x</button>
	        </div>
	        <div class="portal-modal-content">
        		<div class="page-form">
					<form action="" method="post" class="portal-form" @submit.prevent='submit'>
					
						<div class="portal-form-group">
							<label for="page-name" class="portal-form-group-title cursor-pointer">
								{{ $cms.getMessage('infopage_title_infopage') }}
							</label>
							<input id="name" name="name" ref="page_input_name" type="text" v-model="name" v-model.trim="name" required>
						</div>
			
						<div class="portal-form-group">
							<label for="page-summary" class="portal-form-group-title cursor-pointer">
								{{ $cms.getMessage('cms_page_description') }}
							</label>
							<textarea id="description" name="description" cols="120" rows="5" v-model.trim="summary"></textarea>
						</div>
			
						<div class="portal-form-group">
							<label for="var_name_0" class="portal-form-group-title cursor-pointer">
								{{ $cms.getMessage('cms_page_variables') }}
							</label>
							<div class="page-form-var" v-for="(env_var, index) in vars" :key="`env_var_${index}`">
								<input type="text" 
									:id="`var_name_${index}`" 
									:name="`var_name_${index}`" 
									:placeholder="$cms.getMessage('cms_page_var_name')" 
									v-model.trim="env_var.name">
								<input type="text" 
									:id="`var_comment_${index}`" 
									:name="`var_comment_${index}`" 
									:placeholder="$cms.getMessage('cms_page_var_summary')" 
									v-model.trim="env_var.comment">
									
								<div class="groupe-button" v-if="(index+1) == vars.length">
									<button type="button" class="bouton" @click="reset(index)">X</button>
									<button type="button" class="bouton" @click="add">+</button>
								</div>
							</div>
						</div>
						
						<div class="portal-form-buttons">
							<div class="left">
								<button class="bouton" type="submit">{{ $cms.getMessage("save") }}</button>
							</div>
							<div class="right">
								<button v-if="isEntity" class="bouton delete" type="button" @click="remove">{{ $cms.getMessage("delete") }}</button>
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
	    props: ["data"],
	    data: function() {
	        return {
	        	id: 0,
	            name: "",
	            summary: "",
	            vars: [
	                {
	                    name: "",
	                    comment: ""
	                }
	            ],
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
	    	
	    	if(this.data && this.data.id) {
	    		this.id = this.data.id;
	    		this.name = this.data.name;
	    		this.summary = this.data.description;
	    		if(this.data.vars.length != 0) {
		    		this.vars = this.data.vars;
	    		}
	    	}
	     	// Focus sur le premier input du formulaire
            this.$nextTick(() => {
            	this.$refs.page_input_name.focus();
			});
	    },
	    computed: {
	        isEntity: function() {
	            return (this.data && this.data.id && this.data.id != 0);
	        },
	        title: function() {
	        	return this.isEntity ? this.$cms.getMessage("cms_edit_page_title") : this.$cms.getMessage("cms_page_title");
	        }
	    },
	    methods: {
	        reset: function(index) {
	            if (index == 0) {
	                this.vars[index].name = "";
	                this.vars[index].comment = "";
	            } else {
	                this.vars.splice(index, 1);
	            }
	        },
	        add: function() {
	            this.vars.push({
                    name: "",
                    summary: ""
                });
	        },
	        getFormValues: function() {
	        	let formValues = {
	        		name: this.name,
	        		description: this.summary,
	        		var_count: this.vars.length.toString()
	        	}
	        	for(let i=0; i < this.vars.length; i++) {
	        		formValues["var_name_" + (i+1)] = this.vars[i].name;
	        		formValues["var_comment_" + (i+1)] = this.vars[i].comment;
	        	}
	        	return formValues;
	        },
	        submit: function(event) {
	        	this.$cms.showLoader();
				let post = new URLSearchParams();
				let formValues = this.getFormValues();
				
				for(let index in formValues) {
					post.append(index, formValues[index]);
				}
	        	const options = {
        		    method: 'POST',
        		    body: post
        		};
	        	fetch('./ajax.php?module=cms&categ=pages&sub=save&id=' + this.id , options)
		            .then(response => {
            			response.json().then(result => {
            				if(!result.error) {
            					var value = parseInt('25' + ('0' + result).slice(-2));
            					
            					const index = this.$cms.portal.sub_types.findIndex(sub_type => sub_type.value == value);
            					if (index != -1) {
            					    this.$cms.portal.sub_types[index]['label'] = this.name; 
            					    this.$cms.portal.sub_types[index]['value'] = value;
            					} else {            					    
	            					this.$cms.portal.sub_types.push({
	            						label: this.name,
	            						value: value
	            					});
            					}
            					this.$emit('changeSubType', value);
            					this.$emit('closeCmsForm', event, true);
					        	this.$cms.hiddenLoader();
            				}
            			}) 
	            	});
			},
			remove: function(event) {
	            if (confirm(this.$cms.getMessage("confirm_remove_cms_page"))) {
	            	this.$cms.showLoader();
		        	fetch('./ajax.php?module=cms&categ=pages&sub=del&id=' + this.id , {method: 'GET'})
		            .then(response => {
	        			response.json().then(result => {
							if(result == 0) {
					        	let pages = this.$cms.pages.filter(page => (page.sub_type == this.data.subType));
					        	for(let page of pages) {
						        	this.$cms.removePage(page.id);
					        	}
								
								var value = parseInt('25' + ('0' + this.id).slice(-2));
								this.$emit('removeSubType', value);
								this.$emit('closeCmsForm', event, true);
								this.$cms.hiddenLoader();
							}
	        			}) 
	            	});
	            }
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
			    	
	        		this.$refs.modal_cms.style.position = "absolute";
	        		this.$refs.modal_cms.style.left = (this.$refs.modal_cms.offsetLeft - this.x) + "px";
	        		this.$refs.modal_cms.style.top = (this.$refs.modal_cms.offsetTop - this.y) + "px";
			    }
			}
	    }
	}
</script>