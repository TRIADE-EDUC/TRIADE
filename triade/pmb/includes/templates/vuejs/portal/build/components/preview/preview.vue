<template>
	<div class="portal-preview">
		<context_form
			v-if="showFormContext"
 			:contexts="item.contexts"
 			:bookmarkContext="item.bookmark_context"
			@closeContextForm="closeContextForm"
			@saveContext="editContext"
 			@removeContext="removeContext"
 			@sendContext="sendContext"
 			@changeBookmark="changeBookmark">
		</context_form>
		<div class="portal-preview-menu">
			<button type="button" class="bouton" @click="openContextForm" :disabled="item.contexts.length==0">
				<i class="fa fa-cogs" aria-hidden="true" :title="$cms.getMessage('context_manage_title')"></i>
			</button>
			<select v-model="context" @change="setContext">
				<option value="" disabled>
					{{ $cms.getMessage('preview_default_context') }}
				</option>
				<option v-for="(context, index) in item.contexts" :value="index" :key="index">
					{{ context.name }}
				</option>
			</select>
			<label for="preview-url" class="cursor-pointer">{{ $cms.getMessage("portal_url_preview") }}</label>
			<input id="preview-url" name="preview_url" type="text" v-model.trim="url" v-on:keyup.enter="send" :placeholder="$cms.url_base_opac">
			<button type="button" class="bouton" :title="$cms.getMessage('portal_send_preview')" @click="send">
				<i class="fa fa-arrow-right" aria-hidden="true"></i>
			</button>
			<button type="button" class="bouton" :title="$cms.getMessage('portal_reload_preview')" @click="reload">
				<i class="fa fa-refresh" aria-hidden="true"></i>
			</button>
			<button type="button" class="bouton" @click="home">
				<i class="fa fa-home" aria-hidden="true"></i>
			</button>
			<div class="add-context">
				<button type="button" class="bouton" @click.stop="showAddFormContext" :title="getSaveContextMessage()" :disabled="getContextPages().length == 0">
					<i class="fa fa-floppy-o" aria-hidden="true"></i>
				</button>
				<div class="context-form" v-show="showForm" @click.stop="">
					<select v-model="newContextPage">
						<option v-for="(page, index) in getContextPages()" :value="index">{{ page.name }}</option>
					</select>
					<input type="text" ref="context_input"
						v-model="newContext.name"
						v-on:keyup.enter="saveContext"
						:placeholder="$cms.getMessage('context_name')">
					<button type="button" class="bouton" @click="saveContext">
						{{ $cms.getMessage('save') }}
					</button>
				</div>
			</div>
		</div>
		<p class="preview-url-error" v-if="!valid_url">{{ $cms.getMessage('preview_url_not_valid') }}</p>
		<div class="portal-preview-frame">
			<form ref="formContext" :action="generateUrlContext()" method="post" target="preview_frame" style="border: 0px;margin: 0;background: none;">
				<input type="hidden" v-for="(value, prop) in getPOSTContext()" :key="prop" :name="prop" :value="value">
			</form>
			<iframe name="preview_frame" id="preview_frame" @load="iframeLoaded"></iframe>
		</div>
	</div>
</template>

