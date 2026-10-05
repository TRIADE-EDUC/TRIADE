<template>
	<div v-if="isActive" :class="classeCss">
		<attribute_form v-if="showAttributeForm"
			@closeAttributeForm="closeAttributeForm" 
			@update="update"
			:parentAuto="parent"
			:item="item"
			view="layout">
		</attribute_form>
		<span v-show="isHidden" class="content-background"></span>
		<div class="portal-zone-menu" v-if="root == 0">
			<p class="title">
				{{ item.name }} 
				<template v-if="attributes.length > 0">
					<i class="fa fa-tags" aria-hidden="true" :title="$cms.getMessage('attributes_on_item')"></i>
				</template>
			</p>
			<div class="parameters">
				<div class="global-parameters">
					<select name="tag" v-model="item.semantic.tag" @change="updateTag">
						<option v-for="(tag, key) in $cms.semantic" :key="key">{{ tag }}</option>
					</select>
					<common_params :id_tag="item.semantic.id_tag"
						:parent="currentParent" :placing_before="placingBefore"
						:children_parent="$parent.zone.children"
						@change_order="updateOrder"
						@change_parent="updateParent">
					</common_params>
					<div>
						<button class="bouton" @click="shareLayout">
							<i class="fa fa-share"></i>
						</button>
						<button :class="['bouton portal-zone-hidden', isHidden ? 'hidden-btn' : '']" @click="hide">
							<i :class="['fa', item.is_hidden ? 'fa-eye-slash' : 'fa-eye']"></i>
						</button>
						<button v-show="!isZoneOpac()" class="bouton" type="button" @click="remove" :title="deleteLabel" :disabled="!isRemovable">
							<i class="fa fa-trash"></i>
						</button>
					</div>
				</div>
				<class_css :classes="item.semantic.classes" @change_classes="changeClasses"></class_css>
				<div class="other-parameters">
					<button class="bouton" type="button" @click="openZoneForm(item.semantic.id_tag)">{{ $cms.getMessage("add_zone") }}</button>
					<button class="bouton" type="button" @click="openFrameForm(item.semantic.id_tag)">{{ $cms.getMessage("add_frame") }}</button>
					<button class="bouton" type="button" @click="openAttributeForm(item.semantic.id_tag)">{{ $cms.getMessage('attributes') }}</button>
				</div>
			</div>
		</div>
		<div class="portal-zone-container">
			<template v-for="(child, key) in item.children">
				<zone root="0" :key="`child_${key}`" :zone="child" 
					v-if="child.class.toLowerCase().includes('zone')" 
					@update="updateChild(key, $event)" 
					@updateParent="updateChildParent(key, $event)" 
					@updateOrder="updateChildOrder(key, $event)" 
					@updateTag="updateChildTag(key, $event)"
					@openFrameForm="openFrameForm"
					@openZoneForm="openZoneForm"
					@openAttributeForm="openAttributeForm"
					:parent="item.semantic.id_tag" 
					:nextChild="getNextChildren(key)" 
					:parentHidden="parentHidden ? parentHidden : item.is_hidden" 
					:showHidden="showHidden">
				</zone>
				<frame :key="`child_${key}`" :frame="child"
					v-else-if="child.class.toLowerCase().includes('frame')" 
					@update="updateChild(key, $event)" 
					@updateParent="updateChildParent(key, $event)" 
					@updateOrder="updateChildOrder(key, $event)" 
					@updateTag="updateChildTag(key, $event)"
					:parent="item.semantic.id_tag" 
					:nextChild="getNextChildren(key)" 
					:parentHidden="parentHidden ? parentHidden : item.is_hidden" 
					:showHidden="showHidden">
				</frame>
			</template>
		</div>
	</div>
</template>

