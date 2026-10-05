<template>
	<div class="portal-modal" @click="$emit('closeFrameForm', $event, false)">
	    <div class="portal-modal-container" ref="modal_frame">
	        <div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
	            <h3 class="portal-modal-title">{{ $cms.getMessage("frame_form_title") }}</h3>  
	            <button class="bouton close-form cursor-pointer" @click="$emit('closeFrameForm', $event, false)">
	            	<i class="fa fa-times" aria-hidden="true"></i>
	            </button>
	        </div>
	        <div class="portal-modal-content">
	            <form class="portal-form" @submit='submit'>
	            
            	   	<div class="portal-form-group" v-if="errorMessage">
						<alert_error :message="errorMessage" @close="errorMessage = ''"></alert_error>
					</div>

					<div class="portal-form-group">
						<label for="frame-parent-heritage" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("frame_parent_heritage") }}</label>
						<input class="portal-modal-filter" id="search_frame_form" ref="frame_input_search" name="search" v-model="search" type="text" :placeholder="$cms.getMessage('frame_filter_placeholder')">
						<select id="frame-parent-heritage" ref="frameInputSelector" class="portal-modal-frame-list" name="frame_cadre_id" v-model="cadre_id" @change="changeName($event)" required>
							<optgroup v-for="(frames, classement) in filteredList" :key="classement" :label="classement">
								<option v-for="(frame, key) in frames" :key="key" :value="frame.semantic.id_tag">{{ frame.name }}
								</option>
							</optgroup>
						</select>
					</div>
					
	        		<div class="portal-form-group">
						<label for="frame-name" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("frame_name") }}</label>
						<input id="frame-name" ref="frame_input_name" name="frame_name" type="text" v-model.trim="name" required>
					</div>
					
					<div class="portal-form-group">
						<label for="frame-parent" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("frame_parent") }}</label>
						<select id="frame-parent" name="frame_parent" v-model="parent" required>
							<option v-for="(parent, key) in $cms.zoneList" :key="key" :value="parent.semantic.id_tag">{{ parent.name }}</option>
						</select>
					</div>
					
					<semantics
						@change="updateSemantic" 
						@update="updateSemantic"
						:semantic="semantic"
						editable="false">
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
		props: ["parentAuto", "view"],
	    components: {
	        alert_error,
	        semantics
	    },
	    data: function () {
	        return {
	        	name: "",
	        	cadre_id: "",
	        	parent: "",
	        	semantic: {
	        	    id: 0,
	        	    "class": "Pmb\\CMS\\Semantics\\HtmlSemantic",
					classes: [],
	        	    id_tag: "",
	        	    tag: ""
	        	},
		        errorMessage: "",
		        framesAlreadyExist: [],
		        search: "",
	            dragging: false,
            	x: 0,
	            y: 0,
	            startX: 0,
	            startY: 0,
	            dragStyle: ""
	        }
	    },
	    mounted: async function() {
	    	window.addEventListener('mouseup', this.stopDrag);
	    	window.addEventListener('mousemove', this.doDrag);
	    	
	    	await this.$cms.refreshZones(this.$root.container.data.item);
	    	
	    	this.getFramesAlreadyExist();
	    	this.parentAuto ? this.parent = this.parentAuto : this.parent = this.$cms.zoneList.filter(zone => (zone.semantic.id_tag == "container"))[0].semantic.id_tag;
	    	this.semantic.tag = this.$cms.semantic[0];
	    	
        	// Focus sur le premier input du formulaire
            this.$nextTick(() => {
            	this.$refs.frame_input_search.focus();
			});
	    },
	    destroyed : function(){
			window.removeEventListener('mouseup', this.stopDrag);
			window.removeEventListener('mousemove', this.doDrag);
		},
	    computed: {
	    	filteredList: function() {
	    		if(!this.frameList) {
	    			return [];
	    		}
	    		
	    		var list = this.$cms.cloneObject(this.frameList);
	    		
		        if (this.search != "") {
		            const regexDefault = new RegExp(this.search.trim(), 'i');
		            const regexID = new RegExp(`_${this.search.trim()}$`, 'i');
		            
		            const DEFAULT_SEARCH = 0;
		            const SEARCH_BY_NODE_ID = 1;
		            const SEARCH_BY_ID = 2;
		            
		            let search;
		            if (this.search.match('cms_module_')) {
		                search = SEARCH_BY_NODE_ID;
		            } else if (!isNaN(this.search)) {
		                search = SEARCH_BY_ID;		                
		            } else {
			            search = DEFAULT_SEARCH;		                
		            }
		            
	        		const search_id = this.search.match('cms_module_') ? true : false;
		            for (var classement in list) {
		                
		                switch (search) {
		                	case SEARCH_BY_NODE_ID: 
			                	list[classement] = list[classement].filter(child => child.semantic.id_tag == this.search.trim());
		                		break;
		                	case SEARCH_BY_ID:
			                    const search1 = list[classement].filter(child => regexID.exec(child.semantic.id_tag));
			                    const search2 = list[classement].filter(child => regexDefault.exec(child.name.trim()));
			                    list[classement] = [...search1, ...search2]; 
		                		break;
		                	case DEFAULT_SEARCH:
	                	    default:
			                	list[classement] = list[classement].filter(child => regexDefault.exec(child.name.trim()));
		                		break;
		                }
		        	}
	        	}
		        
		        var orderList = {};
		        const orderKeys = this.arrayOrderAlpha(Object.keys(list))
		        if(orderKeys.length == 0){
		        	return list;
		        }
		        for (var i = 0; i < orderKeys.length; i++) {
		            const index = orderKeys[i];
		            orderList[index] = this.arrayOrderAlpha(list[index]);
		        }
		        
		    	const key = orderKeys[0];
		        if(orderList[key].length > 0) {   	
			    	this.cadre_id = orderList[key][0].semantic.id_tag;
			    	this.name = orderList[key][0].name;
		        }
		        return list;
	    	},
	    	framesAlreadyUsed: function() {
	    		var frames = [];
		        for (var i = 0; i < this.framesAlreadyExist.length; i++) {
		        	if(this.framesAlreadyExist[i]['class'].includes("FrameCMSModel")) {
			        	frames.push(this.framesAlreadyExist[i].semantic.id_tag);
		        	}
		        }
		        return frames;
	    	},
	    	frameList: function() {
	    		const default_classement = this.$cms.getMessage('default_classement');
	    		
	    		let frames = {};
	    		for (const index in this.$cms.frames) {
	    			const frame = this.$cms.frames[index];
					if(this.framesAlreadyUsed.includes(frame.semantic.id_tag)) {
						continue;
					}
	    			if (frame && frame.class.includes('FrameOpac')) {
	    				continue;
	    			}

	    			const classement = (frame.classement && frame.classement != "") ? frame.classement : default_classement;
	    			if (!frames[classement]) {
	    				frames[classement] = [];
	    			}
	    			
	    			frames[classement].push(frame);
	    		}
	    		return frames;
	    	}
	    },
	    methods: {
			getPageOrGabarit() {
				return this.$root.container.data.item.class.includes('PagePortalModel') ? 'page_id' : 'gabarit_id';
			},
	        submit: function(event) {
	            event.preventDefault();
	            var item = this.getPageOrGabarit()
	            
	            let promise = this.$cms.addElementlayout({
	            	name: this.name,
	            	cadre_id: this.cadre_id,
					parent: this.parent,
					class: "Pmb\\CMS\\Models\\FrameCMSModel",
					semantic: this.semantic,
					[item]: this.$root.container.data.item.id
	            }, this.view);
	            
	            promise.then((result) => {
	                if (!result.error) {
	                	this.$emit('closeFrameForm', event, true);
	                } else {
	                	this.errorMessage = result.errorMessage;
	                }
	            });
	        },
	    	changeName(event) {
	        	this.name = event.target.options[event.target.selectedIndex].text
	    	},
	        updateSemantic: function(semanticUpdated) {
	            this.semantic = semanticUpdated;
	        },
		    arrayOrderAlpha: function (array) {
		        array.sort((a, b) => {
		            var titleA = a.name ? a.name.toLowerCase() : "";
		            var titleB = b.name ? b.name.toLowerCase() : "";
		            if(titleA < titleB) { return -1; }
		            if(titleA > titleB) { return 1; }
		            return 0;
				});
		        return array;
			},
			getFramesAlreadyExist: function() {
    			var id = this.$cms.container.data.item.id;
    			var className = this.$cms.container.data.item.class;
    			
	    		if(className == "Pmb\\CMS\\Models\\PagePortalModel") {
	    			var promise = this.$cms.model.getFramesInPage(id);
	    		} else {
		    		var promise = this.$cms.model.getFramesInGabarit(id);
	    		}
	    		
	    		promise.then((result) => {
	    		    if (result.error) {
	    		        this.framesAlreadyExist = [];
	    		        throw result.errorMessage;
	    		    } else {		    	
		    			this.framesAlreadyExist = result;
	    		    }
	    		}, () => {this.framesAlreadyExist = []});
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