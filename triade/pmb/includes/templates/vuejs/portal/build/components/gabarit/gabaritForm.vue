<template>
	<div class="gabarit-form">
		<form action="" method="post" class="portal-form" name="portal_form" @submit.prevent='submit'>

			<div class="portal-form-group" v-if="errorMessage">
				<alert_error :message="errorMessage" @close="errorMessage = ''"></alert_error>
			</div>

			<div class="portal-form-group">
				<label for="gabarit-name" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("gabarit_name") }}</label>
				<input id="gabarit-name" ref="gabarit_input_name" name="gabarit_name" type="text" v-model.trim="name">
			</div>

			<div class="portal-form-group">
				<label for="gabarit-default" class="cursor-pointer">{{ $cms.getMessage("gabarit_default") }}</label>
				<input id="gabarit-default" name="gabarit_default" type="checkbox" v-model="isDefault" value="1" :disabled="isDefaultGabarit">				
			</div>

			<div class="portal-form-group">
				<label for="heritage-selector">{{ $cms.getMessage("heritage_label") }}</label>
				<gabarit_heritage :data="data" @change="changeHeritage"></gabarit_heritage>
			</div>

			<div class="portal-form-group" v-if="hasLayoutsList">
				<label for="heritage-list">{{ $cms.getMessage("layouts_list") }}</label>
				<template v-for="(layout, index) in layouts_list">
					<p>{{ layout }} <i class="fa fa-times cursor-pointer remove" aria-hidden="true" @click="removeLayout(index)"></i></p>
				</template>
			</div>
			
			<div class="portal-form-group" v-if="id > 0">
				<page_form :pagesSelected="listPageId" v-if="showPageForm" @addPage="addPage" @closePageForm="closePageForm"></page_form> 
				<list :list="pages" v-if="$cms.pages.length > 0" @add="openPageForm"
					:addable="pageIsAddable" :title="$cms.getMessage('gabarit_page_list')"
					:add_msg="$cms.getMessage('add_page')" @itemClicked="openSubNav">
				</list>
			</div>

			<div class="portal-form-group" v-if="id > 0">
				<frame_list @frameRemove="frameRemove" :list="frameList" :title="title" :is_entity="isEntity"></frame_list>
			</div>

			<div class="portal-form-group" v-if="id > 0">
				<list :list="inheritedModel" :addable="false" :title="$cms.getMessage('gabarit_legacy_heritage')" :empty_msg="$cms.getMessage('gabarit_no_legacy_heritage')"></list>
			</div>

			<div class="portal-form-buttons">
				<div class="left">
					<button class="bouton" type="submit">{{ $cms.getMessage("save") }}</button>
				</div>
				<div class="right">
					<button v-if="isEntity" class="bouton" type="button" @click="duplicate">{{ $cms.getMessage("duplicate") }}</button>
					<button v-if="isEntity" class="bouton delete" type="button" @click="remove" :disabled="cannotDelete">{{ $cms.getMessage("delete") }}</button>
				</div>
			</div>

		</form>
	</div>
</template>

