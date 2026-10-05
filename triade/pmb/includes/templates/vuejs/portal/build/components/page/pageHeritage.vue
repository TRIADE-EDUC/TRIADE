<template>
	<div class="portal-heritage">
		<select id="heritage-selector" name="heritage_selector" v-model="heritageSelected" @change="changeHeritage" required>
			<optgroup v-if="pageList.length != 0" :label="$cms.getMessage('heritage_label_group_page')">
				<option v-for="(page, key) in pageList" :key="key" :value="`page_${page.id}`">{{ page.name }}</option>
			</optgroup>
			<optgroup :label="$cms.getMessage('heritage_label_group_model')">
				<option v-for="(gabarit, key) in $cms.gabarits" :key="key" :value="`gabarit_${gabarit.id}`">{{ gabarit.name }}</option>
			</optgroup>
		</select>
	</div>
</template>

<script>
	export default {
		props: ['data'],
	    data: function () {
	        return {
	            heritageSelected: "",
	            parent_page: 0,
	            gabarit: 0
	        }
	    },
	    mounted: function() {
	        this.$nextTick(() => {
            	this.load();
			});
	        
	    },
	    watch: {
	        data: function(newValue, OldValue) {
            	this.load();
	        }
	    },
	    computed: {
	        pageId: function () {
	            return (this.data && this.data.item) ? this.data.item.id : 0;
	        },
	        isEntity: function () {
	            return (this.data && this.data.item && this.data.item.id != 0);
	        },
	    	pageList: function() {
	    		let list = [];
	    		if (this.pageId) {
    				this.$cms.pages.forEach((page) => {
    					if (this.pageId != page.id) {
    						let nextPage = page.parent_page;
    						let isLoop = false;
    						while (nextPage && nextPage.id) {
    							if (nextPage.id == this.pageId) {
    								isLoop = true;
    							}
    							nextPage = nextPage.parent_page;
    						}
    						if (!isLoop) {
	    						list.push(page);
    						}
    					}
    				});
    				return list;
	    		} else {
	    			return this.$cms.pages;
	    		}
	    	},
	    	currentHeritage: function() {
	        	if (this.gabarit) {
	        		return `gabarit_${this.gabarit}`;
	        	}
	        	
	        	if(this.parent_page){
	        		return `page_${this.parent_page}`;
	        	}
	        	return false;
	        }
	    },
	    methods: {
	        load: function() {
	            if (this.isEntity) {
	                if (this.data.item.parent_page && this.data.item.parent_page.id) {	                    
		                this.parent_page = this.data.item.parent_page.id
	                } else {	                    
		                this.gabarit = this.data.item.gabarit_layout.id
	                }
		        } else {
	                this.parent_page = 0;
	                this.gabarit = 0;
		        }
	            
	            if (this.currentHeritage === false) {
		        	this.setDefault();
		        } else {	            
			        this.heritageSelected = this.currentHeritage;
		        }
	        },
	        setDefault: function() {
	            const gabarit = this.$cms.getDefaultGabarit();
	        	this.heritageSelected = `gabarit_${gabarit.id}`;
	        	this.changeHeritage();  
	        },
	        changeHeritage: function() {
	            var heritageSplitted = this.heritageSelected.split('_');
	            var heritage = null;
	            
	            if (this.heritageSelected.toLowerCase().includes('page')) {
	                heritage = this.$cms.pages.find(page => page.id == heritageSplitted[1]);
	            } else {
	                heritage = this.$cms.gabarits.find(gabarit => gabarit.id == heritageSplitted[1]);	                
	            }
	            this.$emit('change', heritage);
	        }
	    }
	}
</script>