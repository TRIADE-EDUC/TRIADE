<template>
	<div class="page-form">
		<cms_form v-if="showCmsForm" :data="cmsFormData" @changeSubType="changeSubType" @removeSubType="removeSubType" @closeCmsForm="closeCmsForm"></cms_form> 
	
		<div class="portal-form-group-clear" v-if="isEntity">
			<button class="bouton clear-btn" @click="cleanCache" type="button">{{ $cms.getMessage('page_clean_cache') }}</button>
		</div>
		
		<form action="" method="post" class="portal-form" @submit='submit'>
			
			<div class="portal-form-group" v-if="errorMessage">
				<alert_error :message="errorMessage" @close="errorMessage = ''"></alert_error>
			</div>
			<div class="portal-form-group">
				<label for="page-name" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("page_name") }}</label>
				<input id="page-name" name="page_name" ref="page_input_name" type="text" v-model.trim="name">
			</div>

			<div class="portal-form-group">
				<label for="page-type" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("page_type") }}</label>
				<select id="page-type" name="page_type" v-model="type" required :disabled="isEntity">
					<option v-for="(type, key) in typesOrderAsc" :key="key" :value="type.value">{{ type.label }}</option>
				</select>
			</div>

			<div class="portal-form-group">
				<label for="page-sub-type" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("page_sub_type") }}</label>
				<div class="portal-page-sub-type">
					<select id="page-sub-type" name="page_sub_type" v-model="sub_type" required :disabled="sub_types.length == 0 || isEntity">
						<option v-for="(sub_type, key) in sub_types" :key="key" :value="sub_type.value">{{ sub_type.label }}</option>
					</select>
					<span v-show="type==25" class="portal-page-icon"><i class="portal-page-icon-add fa fa-plus-circle" :title="$cms.getMessage('cms_page_add')" aria-hidden="true" @click="openCmsForm"></i></span>
					<span v-show="type==25 && sub_type!=2500" class="portal-page-icon"><i class="portal-page-icon-add fa fa-pencil" :title="$cms.getMessage('cms_page_edit')" aria-hidden="true" @click="openCmsForm($event, true)"></i></span>
				</div>
			</div>
			
			<conditions 
				v-if="type!=25" 
				@add="addCondition" 
				@remove="removeCondition" 
				@update="updateCondition" 
				:conditions="conditions">
			</conditions>
			
			<div class="portal-form-group">
				<label for="heritage-selector">{{ $cms.getMessage("heritage_label") }}</label>
				<page_heritage :data="data" @change="updateHeritage"></page_heritage>
			</div>

			<div class="portal-form-group" v-if="hasLayoutsList">
				<label for="heritage-list">{{ $cms.getMessage("layouts_list") }}</label>
				<template v-for="(layout, index) in layouts_list">
					<p>{{ layout }} <i class="fa fa-times cursor-pointer remove" aria-hidden="true" @click="removeLayout(index)"></i></p>
				</template>
			</div>

			<div class="portal-form-group">
				<frame_list @frameRemove="frameRemove" :list="frameList" :title="title" :is_entity="isEntity"></frame_list>
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
</template>

