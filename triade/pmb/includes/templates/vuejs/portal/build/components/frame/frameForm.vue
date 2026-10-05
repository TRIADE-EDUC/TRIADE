<template>
	<div>
		<!-- On retire le bouton qui vide pas correctement le cache d'un seul cadre car son hash est calculé sur URL
		<div class="portal-form-group-btn" v-if="this.isEntity && !this.isOpacFrame">
			<button class="bouton clear-btn" @click="cleanCache" type="button">{{ $cms.getMessage('frame_clean_cache') }}</button>
		</div> -->
				
		<div class="portal-form-group" v-if="errorMessage">
			<alert_error :message="errorMessage" @close="errorMessage = ''"></alert_error>
		</div>
		
		<add_form v-if="showForm" 
			@closeFrameForm="closeForm"
			@submit="formSubmit"
			:id_tag="data.item.semantic.id_tag"
			:init_name="name"
			:pageList="pagesWithoutFrame"
			:gabaritList="gabaritWithoutFrame"
			:title="modalTitle">
		</add_form>
		<template v-if="isOpacFrame">
			<div class="portal-form-group">
				<label for="frame-name" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("gabarit_name") }}</label>
				<input id="frame-name" name="frame_name" type="text" :value="name" readonly="readonly">
				<div>
					<list 
						:list="getPageList()" 
						:title="$cms.getMessage('pages_list')" 
						:empty_msg="$cms.getMessage('no_pages_using_frame')"
						:add_msg="$cms.getMessage('add_page')"
						@add="addPage"
						:removable="false">
					</list>
					<list 
						:list="getGabaritList()" 
						:title="$cms.getMessage('gabarits_list')" 
						:empty_msg="$cms.getMessage('no_gabraits_using_frame')"
						:add_msg="$cms.getMessage('add_gabarit')"
						@add="addGabarit"
						:removable="false">
					</list>
				</div>
			</div>
		</template>
		<template v-else-if="isEntity && !isOpacFrame">
			<div id="frame_container"><!-- div utiliser pour DOJO --></div>
			<div>
				<list 
					:list="pageList" 
					:title="$cms.getMessage('pages_list')" 
					:empty_msg="$cms.getMessage('no_pages_using_frame')"
					:removable="true"
					:addable="pageAddable"
					:add_msg="$cms.getMessage('add_page')"
					@add="addPage"
					@itemRemove="removePage"
					@itemClicked="openPage">
				</list>
				<list 
					:list="gabaritList" 
					:title="$cms.getMessage('gabarits_list')" 
					:empty_msg="$cms.getMessage('no_gabraits_using_frame')"
					:removable="true"
					:addable="gabaritAddable"
					:add_msg="$cms.getMessage('add_gabarit')"
					@add="addGabarit"
					@itemRemove="removeGabarit"
                    @itemClicked="openGabarit">
				</list>
			</div>
		</template>
		<template v-else>
			<div id="add_frame_container" v-if="addForm"><!-- div utiliser pour DOJO --></div>
			<template v-else>
				<label for="frame-module" class="portal-form-group-title cursor-pointer">{{ $cms.getMessage("frame_module") }}</label>
				<p class="portal-module" v-for="(module, key) in $cms.modules" :key="key" @click="getForm(key)">{{ module.name }}</p>
			</template>
		</template>
	</div>
</template>

