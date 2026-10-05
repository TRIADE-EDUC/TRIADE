<template>
	<div ref="modal" id="modal" class="portal-modal" @click="closeModal">
	    <div class="portal-modal-container" ref="modal_frame">
	        <div class="portal-modal-header" :style="dragStyle" @mousedown.prevent="startDrag">
	            <h3 class="portal-modal-title" v-if="title">{{ title }}</h3>  
	            <button class="bouton close-form cursor-pointer" type="button" @click="$emit('closeFrameForm', true)">x</button>
	        </div>
	        <div class="portal-modal-content">
	            <form class="portal-form" @submit.prevent='submit'>
	            
            	   	<div class="portal-form-group" v-if="errorMessage">
						<alert_error :message="errorMessage" @close="errorMessage = ''"></alert_error>
					</div>
					
	        		<div class="portal-form-group">
						<label for="name" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("frame_name") }}</label>
						<input id="name" ref="input_name" name="name" type="text" v-model.trim="name" required>
					</div>
					
					<div class="portal-form-group">
						<label for="layout" class="portal-form-group-title cursor-pointer">{{ layoutLabel }}</label>
						<select id="layout" v-model="layout" @change="updateParent" :disabled="noLayout">
							<optgroup v-if="pages.length != 0" :label="$cms.getMessage('heritage_label_group_page')">
								<option v-for="(page, key) in pages" :key="`page_${key}`" :value="`page_${page.id}`">
									{{ page.name }}
								</option>
							</optgroup>
							<optgroup v-if="gabarits.length != 0" :label="$cms.getMessage('heritage_label_group_model')">
								<option v-for="(gabarit, key) in gabarits" :key="key"
									:value="`gabarit_${gabarit.id}`">
										{{ gabarit.name }}
								</option>
							</optgroup>
						</select>
					</div>
					
					<div class="portal-form-group">
						<label for="parent" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("frame_parent") }}</label>
						<select id="parent" name="frame_parent" v-model="parent" required :disabled="noLayout">
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
						<button class="bouton" type="submit" :disabled="noLayout">{{ $cms.getMessage("save") }}</button>
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
		props: [
		    'title',
		    'id_tag',
		    'init_name',
		    'pageList',
		    'gabaritList',
		],
	    components: {
	        alert_error,
	        semantics
	    },
	    data: function () {
	        return {
	        	name: "",
	        	parent: "",
	        	layout: "",
	        	semantic: {
	        	    id: 0,
	        	    "class": "Pmb\\CMS\\Semantics\\HtmlSemantic",
					classes: [],
	        	    id_tag: "",
	        	    tag: ""
	        	},
	        	zoneList: {},
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
	    	
	    	this.name = this.init_name ?? "";
	    	this.layout = this.firstLayout;
	    	this.updateParent();
	    	this.$nextTick(() => {
            	this.$refs.input_name.focus();
			});
	    },
	    computed: {
	        noLayout: function() {
	            return (this.pages.length == 0 && this.gabarits.length == 0)
	        },
	        pages: function() {
	            return (this.pageList && this.pageList.length > 0) ? this.pageList : [];
	        },
	        gabarits: function() {
	            return (this.gabaritList && this.gabaritList.length > 0) ? this.gabaritList : [];
	        },
	        firstLayout: function () {
	            if (this.pages && this.pages.length > 0) {
	                return `page_${this.pages[0].id}`;
	            }
	            if (this.gabarits && this.gabarits.length > 0) {
	                return `gabarit_${this.gabarits[0].id}`;
	            }
	            return "";
	        },
	        layoutLabel: function() {
	            if (this.pages && this.pages.length > 0) {
	                return this.$cms.getMessage('pages_list');
	            }
	            if (this.gabarits && this.gabarits.length > 0) {
	                return this.$cms.getMessage('gabarits_list');
	            }
	            return '';
	        }
	    },
	    methods: {
	        updateParent: async function() {
	            if (!this.layout) {
	                return;
	            }
	            
	            if (typeof this.zoneList[this.layout] != "undefined") {
	                this.zoneList[this.layout];
	            } else {	                
		            const layout = this.layout.split("_");
		            this.zoneList[this.layout] = await this.$cms.refreshZones({
		                id: layout[1],
	                    "class": layout[0] == "page" ? "Pmb\\CMS\\Models\\PagePortalModel" : "Pmb\\CMS\\Models\\GabaritLayoutModel",
		            })
	            }
	            this.parent = this.zoneList[this.layout][0].semantic.id_tag;
	        },
	        updateSemantic: function(semanticUpdated) {
	            this.semantic = semanticUpdated;
	        },
 	        closeModal: function(event) {
	            if (this.$refs.modal && event.target.id == this.$refs.modal.id) {	                
	                this.$emit('closeFrameForm', true);
	            }	            
	        },
	        getLayoutIndex() {
				return (this.layout.substr(0, 4) == "page") ? 'page_id' : 'gabarit_id';
			},
			getLayoutId() {
			    const layout = this.layout.split("_");
				return layout[1];
			},
	        submit: function() {
	            this.semantic.id_tag = this.id_tag;
	            const formData = {
	            	"name": this.name,
	            	"cadre_id": this.id_tag,
					"parent": this.parent,
					"class": "Pmb\\CMS\\Models\\FrameCMSModel",
					"semantic": this.semantic,
					[this.getLayoutIndex()]: this.getLayoutId()
	            }
	            
	            let promise = this.$cms.addElementlayout(formData);
	            promise.then((result) => {
	                if (!result.error) {
	                	this.$emit('submit', true);
	                } else {
	                	this.errorMessage = result.errorMessage;
	                }
	            });
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