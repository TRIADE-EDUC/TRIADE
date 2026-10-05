<template>
	<div class="page-type">
		<cms_form v-if="showCmsForm" :data="cmsFormData" @closeCmsForm="closeCmsForm"></cms_form> 
		<div class="menu">
			<div class="check">
				<i class="fa fa-plus-square cursor-pointer" @click="checkAll"
					:title="$cms.getMessage('check_all')"></i> 
				<i class="fa fa-minus-square cursor-pointer" @click="uncheckAll"
					:title="$cms.getMessage('uncheck_all')"></i>
			</div>

			<select v-model="layout" @change="status = {}">
				<optgroup v-if="pageList.length != 0" :label="$cms.getMessage('heritage_label_group_page')">
					<option v-for="(page, key) in pageList" :key="`page_${key}`" :value="`page_${page.id}`">
						{{ page.name }}
					</option>
				</optgroup>
				<optgroup :label="$cms.getMessage('heritage_label_group_model')">
					<option 
						v-for="(gabarit, key) in $cms.gabarits" :key="key"
						:value="`gabarit_${gabarit.id}`">
							{{ gabarit.name }}
					</option>
				</optgroup>
			</select>
			<button type="button" 
				:class="['bouton', available ? 'cursor-pointer' : '']"
				:disabled="!available" @click="apply">
					{{ $cms.getMessage('apply_in_checkbox') }}
			</button>
			<button v-if="type==25" type="button" class="bouton cursor-pointer" @click="openCmsForm">
					{{ $cms.getMessage('cms_page_add_subtype') }}
			</button>
		</div>

		<fieldset class="pages">
			<legend class="title">{{ $cms.getMessage('pages_list') }}</legend>
			<p v-if="pages.length == 0">{{ $cms.getMessage('no_subpages_created') }}</p>
			<div v-else class="page" v-for="(page, index) in pages" :key="`page_${index}`">
				<div>
					<input :id="`page_${index}`" type="checkbox" value="1"
						:checked="checked.includes(page.id)"
						@change="changeCheck(page.id)">
				</div>
				<div class="page-info">
					<div class="page-name">
						<label :for="`page_${index}`" class="cursor-pointer">
							{{ page.name }}
						</label>
					</div>
					<div class="page-layout">
						<select @change="updateHeritage(page.id)" v-model="layouts[page.id]">
							<optgroup v-if="getPageList(page.id).length != 0" :label="$cms.getMessage('heritage_label_group_page')">
								<option v-for="(page, key) in getPageList(page.id)" :key="`page_${key}`" :value="`page_${page.id}`">
									{{ page.name }}
								</option>
							</optgroup>
							<optgroup :label="$cms.getMessage('heritage_label_group_model')">
								<option v-for="(gabarit, key) in $cms.gabarits" :key="`gabarit_${key}`" :value="`gabarit_${gabarit.id}`">
									{{ gabarit.name }}
								</option>
							</optgroup>
						</select>
						<img v-if="getImage(page.id)" class="icon" :src="getImage(page.id)" :alt="getImage(page.id)">
					</div>
				</div>
			</div>
		</fieldset>
	</div>
</template>

