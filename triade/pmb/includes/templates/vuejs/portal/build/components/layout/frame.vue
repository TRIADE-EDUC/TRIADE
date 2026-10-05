<template>
	<div v-if="isActive" class="portal-frame">
		<attribute_form v-if="showAttributeForm"
			@closeAttributeForm="closeAttributeForm" 
			:parentAuto="parent"
			@update="update"
			:item="item" view="layout"></attribute_form>
		<span v-show="isHidden" class="content-background"></span>
		<div class="portal-frame-menu">
			<p class="title">
				{{ item.name }}
				<template v-if="attributes.length > 0">
					<i class="fa fa-tags" aria-hidden="true" :title="$cms.getMessage('attributes_on_item')"></i>
				</template>
			</p>
			<div class="parameters">
				<div class="global-parameters">
					<select name="tag" v-model="item.semantic.tag" @change="updateTag">
						<option v-for="(tag, key) in $cms.semantic" :key="key">
							{{ tag }}
						</option>
					</select>
					<common_params :id_tag="item.semantic.id_tag"
						:children_parent="$parent.zone.children" :parent="currentParent"
						:placing_before="placingBefore" @change_order="updateOrder"
						@change_parent="updateParent"> 
					</common_params>
					<div>
						<button
							:class="['bouton portal-zone-hidden', !parentHidden || (!parentHidden && item.is_hidden) ? 'hidden-btn' : '']"
							@click="hide">
							<i :class="['fa', item.is_hidden ? 'fa-eye-slash' : 'fa-eye']"></i>
						</button>
						<button class="bouton" type="button"
							@click="edit">
							<i class="fa fa-edit"></i>
						</button>
						<button v-show="!isFrameOpac()" class="bouton" type="button"
							@click="remove">
							<i class="fa fa-trash"></i>
						</button>
					</div>
				</div>
				<class_css :classes="item.semantic.classes" :originClassCss="item.origin_class_css"
					@change_classes="changeClasses"></class_css>
				<div class="classes-parameters">
					<button class="bouton" type="button" @click="openAttributeForm(item.semantic.id_tag)">{{ $cms.getMessage('attributes') }}</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import class_css from "./classesCSS.vue";
	import common_params from "./commonParams.vue";
	import attribute_form from "./attributeForm.vue";
	export default {
		props: [
		    'frame', 
		    'parent', 
		    'parentHidden', 
		    'showHidden',
	    	'nextChild'
	    ],
		components: {
			class_css,
			common_params,
			attribute_form
		},
	    data: function () {
	        return {
	            item: {
					"class": "",
					classement: "",
					id: 0,
					is_hidden: false,
					name: "",
					semantic: {
						"class": "",
						classes: [],
						id: 0,
						id_tag: "",
						tag: "",
						attributes : []
					},
				},
	            currentParent: "",
	            placingBefore: "",
	            showAttributeForm : false
	        }
	    },
	    created: function() {
	        this.load();
	    },
	    beforeUpdate: function() {
	        this.load();
	    },
	    computed: {
		    isHidden: function() {
		        return !this.parentHidden && this.item.is_hidden;
		    },
		    isActive: function() {
		        return (!this.showHidden && !this.item.is_hidden) || this.showHidden;
		    },
	        attributes: function () {
		        return this.item.semantic.attributes || [];
	        }
	    },
	    methods: {
	        load: function() {
		        this.item = this.$cms.cloneObject(this.frame);
		        this.currentParent = this.parent;
		        this.placingBefore = this.nextChild ? this.nextChild.semantic.id_tag : '';
	        },
	        changeClasses(classes) {
	            this.item.semantic.classes = classes;
	            this.$cms.addFrameClasses(this.item, classes);
	            this.update();
			},
	        update() {
	            this.$emit('update', this.item)
			},
	        updateParent(newParent) {
			    this.currentParent = newParent;
	            this.$emit('updateParent', this.currentParent);
			},
			updateOrder(placingBefore) {
			    this.placingBefore = placingBefore;
			    if (placingBefore != "") {			        
				    var newIndex = this.$parent.zone.children.findIndex(child => child.semantic.id_tag == placingBefore);
			    } else {
			        var newIndex = this.$parent.zone.children.length;
			    }
			    if (0 <= newIndex && newIndex <= this.$parent.zone.children.length) {
		            this.$emit('updateOrder', newIndex);			        
			    }
			},
	        updateTag() {
	            this.$emit('updateTag', this.item.semantic.tag);
			},
			isNotMe(frame) {
				return frame.semantic.id_tag != this.item.semantic.id_tag;
			},
			isFrameOpac() {
				return this.frame.class.includes('FrameOpacModel');
			},
			pageOrGabarit() {
				return this.$root.container.data.item.class.includes('PagePortalModel') ? 'page_id' : 'gabarit_id';
			},
			edit() {
				this.$cms.openItem({
                    type: "frame",
                    name: this.frame.name,
                    item: this.frame,
                }, true);

				//this.$cms.itemNavActive = this.$cms.itemsNav.length-1;
			},
			hide() {
				var item = this.pageOrGabarit();
				this.$cms.hideElementLayout({
					item: this.frame,
					parent: this.parent,
					[item]: this.$root.container.data.item.id
				});
			},
			remove() {
				if (confirm(this.$cms.getMessage("layout_element_confirm_delete"))) {
					var item = this.pageOrGabarit();
					this.$cms.removeElementLayout({
						item: this.frame,
						parent: this.parent,
						[item]: this.$root.container.data.item.id
					});
				}
			},
			openAttributeForm: function(parent) {
        		this.showAttributeForm = true;
	        },
	        closeAttributeForm: function(event, submit) {
	        	var classList = event.target.classList;
	        	if(submit || classList.contains("portal-modal") || classList.contains("close-form")) {
	        		this.showAttributeForm = false;
	        	}
	    	}
	    }
	}
</script>