<script>
	import context_form from "./contextForm.vue";
	export default {
	    props: ["data"],
		components: {
			context_form
		},
	    data: function () {
	        return {
				url: "",
				home_url: "",
				init: false,
				context: "",
				applyContext: false,
				showForm: false,
				showFormContext: false,
				currentContext: {
				    name: "",
				    value: "",
				    url: ""
				},
				newContextPage: 0,
				newContext: {
				    name: "",
				    value: "",
				    url: ""
				},
	        }
	    },
	    mounted: function() {
		    window.addEventListener('click', () => {this.showForm = false;})
	    	this.url = this.base_url;
	    	this.home_url = this.base_url;

	    	if (this.item.bookmark_context !== null && this.item.contexts[this.item.bookmark_context]) {
	    	    this.context = this.$cms.cloneObject(this.item.bookmark_context);
	    	    this.$nextTick(() => {
		    	    this.setContext(true);
				});
	    	} else {
		    	this.getIframe().src = this.initUrl;
	    	}
	    },
	    computed: {
	    	item: function() {
	    		return this.$cms.cloneObject(this.$cms.container.data.item);
	    	},
	    	base_url: function() {
	    	    var sub_type = this.$cms.portal.sub_types.find(subtype => subtype.value == this.item.sub_type);
				return (sub_type && sub_type.url) ? sub_type.url : this.$cms.url_base_opac;
	    	},
	    	valid_url: function() {
	    	    var url = this.base_url;
	    	    if (this.currentContext.url != "") {
	                url = this.generateUrlContext();
	            }

	    	    if (this.data.item.sub_type == 2801) {
	    	        // 2801 == Page d'accueil
	    	        return url == this.$cms.url_base_opac
	    	    }
				return url != this.$cms.url_base_opac;
	    	},
	    	initUrl: function() {

  			    const urlBase = new URL(this.base_url);
  			    var urlSearch = new URLSearchParams(urlBase.search);
  			    var url = this.base_url.replace(urlBase.search, "");

  			    if (!urlSearch.has('database')) {
					urlSearch.append('database', this.$cms.portal.database);
  			    }
  			    if (!urlSearch.has('cms_build_activate')) {
					urlSearch.append('cms_build_activate', '1');
  			    }
  			    if (!urlSearch.has('opac_view')) {
					urlSearch.append('opac_view', '-1');
  			    }

  			    return url + '?' + urlSearch.toString();
	    	},
	    },
	    methods: {
	        getPOSTContext: function() {
	            if (this.currentContext.value == "") {
	                return {};
	            }

				const valueDecode = JSON.parse(this.currentContext.value) ?? {};
  			    return valueDecode['post'] ?? {};
	    	},
	        generateUrlContext: function() {

	            if (this.currentContext.url == "") {
	                return this.$cms.url_base_opac;
	            }

  			    const valueDecode = JSON.parse(this.currentContext.value);
  			    if (typeof valueDecode.shorturl != "undefined" && valueDecode.shorturl != "") {
  			        return valueDecode.shorturl;
  			    }

  			    const urlBase = new URL(this.currentContext.url);
  			    var urlSearch = new URLSearchParams(urlBase.search);
  			    const url = this.$cms.url_base_opac.replace(urlBase.search, "");
  			    if (typeof url == "undefined" || url == "") {
					return this.$cms.url_base_opac;
  			    }

				for (var prop in valueDecode['get']) {
				    if (!urlSearch.has(prop)) {
						urlSearch.append(prop, valueDecode['get'][prop]);
	  			    }
				}

				if (!this.init) {
				    if (!urlSearch.has('database')) {
						urlSearch.append('database', this.$cms.portal.database);
	  			    }
	  			    if (!urlSearch.has('cms_build_activate')) {
						urlSearch.append('cms_build_activate', '1');
	  			    }
	  			    if (!urlSearch.has('opac_view')) {
						urlSearch.append('opac_view', '-1');
	  			    }
				}

  			    return url + '?' + urlSearch.toString();
	    	},
	    	getIframe: function() {
	    		return document.getElementById('preview_frame') ?? undefined;
	    	},
	    	reload: function() {
	    	    if (this.currentContext.url && this.currentContext.url == this.getIframe().contentWindow.location.href) {
	    	        this.setContext(true);
	    	    } else {
		    		this.getIframe().contentDocument.location.reload(true);
	    	    }
	    	},
	    	send: function() {
	    	    this.context = "";
    			this.getIframe().src = this.url;
	    	},
	    	home: function() {
	    	    this.context = "";
    			this.getIframe().src = this.home_url;
	    	},
	    	getPageTitle: function() {
	    	    if (typeof this.getIframe() == "undefined") {
	    	        return "";
	    	    }
	    		const title =  this.getIframe().contentDocument.querySelector('title');
	    		if (title) {
	    		    return title.innerText ?? "";
	    		}
    		    return "";
	    	},
	    	getCmsBuildInfo: function() {
	    	    if (typeof this.getIframe() == "undefined") {
	    	        return "";
	    	    }
	    		const input =  this.getIframe().contentDocument.getElementById('cms_build_info');
	    		if (input) {
	    		    return input.value ?? "";
	    		}
    		    return "";
	    	},
	    	iframeLoaded: function () {
	    	    this.url = ""; // Permet de recaculer le template

  			    if (this.init) {
	  				this.url = this.getIframe().contentWindow.location.href;
  			    } else {

					if (this.item.bookmark_context !== null && this.item.contexts[this.item.bookmark_context]) {
	  			    	this.url = this.currentContext.url;
					} else {
	  			    	this.url = this.base_url;
					}
  			    	this.init = true;
  			    }

                this.$nextTick(() => {
                	if(this.getContextPages().length == 0){
                		this.showForm = false;
                	}
	  			    this.newContext.name = this.getPageTitle();
	  			    this.newContext.value = this.getCmsBuildInfo();
	  			    this.newContext.url = this.url;
				});
  			},
  			showAddFormContext: function () {
  			    this.showForm = !this.showForm;
  			    this.$nextTick(() => {
  			        if (this.showForm) {
  			            this.$refs.context_input.focus();
  			        }
  			    })
  			},
  			setContext: function (force = false) {
  			    if (force || confirm(this.$cms.getMessage('preview_confirm_apply_context'))) {
					this.currentContext = this.$cms.cloneObject(this.item.contexts[this.context]);
					const valueDecode = JSON.parse(this.currentContext.value);
	  			    if (typeof valueDecode.shorturl != "undefined" && valueDecode.shorturl != "") {
						this.getIframe().src = valueDecode.shorturl;
	  			    } else {
		                this.$nextTick(() => {
							this.$refs.formContext.submit();
						});
	  			    }
  			    } else {
  			        this.context = "";
  			    }
  			},
  			saveContext: async function () {
  			  	this.showForm = false;
  			    this.newContext.url = this.url;

  			  	const context = this.$cms.cloneObject(this.newContext);
  			  	if (!context.value) {
  			  	    return false;
  			  	}

  			  	const contextPages = this.getContextPages();
  			  	if (contextPages.length <= 0 && !contextPages[this.newContextPage]) {
  			  	    return false;
  			  	}

  			  	const contextPage = contextPages[this.newContextPage];
  			  	const result = await this.$cms.model.pageSaveContext(contextPage.id, context)
  			  	if (result.error) {
  			  	    return false;
  			  	}


				var pageIndex = this.$cms.pages.findIndex(page => page.id == contextPage.id);
				this.$cms.pages[pageIndex].contexts.push(context);

				this.newContext = {
				    name: this.getPageTitle(),
				    value: this.getCmsBuildInfo(),
				    url: this.newContext.url
				};

				// this.newContext.name = this.getPageTitle();
				// this.newContext.value = this.getCmsBuildInfo();
  			},
	        closeContextForm: function() {
        		this.showFormContext = false;
	        },
	        openContextForm: function(parent) {
	        	if(typeof parent === 'string') {
	        		this.parent = parent;
	        	}
	        	this.showFormContext = true;
	        },
  			editContext: async function (index, context) {
  			  	const newContext = this.$cms.cloneObject(context);
  			  	if (!newContext.value) {
  			  	    return false;
  			  	}

  			  	const result = await this.$cms.model.pageEditContext(this.item.id, {index_context: index, context: newContext})
  			  	if (result.error) {
  			  	    return false;
  			  	}
				var pageIndex = this.$cms.pages.findIndex(page => page.id == this.item.id);
				if (this.item.bookmark_context == index) {
					this.$cms.pages[pageIndex].bookmark_context = null;
  			  	}
				this.$cms.pages[pageIndex].contexts.splice(index, 1, newContext);
  			},
  			removeContext: async function (index) {
  			  	const result = await this.$cms.model.pageRemoveContext(this.item.id, {index_context: index})
  			  	if (result.error) {
  			  	    return false;
  			  	}

				var pageIndex = this.$cms.pages.findIndex(page => page.id == this.item.id);
				this.$cms.pages[pageIndex].contexts.splice(index, 1);
				if(this.context == index) {
					this.context = "";
					this.currentContext = {
					    name: "",
					    value: "",
					    url: ""
					};
				}

				if(!this.$cms.pages[pageIndex].contexts.length) {
					this.showFormContext = false;
				}
  			},
  			sendContext: function(index) {
  				this.showFormContext = false;
  				this.context = index;
		        const context = this.item.contexts[this.context];
				this.currentContext = context;
                this.$nextTick(() => {
					this.$refs.formContext.submit();
				});
  			},
  			getContextPages: function() {
	    	    const cms_build_infos_string = this.getCmsBuildInfo();
	    	    if (cms_build_infos_string) {
		    	    const pages = this.$cms.cloneObject(this.$cms.pages);
		    	    const cms_build_infos = JSON.parse(cms_build_infos_string);
		    		return pages.filter(page => page.type == cms_build_infos.type && page.sub_type == cms_build_infos.subType);
	    	    }
	    	    return [];
	    	},
	    	changeBookmark: async function(indexBookmark) {
  			  	if (typeof this.item.contexts[indexBookmark] == "undefined") {
  			  	    return false;
  			  	}

  			  	const result = await this.$cms.model.pageBookmarkContext(this.item.id, {index_context: indexBookmark})
  			  	if (result.error) {
  			  	    return false;
  			  	}

				var pageIndex = this.$cms.pages.findIndex(page => page.id == this.item.id);
				if (this.item.bookmark_context == indexBookmark) {
					this.$cms.pages[pageIndex].bookmark_context = null;
  			  	} else {
					this.$cms.pages[pageIndex].bookmark_context = indexBookmark;
  			  	}
	    	},
	    	getSaveContextMessage : function() {
	    		if(this.getContextPages().length > 0) {
	    			return this.$cms.getMessage('context_save');
	    		}
	    		return this.$cms.getMessage('context_save_unavailable');
    		}
	    }
	}
</script>