<script>
	import list from "../list.vue";
	import frame_list from "../frameList.vue";
	import alert_error from "../alertError.vue";
	import page_form from "./gabaritPageForm.vue";
	import gabarit_heritage from "./gabaritHeritage.vue";

	export default {
	    props: ["data"],
	    components: {
	        list,
	        alert_error,
	        frame_list,
       		page_form,
       		gabarit_heritage
	    },
	    data: function() {
	        return {
		        id: 0,
		        name: "",
		        isDefault: 0,
		        errorMessage: "",
		        frameList: [],
		        pages_selected: [],
	            showPageForm: false,
	            legacy_layout: null,
	            layouts_list: {}
	        }
	    },
	    created: function() {
            this.load();
	    },
	    watch: {
	        "data" : function(newValue, oldValue) {
	            this.load()
	        }
	    },
	    computed: {
	        inheritedModel: function () {
	            let legacyElements = this.$cms.gabarits.filter(gabarit => {
	        		if(gabarit.legacy_layout) {
	        			return (gabarit.legacy_layout.id == this.id) && (gabarit.legacy_layout['class'] == this.data.item['class']);
	        		}
	        		return false;
	        	});
	            return legacyElements;
	        },
	        isEntity: function () {
	            return (this.data && this.data.item && this.data.item.id != 0);
	        },
	        pages: function () {
	            return this.getPages().filter(page => this.pages_selected.includes(page.id)) ?? [];
	        },
	    	title: function() {
	    		let title = this.$cms.getMessage("heritage_has_frame");
	    		if(this.frameList.length == 0) {
		    		title = this.$cms.getMessage("heritage_no_frame");
	    		}
	    		return title;
	    	},
	        listPageId: function() {
	        	var listId = [];
	        	for(var page of this.pages) {
	        		listId.push(page.id);
	        	}
	        	return listId;
	        },
	        pageIsAddable: function() {
	        	return this.pages.length != this.$cms.pages.length;
	        },
	        cannotDelete: function() {
	        	let legacyElements = this.$cms.gabarits.findIndex(gabarit => {
	        		if(gabarit.legacy_layout) {
	        			return (gabarit.legacy_layout.id == this.id) && (gabarit.legacy_layout['class'] == this.data.item['class']);
	        		}
	        		return false;
	        	});
	        	if(legacyElements !== -1) {
	        		return true;
	        	}
	        	return this.isDefaultGabarit;
	        },
	        isDefaultGabarit : function() {
	        	return (this.data && this.data.item && this.data.item.default == 1);
	        },
	        hasLayoutsList : function() {
	        	return (Object.keys(this.layouts_list).length > 0);
	        }
	    }, 
	    methods: {
			getPages: function() {
			    return this.$cms.pages ?? [];
			},
	        closeError: function() {
                this.errorMessage = "";
	        },
	        load: function() {
	            this.frameList = [];
	            
	            if (this.isEntity) {
		            this.id = this.data.item.id;
		            this.name = this.data.item.name;
		            this.isDefault = this.data.item.default;
		            this.legacy_layout = this.data.item.legacy_layout;
		            this.getSelectedPages();
		            this.layouts_list = this.data.item.layouts_list;
		            
		            let promise = this.$cms.model.getFramesInGabarit(this.id);
		    		promise.then((result) => {
		    		    if (result.error) {
		    		        this.frameList = [];
		    		        throw result.errorMessage;
		    		    } else {		    		        
			    			this.frameList = result;
		    		    }
		    		}, () => { this.frameList = [] });
		        } else {
		            this.id = 0;
		            this.name = "";
		            this.isDefault = 0;
		            this.legacy_layout = null;
		            this.pages_selected = [];
		            this.layouts_list = {};
		            
		        	// Focus sur le premier input du formulaire
	                this.$nextTick(() => {
	                	this.$refs.gabarit_input_name.focus();
					});
		        }
	        },
	        getSelectedPages: function() {
	            if (!this.isEntity) {
	                return;
	            }
	            
	            this.pages_selected = [];
                this.$cms.getPagesUsingGabarit(this.id).forEach(page => {
    	            this.pages_selected.push(page.id);
    	        });
	        },
	        remove: function() {
	            if (!this.isEntity) {
	                retrun;
	            }
	            
	            if (this.pages_selected.length > 0) {
		            if (!confirm(this.$cms.getMessage("gabarit_confirm_delete_pages"))) {
		                return;
		            }
	            }
	            
	            if (confirm(this.$cms.getMessage("gabarit_confirm_delete"))) {
	                let promise = this.$cms.removeGabarit(this.id);
		            promise.then((result) => {
		                if (result.error) {
		                    this.errorMessage = result.errorMessage;
		                }
		            });
	            }
	        },
	        submit: function() {
	            if (this.pages) {
	                let pages_selected = [];
		            this.pages.forEach(page => {
		                pages_selected.push(page.id);
		            })
		            this.pages_selected = pages_selected;
	            }
	            
	            var legacy_layout = { 'id': 0, 'class': ''};
	            if (this.legacy_layout != null) {
	                legacy_layout = {
	                    'id': this.legacy_layout.id,
	                    'class': this.legacy_layout['class']
	                };
	            }
	            
	            let promise = this.$cms.updateGabarit({
                	gabarit : {
		                id: this.id ?? 0,
		                name: this.name,
		                default: this.isDefault,
		                legacy_layout: legacy_layout
		            },
                	pages: this.pages_selected
	            });
	            promise.then((result) => {
	                if (result.error) {
	                    this.errorMessage = result.errorMessage;
	                }
	            });
	        },
	    	frameRemove: function(frame) {
				if (confirm(this.$cms.getMessage("layout_element_confirm_delete"))) {
					this.$cms.removeElementLayout({
						item: frame,
						parent: null,
						gabarit_id: this.$cms.container.data.item.id
					});
				}
	    	},
	    	addPage: function(pageId) {
	    		this.pages_selected.push(pageId);
	    		this.submit();
	    	},
	        openPageForm: function() {
	        	this.showPageForm = true;
	        },
	        closePageForm: function(event, submit) {
	        	var classList = event.target.classList;
	        	if(submit || classList.contains("portal-modal") || classList.contains("close-form")) {
	        		this.showPageForm = false;
	        	}
	        },
	        openSubNav: function(item) {
	        	this.$cms.openItem({
	        		item,
	        		name: item.name,
	        		type: "pageSubNav"
	        	});
	        	this.$cms.itemNavActive = 2;
	        },
	        duplicate: function() {
	            if (!this.isEntity) {
	                return false;
	            }
	            
	            this.$cms.duplicateGabarit(this.id).then(response => {
	                if (response) {
		                dojo.topic.publish('dGrowl', this.$cms.getMessage('duplicate_successfully'));
	                } else {
		                dojo.topic.publish('dGrowl', this.$cms.getMessage('duplicate_failed'));	                    
	                }
	            });
	        },
	        changeHeritage: function(gabarit) {
	            this.legacy_layout = gabarit || null;
	        },
		    removeLayout: function(index){
		    	this.$cms.removeLayout({
	            	gabarit : this.id,
	            	layout: index
	            });
		    }
	    }
	}
</script>