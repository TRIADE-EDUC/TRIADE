<template>
	<div class="portal-list">
		<frame_form v-if="showFrameForm" @closeFrameForm="closeFrameForm" view="page"></frame_form> 
		<div class="portal-frame-list">
			<h4 v-show="title" class="portal-frame-list-title">{{ title }}</h4>
			<button v-show="is_entity" type="button" class="bouton" @click="openFrameForm">{{ $cms.getMessage('add_frame') }}</button>
		</div>
		<div class="frame-classements">
			<fieldset class="frame-classement" v-for='(classement, index) in classements' :key="`classement_${index}`">
				<legend class="frame-classement-title">{{ classement.title }}</legend>
				<div class="frame-classement-container">
					<div class="frame-classement-item cursor-pointer" v-for="(item, key) in classement.children" :key="key" @click="itemClicked(item)">
						<span class="portal-list-item-title" :title="item.name">{{ item.name }}</span>
						<i v-if="isRemovable(item)" 
							:title="$cms.getMessage('delete')" 
							class="fa fa-times remove-icon"
							@click.stop="itemRemove(item)">
						</i>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</template>

<script>
	import frame_form from "./layout/frameForm.vue";
	export default {
	    props: ['list', 'title', 'is_entity'],
		components: {
			frame_form
		},
	    data: function () {
	        return {
	            items: [],
	            showFrameForm: false,
	        }
	    },
	    created: function() {
	        this.updateItems();
	        if(this.is_entity) {
		        this.$cms.refreshZones(this.$root.container.data.item);
	        }
	    },
	    watch: {
	        'list': function(newValue, oldValue) {
	            this.list = newValue;
		        this.updateItems();
	        }
	    },
	    computed: {
	        classements: function() {
	    	    const default_classement = this.$cms.getMessage('default_classement');
	    		
	    		let classements = {};
	    		for (const index in this.items) {
	    			const frame = this.items[index];

    				const classement = (frame.classement && frame.classement != "") ? frame.classement  : default_classement;
	    			if (!classements[classement]) {
	    			    classements[classement] = {
	    					title: classement,
	    					children: []
	    				}
	    			}
	    			classements[classement].children.push(frame);
	    		}
	    		
	    		return classements;
	    	}
	    },
	    methods: {
	        itemClicked: function (item) {
	            this.$cms.openItem({
                    type: "frame",
                    name: item.name,
                    item: item,
                }, true);
	        },
	        updateItems: function() {
	            if (this.list instanceof Promise) {
		            this.list.then((result) => {
				        this.items = result;
				    })
		        } else {
			        this.items = this.list;	            
		        }
	        },
	        openFrameForm: function() {
	        	this.showFrameForm = true;
	        },
	        closeFrameForm: function(event, submit) {
	        	var classList = event.target.classList;
	        	if(submit || classList.contains("portal-modal") || classList.contains("close-form") || classList.contains("fa-times")) {
	        		this.showFrameForm = false;
	        	}
	        },
	        itemRemove: function(frame) {
	        	if (this.isRemovable(frame)) {
		        	this.$emit('frameRemove', frame);
	        	}
	        },
	        isRemovable: function(frame) {
	        	return frame['class'].toLowerCase().includes('framecmsmodel');
	        }
	    }
	}
</script>