<script>
	import add_form from "./addForm.vue";
	import alert_error from "../alertError.vue";
	import list from "../list.vue";
	
	export default {
	    props: ["data"],
	    components: {
	        alert_error,
	        list,
	        add_form
	    },
	    data: function() {
	        return {
		        id: 0,
		        name: "",
		        errorMessage: "",
		        pageList: [],
		        gabaritList: [],
		        pagesWithoutFrame: [],
		        gabaritWithoutFrame: [],
		        showForm: false,
		        addForm: false,
		        modalTitle: "",
		        events: {
		            "save_module": (event) => {this.save(event.detail)},
		            "save_new_module": (event) => {this.save_new_module(event.detail)},
		            "delete_module": () => {this.remove()},
		            "cancel_module": () => {this.cancel()},
		            "cancel_new_module": () => {this.cancel_new_module()},
		        }
	        }
	    },
	    mounted: function() {
            this.load()
            
            for (event in this.events) {
			    window.addEventListener(event, this.events[event]);
            }
	    },
	    destroyed: function() {
            for (event in this.events) {
			    window.removeEventListener(event, this.events[event]);
            }
	    },
	    watch: {
	        "data" : function(newValue, oldValue) {
	            this.load()
	        }
	    },
	    computed: {
	        pageAddable: function() {
	            return (this.getPagesWithoutFrame().length > 0) ? true : false;
	        },
	        gabaritAddable: function() {
	            return (this.getGabaritsWithoutFrame().length > 0) ? true : false;
	        },
	        isEntity: function () {
	            return (this.data && this.data.item && this.data.item.id != 0) ?? false;
	        },
	        isOpacFrame: function () {
	            return (this.isEntity && this.data.item && this.data.item['class'] && this.data.item['class'] == "Pmb\\CMS\\Models\\FrameOpacModel") ?? false;
	        }
	    }, 
	    methods: {
	        getForm: function(moduleName) {
	            if (typeof cms_build_load_module != "function") {
	                throw "cms_build_load_module is not a function!";
	            }
	            
	            if (moduleName == "") {
	                return false;
	            }

	            // On supprime tout les widgets DOJO qui ont été créé
	            destroyWidget();
	            this.addForm = true;
	            
            	this.$nextTick(() => {
		            cms_build_load_module(moduleName, "get_form", this.id, this.$cms.getMessage("cms_build_modules"));
            	})
	        },
	        closeError: function() {
                this.errorMessage = "";
	        },
	        load: async function() {
	            // On supprime tout les widgets DOJO qui ont été créé
	            destroyWidget();
	            
	            if (this.isEntity) {
		            this.id = this.data.item.id;
		            this.name = this.data.item.name;
		            if(!this.isOpacFrame) {
		            	this.$nextTick(() => {
		            	    let module = this.data.item.semantic.id_tag.replace(/_[0-9]+/, '');
				            edit_module(module, this.data.item.semantic.id_tag);
		            	})
		            }
		            
		            this.pageList = await this.getPageList();
		            this.gabaritList = await this.getGabaritList();
		        } else {
		            this.id = 0;
		            this.name = "";
		            
		            this.pageList = [];
		            this.gabaritList = [];
		        }
	        },
	        remove: function() {
	        	this.$cms.updateFrame();
	        	this.$cms.resetNav();
	        	this.$cms.clearContainer();
	        	this.$cms.frameRemove(this.data.item);
	        },
	        save: async function(data) {
	            await this.$cms.updateFrame();
	            
	            if (this.isEntity && data && (this.data.item.semantic.id_tag != data.dom_id)) {
                    destroyWidget();
                    
	                // On a dupliquer le cadre
	                const frame = this.$cms.frames.find(frame => frame.name == data.name);
	                if (frame) {
	                    const item = {
	                        item: frame,
	                        name: frame.name,
	                        type: "frame"
	                    };
	                    this.$cms.updateContainer(item);
	                } else {
	                    // On n'a pas retrouver le nouveau cadre
	                    // On vide tout car des champs sont modifiés par dojo à la sauvegarde/dupli
		    			this.$cms.resetNav();
		                this.$cms.clearContainer();
	                }
	            }

				this.cleanAllCache();
	        },
	        save_new_module: async function(data) {
	            // On vide la page
    			this.$cms.resetNav();
                this.$cms.clearContainer();
                
	            await this.$cms.updateFrame();
				
	         	// Si on trouve le nouveau cadre créer on ouvre le formulaire
                const frame = this.$cms.frames.find(frame => frame.semantic.id_tag == data.dom_id);
                if (frame) {
                    const item = {
                        item: frame,
                        name: frame.name,
                        type: "frame"
                    };
                    this.$cms.openItem(item);
                } else {
		            destroyWidget();
                }
	        },
	        cancel: function() {
	        	this.$cms.resetNav(true);
	            this.$cms.clearContainer();
	        },
	        cancel_new_module: function() {
	            this.addForm = false;
            	this.$nextTick(() => {
    	            // On supprime tout les widgets DOJO qui ont été créé
    	            destroyWidget();
            	})
	        },
	        // cleanCache: function() {
	        //     if (this.isEntity && !this.isOpacFrame) {	                
		    //         let promise = this.$cms.model.clearCacheFrame(this.data.item.semantic.id_tag);
		    //         promise.then((response) => {
		    //     		dojo.topic.publish('dGrowl', response.msg);
		    //         })
	        //     }
	        // },
			cleanAllCache: function() {
				if (this.isEntity && !this.isOpacFrame) {	                
		            this.$cms.model.clearCache();
	            }
			},
	        getPageList: function() {
	            return this.$cms.model.getPagesUsingFrame({
	                idTag: this.data.item.semantic.id_tag ?? ""
	            }) || [];
	        },
	        getGabaritList: function() {
	            return this.$cms.model.getGabaritsUsingFrame({
	                idTag: this.data.item.semantic.id_tag ?? ""
	            }) || [];	            
	        },
	        removePage: async function(page) {
	            const msg = this.$cms.getMessage('confirm_remove_frame_in_layout').replace('%s', page.name);
	            if (confirm(msg)) {	                
		            const result = await this.$cms.pageRemoveFrame(page.id, this.data.item.semantic.id_tag);
		            if (!result.error) {
			            this.pageList = await this.getPageList();
			            this.gabaritList = await this.getGabaritList();
			            this.pagesWithoutFrame = this.getPagesWithoutFrame();
	                }
	            }
	        },
	        removeGabarit: async function(gabarit) {
	            const msg = this.$cms.getMessage('confirm_remove_frame_in_layout').replace('%s', gabarit.name);
	            if (confirm(msg)) {	 
		            const result = await this.$cms.gabaritRemoveFrame(gabarit.id, this.data.item.semantic.id_tag);
		            if (!result.error) {	                    
			            this.pageList = await this.getPageList();
			            this.gabaritList = await this.getGabaritList();
			            this.gabaritWithoutFrame = this.getGabaritsWithoutFrame();
	                }
	            }
	        },
	        addPage: function() {
	            this.modalTitle = this.$cms.getMessage('add_frame_page');
	            this.pagesWithoutFrame = this.getPagesWithoutFrame();
	            this.gabaritWithoutFrame = [];
	            this.showForm = true;
	        },
	        addGabarit: function() {
	            this.modalTitle = this.$cms.getMessage('add_frame_gabarit');
	            this.gabaritWithoutFrame = this.getGabaritsWithoutFrame();
	            this.pagesWithoutFrame = [];
	            this.showForm = true;	            
	        },
	        closeForm: function() {
	            this.showForm = false;	            
	        },
	        getPagesWithoutFrame: function() {
	            var pageIds = [];
	            this.pageList.forEach(page => pageIds.push(page.id));
				return this.$cms.pages.filter(page => pageIds.includes(page.id) == false);
	        },
	        getGabaritsWithoutFrame: function() {
	            var gabaritIds = [];
	            this.gabaritList.forEach(gabarit => gabaritIds.push(gabarit.id));
				return this.$cms.gabarits.filter(gabarit => gabaritIds.includes(gabarit.id) == false);
	        },
	        formSubmit: async function() {
	            this.pageList = await this.getPageList();
	            this.gabaritList = await this.getGabaritList();
	            this.closeForm();
	        },
	        openPage: function (eventPage) {
	            let page = this.$cms.pages.find(page => page.id == eventPage.id);
                this.$cms.openItem({
                    type: "page",
                    name: page.name,
                    item: page,
                    subNav: false
                }, true);
	        },
	        openGabarit: function (eventGabarit) {
	            let gabarit = this.$cms.gabarits.find(gabarit => gabarit.id == eventGabarit.id);
	            this.$cms.openItem({
                    type: "gabarit",
                    name: gabarit.name,
                    item: gabarit,
                }, true);
	        }
	    }
	}
</script>