<script>
	import frame from "./frame.vue";
	import class_css from "./classesCSS.vue";
	import common_params from "./commonParams.vue";
	import attribute_form from "./attributeForm.vue";
	
	export default {
		name: "zone",
		props: [
	    	'root', 
	    	'zone', 
	    	'parent', 
	    	'parentHidden', 
	    	'showHidden',
	    	'nextChild'
    	],
		components: {
			frame,
			class_css,
			common_params,
			attribute_form
		},
	    data: function () {
	        return {
	            item: {
					"class": "",
					id: 0,
					is_hidden: false,
					is_edited: false,
					name: "",
					children: [],
					semantic: {
						"class": "",
						classes: [],
						id: 0,
						id_tag: "",
						tag: "",
						attributes: []
					},
				},
	            classes: [],
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
	    watch: {
	        zone: function (newValue, oldValue) {
		        this.item = this.$cms.cloneObject(newValue);
	        },
	        parent: function (newValue, oldValue) {
		        this.currentParent = newValue;
	        },
	        nextChild: function (newValue, oldValue) {
		        this.placingBefore = newValue ? newValue.semantic.id_tag : '';
	        }
	    },
	    computed: {
		    isHidden: function() {
		        return !this.parentHidden && this.item.is_hidden;
		    },
		    isActive: function() {
		        return (!this.showHidden && !this.item.is_hidden) || this.showHidden;
		    },
		    isRemovable: function() {
		    	return !this.isZoneOpac() && !this.$cms.haveElementOpac(this.item);
		    },
		    deleteLabel: function() {
		    	return (!this.isRemovable) ? this.$cms.getMessage("confirm_remove_zone_has_element_opac") : "";
		    },
	        attributes: function () {
		        return this.item.semantic.attributes || [];
	        },
	        classeCss: function () {
	            let classeCss = "portal-zone";

	            if (this.item.is_edited) {
	                classeCss += " portal-zone-edited";
	            }
	            return classeCss;
	        }
	    },
	    methods: {
	        load: function() {
		        this.item = this.$cms.cloneObject(this.zone);
		        this.currentParent = this.parent;
		        this.placingBefore = this.nextChild ? this.nextChild.semantic.id_tag : '';
	        },
	        changeClasses(classes) {
	            this.item.semantic.classes = classes;
	            this.$cms.addZoneClasses(this.item, classes);
	            this.update();
			},
	        update() {
	            this.$emit('update', this.item);
			},
	        updateParent(newParent) {
			    this.currentParent = newParent;
	            this.$emit('updateParent', this.currentParent);
			},
			updateOrder(placingBefore) {
			    this.placingBefore = placingBefore;
			    let newIndex = null;
			    if (placingBefore != "") {
				    newIndex = this.$parent.zone.children.findIndex(child => child.semantic.id_tag == placingBefore);
			    } else {
			        newIndex = this.$parent.zone.children.length;
			    }
			    if (0 <= newIndex && newIndex <= this.$parent.zone.children.length) {
		            this.$emit('updateOrder', newIndex);	
			    }
			},
	        updateTag() {
	            this.$emit('updateTag', this.item.semantic.tag);
			},
			updateChild(index, child) {
			    this.item.children[index] = child;
			    this.update();
			},
			updateChildParent(indexElement, newParent) {
				var index = this.getPageOrGabarit();
				this.$cms.updateParent({
					parent: this.item,
					index_element: indexElement,
					new_parent: newParent,
					[index]: this.$root.container.data.item.id
				});
			},
			updateChildTag(indexElement, newTag) {
				var index = this.getPageOrGabarit();
				this.$cms.updateTagElementLayout({
					parent: this.item,
					index_element: indexElement,
					tag_element: newTag,
					[index]: this.$root.container.data.item.id
				});
			},
			updateChildOrder(indexElement, newIndexChild) {
				var index = this.getPageOrGabarit();
				this.$cms.updateParent({
					parent: this.item,
					index_element: indexElement,
					new_index_element: newIndexChild,
					new_parent: this.item.semantic.id_tag,
					[index]: this.$root.container.data.item.id
				});
			},
			getPageOrGabarit() {
				return this.$root.container.data.item.class.includes('PagePortalModel') ? 'page_id' : 'gabarit_id';
			},
			hide() {
				var index = this.getPageOrGabarit();
				this.$cms.hideElementLayout({
					item: this.zone,
					parent: this.parent,
					[index]: this.$root.container.data.item.id
				});
			},
			remove() {
				if (confirm(this.$cms.getMessage("layout_element_confirm_delete"))) {
					if(this.$cms.haveElementOpac(this.zone)) {
						confirm(this.$cms.getMessage("confirm_remove_zone_has_element_opac"));
						return false;
					}
					if(this.zone.children.length && !confirm(this.$cms.getMessage("confirm_remove_zone_has_children"))) {
						return false;
					}
					var index = this.getPageOrGabarit();
					this.$cms.removeElementLayout({
						item: this.zone,
						parent: this.parent,
						[index]: this.$root.container.data.item.id
					});
				}
			},
			isZoneOpac() {
				return this.zone.class.includes('ZoneOpacModel');
			},
			getNextChildren(currentIndex) {
			    var nextIndex = currentIndex + 1;
			    if (nextIndex == this.item.children.length) {
			        return null;
			    }
			    return this.item.children[nextIndex] ? this.item.children[nextIndex] : null;
			},
			openFrameForm(parent) {
				this.$emit('openFrameForm', parent);
			},
			openZoneForm(parent) {
				this.$emit('openZoneForm', parent);
			},
			openAttributeForm: function(parent) {
	        	this.showAttributeForm = true;
	        },
	        closeAttributeForm: function(event, submit) {
	        	var classList = event.target.classList;
	        	if(submit || classList.contains("portal-modal") || classList.contains("close-form")) {
	        		this.showAttributeForm = false;
	        	}
	        },
	        shareLayout: async function() {
	        	const shareLayoutResult = await this.$cms.shareLayout(this.$root.container.data.item, this.item);
	        	if (typeof showShareLayout == "function") showShareLayout(shareLayoutResult);
	        }
 	    }
	}
</script>