<script>
	import cms_form from './cmsPageForm.vue';
	export default {
	    props: ["data"],
	    components: {
	    	cms_form
	    },
	    data: function() {
	        return {
	            layout: "",
	            layouts: {},
	            type: "",
	            checked: [],
	            status: {},
	            errorMessage: "",
		        showCmsForm: false,
		        cmsFormData: {}
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
	        available: function() {
	            return (this.checked.length > 0);
	    	},
	        pages: function() {
	            const pages = this.$cms.pages.filter(page => (page.id != 0 && page.type == this.type));
	            for (var i = 0; i < pages.length; i++) {
	                this.layouts[pages[i].id] = this.getLayoutByPage(pages[i]);
	            }
	            return pages;
	    	},
	    	pageList: function() {
	    	    
	    	    var pageIds = [];
	    	    this.pages.forEach((page) => { pageIds.push(page.id)});
	    	    
	    	    let list = [];
   				this.$cms.pages.forEach((page) => {
   					if(!pageIds.includes(page.id)) {
   						
   					    let nextPage = page.parent_page;
   						let isLoop = false;
   						
   						while(nextPage && nextPage.id) {
   							if(pageIds.includes(nextPage.id)) {
   								isLoop = true;
   							}
   							nextPage = nextPage.parent_page;
   						}
   						
   						if(!isLoop) {
							list.push(page);
   						}
   					}
   				});
   				return list;
	    	}
	    },
	    methods: {
	        load: function() {
    		    this.status = {};
	            this.checked = [];
	            
	            if (this.data && this.data.item) {
		            this.type = this.data.item.type ?? first_type;
	            }
		        this.layout = `gabarit_${this.$cms.gabarits[0].id}`;
	        },
	        updateHeritage: async function(pageId) {
                var page = this.$cms.cloneObject(this.pages.find(page => (page.id == pageId)));
                const msg = this.$cms.getMessage('change_page_layout').replace('%s', page.name);
	            if (this.layouts[pageId] && confirm(msg)) {
	    		    this.status = {};
	                this.status[pageId] = "wait";
	                let tmp = this.layouts[pageId].split("_");
		            page = this.updatedPage(page, {
	                    id: tmp[1],
	                    "class": tmp[0] == "page" ? "Pmb\\CMS\\Models\\PagePortalModel" : "Pmb\\CMS\\Models\\GabaritLayoutModel",
		            });
	                this.status = await this.$cms.updatePages([page]);
	            }
	        },
	        apply: async function() {
    		    this.status = {};
	            
	            let tmp = this.layout.split("_");
	            const heritage = {
                    id: tmp[1],
                    "class": tmp[0] == "page" ? "Pmb\\CMS\\Models\\PagePortalModel" : "Pmb\\CMS\\Models\\GabaritLayoutModel",
	            };

	            var pages = this.$cms.cloneObject(this.pages.filter(page => (this.checked.includes(page.id))));
	            if (pages) {	                
		            for (var i = 0; i < pages.length; i++) {
		                this.status[pages[i].id] = "wait";
		        		pages[i] = this.updatedPage(pages[i], heritage)
		            }
		            
	                this.status = await this.$cms.updatePages(pages);
	            }
	        },
	        updatedPage: function(page, layout) {
	            if(layout['class'] == "Pmb\\CMS\\Models\\PagePortalModel") {
	        		page.parent_page = layout;
	        		page.gabarit_layout = {};
	        	} else {
	        		page.gabarit_layout = layout
	        		page.parent_page = {};
	        	}
	            return page;
	        },
	        checkAll: function() {
    		    this.status = {};
	            this.pages.forEach(page => {
	                if (!this.checked.includes(page.id)) {
		                this.checked.push(page.id)
		            }
	            });
	        },
	        uncheckAll: function() {
    		    this.status = {};
	            this.checked = [];
	        },
	        changeCheck: function(pageId) {
    		    this.status = {};
	            if (this.checked.includes(pageId)) {
	                const index = this.checked.findIndex(id => id == pageId);
	                this.checked.splice(index, 1);
	            } else {
	                this.checked.push(pageId);	                
	            }
	        },
	        getLayoutByPage: function(page) {
	        	if(page.gabarit_layout && page.gabarit_layout.id) {
	        		return `gabarit_${page.gabarit_layout.id}`;
	        	}
	        	if(page.parent_page && page.parent_page.id){
	        		return `page_${page.parent_page.id}`;
	        	}
        		return "";
	        },
	        getImage: function(pageId) {
	            if (this.status[pageId] == "wait") {
	                return this.$cms.getImage("patience.gif")
	            } else if (this.status[pageId] == true) {
	                return this.$cms.getImage("tick.gif")
	            } else if (this.status[pageId] == false) {
	                return this.$cms.getImage("error.png")
	            }
	            return false;
	        },
	        getPageList: function(pageId) {
	            let list = [];
   				this.$cms.pages.forEach((page) => {
   					if(pageId != page.id) {
   						
   					    let nextPage = page.parent_page;
   						let isLoop = false;
   						
   						while(nextPage && nextPage.id) {
   							if(nextPage.id == pageId) {
   								isLoop = true;
   							}
   							nextPage = nextPage.parent_page;
   						}
   						
   						if(!isLoop) {
							list.push(page);
   						}
   					}
   				});
   				return list;
	        },
	        openCmsForm: function(event, edit = false) {
	        	if(edit) {
	        		var id = parseInt(this.sub_type.toString().substr(2), 10);
		        	fetch('./ajax.php?module=cms&categ=pages&sub=edit&id=' + id, { method: 'GET' })
			            .then(response => {
	            			response.json().then(result => {
		  						this.cmsFormData = result
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
	        }
	    }
	}
</script>