<script>
	import conditions from './conditions.vue';
	import page_heritage from './pageHeritage.vue';
	import alert_error from "../alertError.vue";
	import cms_form from './cmsPageForm.vue';
	import frame_list from '../frameList.vue';
	
	export default {
	    props: ["data"],
	    components: {
	    	page_heritage,
	    	alert_error,
	    	conditions,
	    	cms_form,
	    	frame_list
	    },
	    data: function() {
	        return {
	        	id: 0,
		        name: "",
		        conditions: [],
		        gabarit_layout: {
					"class": "",
					id: 0,
				},
		        page_layout: {
					"class": "",
					id: 0,
				},
		        parent_page: {
					"class": "",
					id: 0,
				},
		        type: "",
		        sub_type: "",
		        errorMessage: "",
		        showCmsForm: false,
		        cmsFormData: {},
		        frameList: [],
		        layouts_list: {}
	        }
	    },
	    mounted: function() {
	    	this.load();
	    },
	    watch: {
	        "data" : function(newValue, oldValue) {
	            this.load();
	        }
	    },
	    computed: {
	    	title: function() {
	    		let title = this.$cms.getMessage("heritage_has_frame");
	    		if(this.frameList.length == 0) {
		    		title = this.$cms.getMessage("heritage_no_frame");
	    		}
	    		return title;
	    	},
	        isEntity: function () {
	            return (this.data && this.data.item && this.data.item.id != 0);
	        },
	        typesOrderAsc: function() {
	            return this.sortAscType(this.$cms.portal.types);
	        },
	        sub_types: function() {
	            let sub_types = this.$cms.portal.sub_types.filter(sub_type => {
		            let type_code = this.$cms.getTypeFromSubType(sub_type.value);
	    			return type_code == this.type;
		        });

	            sub_types = this.sortAscType(sub_types);
	            
	            if (sub_types.length == 0) {
	                this.sub_type = null;
	            } else {
	            	if(!sub_types.find(sub_type => sub_type.value == this.sub_type)) {
			            this.sub_type = sub_types[0].value;
	            	}
	            }
	            return sub_types;
	        },
	        hasLayoutsList : function() {
	        	return (Object.keys(this.layouts_list).length > 0);
	        }
	    }, 
	    methods: {
	        load: function() {
	            this.frameList = [];
	            this.name = "";
	            if (this.data && this.data.item) {
		            this.name = this.data.item.name ?? "";
	            }
	            
	            const first_type = this.typesOrderAsc[0].value;
	            this.type = first_type;
	            if (this.data && this.data.item) {
		            this.type = this.data.item.type ?? first_type;
	            }
	            
	            const first_sub_type = this.sub_types[0].value;
	            this.sub_type = first_sub_type
	            if (this.data && this.data.item) {
		            this.sub_type = this.data.item.sub_type ?? first_sub_type;
	            }
	            
	            if (this.isEntity) {
		            this.id = this.data.item.id;
		            this.conditions = this.data.item.conditions;
		            this.gabarit_layout = this.data.item.gabarit_layout;
		            this.page_layout = this.data.item.page_layout;
		            this.parent_page = this.data.item.parent_page;
		            this.layouts_list = this.data.item.page_layout.layouts_list;
		            
		            let promise;
		    		if(this.parent_page) {
		    			promise = this.$cms.model.getFramesInPage(this.id);
		    		} else {
			    		promise = this.$cms.model.getFramesInGabarit(this.gabarit_layout.id);
		    		}
		    		promise.then((result) => {
		    		    if (result.error) {
		    		        throw result.errorMessage;
		    		    } else {		    		        
		    		        this.frameList = result;
		    		    }
		    		}, () => {
		    		    this.frameList = [];
	    		    });
		        } else {
		            this.id = 0;
			        this.conditions = [];
			        this.gabarit_layout = this.$cms.getDefaultGabarit();
			        this.page_layout = {};
			        this.parent_page = {};
		            this.layouts_list = {};
			        
		        	// Focus sur le premier input du formulaire
	                this.$nextTick(() => {
	                	this.$refs.page_input_name.focus();
					});
		        }
	        },
	        closeError: function() {
                this.errorMessage = "";
	        },
	        updateHeritage: function(heritage) {
	        	if (this.data.item == undefined){
	        		this.data.item = {}
	        	}
	        	if(heritage['class'].toLowerCase().includes('page')) {
	        		this.parent_page = heritage;
	        		this.gabarit_layout = null;
	        	} else {
	        		this.gabarit_layout = heritage;
	        		this.parent_page = null;
	        	}
	        },
			sortAscType: function(array) {
				array.sort((a, b) => {
				    const labelA = a.label.toLocaleLowerCase();
				    const labelB = b.label.toLocaleLowerCase();
				    
					if (labelA == labelB) {
						return 0;
					}
					return labelA < labelB ? -1 : 1
				});
				
				return array;
			},        
	        submit: function(event) {
	            event.preventDefault();
	            let promise = this.$cms.updatePage({
	            	"id": this.id,
	                "class": "Pmb\\CMS\\Models\\PagePortalModel",
	                "name": this.name,
	                "type": this.type,
	                "sub_type": this.sub_type,
	                "parent_page": this.parent_page,
	                "gabarit_layout": this.gabarit_layout,
	                "page_layout": this.page_layout,
	                "conditions": this.conditions
	            });
	            
	            promise.then((result) => {
	                if (result.error) {
	                    this.errorMessage = result.errorMessage;
	                }
	            });
	        },
	        remove: function() {
	        	if(!this.isEntity) {
	        		return;
	        	}
	        	
	            if (confirm(this.$cms.getMessage("page_confirm_delete"))) {
	                let promise = this.$cms.removePage(this.id);
		            promise.then((result) => {
		                if (result.error) {
		                    this.errorMessage = result.errorMessage;
		                }
		            });
	            }
	        },
	        cleanCache: function() {
	            let promise = this.$cms.model.clearCacheInPage(this.id);
	            promise.then((response) => {
	        		dojo.topic.publish('dGrowl', response.msg);
	            })
	        },
	        addCondition: function(condition) {
	            this.conditions.push({
	                "id": 0,
	                "class": condition,
	                "data": {}
	            });
	        },
	        removeCondition: function(index) {
	            this.conditions.splice(index, 1);
	        },
	        updateCondition: function(conditionData, index) {
	            this.conditions[index].data = conditionData;
	        },
	        openCmsForm: function(event, edit = false) {
	        	if(edit) {
	        		var id = parseInt(this.sub_type.toString().substr(2), 10);
		        	fetch('./ajax.php?module=cms&categ=pages&sub=edit&id=' + id, { method: 'GET' })
			            .then(response => {
	            			response.json().then(result => {
		  						this.cmsFormData = result;
		  						this.cmsFormData.subType = this.sub_type;
		  						this.showCmsForm = true;
	            			}) 
		            	});
	        	} else {
	        		this.cmsFormData = {};
		        	this.showCmsForm = true;
	        	}
	        },
	        closeCmsForm: function(event, submit) {
	        	var classList = event.target.classList;
	        	if(submit || classList.contains("portal-modal") || classList.contains("close-form")) {
	        		this.showCmsForm = false;
	        	}
	        },
	        changeSubType: function(value) {
	        	this.sub_type = value;
	        },
	        removeSubType: function(value) {
	        	let index = this.$cms.portal.sub_types.findIndex((sub_type) => sub_type.value == value);
	        	this.$cms.portal.sub_types.splice(index, 1);
	        	this.sub_type = this.sub_types[0].value;
	        },
	    	frameRemove: function(frame) {
				if (confirm(this.$cms.getMessage("layout_element_confirm_delete"))) {
					this.$cms.removeElementLayout({
						item: frame,
						parent: null,
						page_id: this.id
					});
				}
	    	},
	    	setFrameList: function(frameList) {
	    	    this.frameList = frameList;
	    	},
	    	removeLayout: function(index) {
	    	    this.$cms.removePageLayout({
	            	page : this.id,
	            	layout: index
	            });
	    	}
	    }
	}
</script>