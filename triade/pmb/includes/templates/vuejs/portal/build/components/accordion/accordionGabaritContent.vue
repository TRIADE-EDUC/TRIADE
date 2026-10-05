<template>
	<div :class="['portal-accordion-sub-content', active ? 'active' : '']">
		<div :class="classes" :title="item.title" @click="openItemAccordion">
			<p :class="'portal-accordion-sub-title'">
				{{ item.title }}
			</p>
			<span class="portal-accordion-icon">
				<i :title="isUsedTitle" :class="['portal-icon', isUsed ? 'fa fa-check-circle-o' : '']"></i>
				<i :title="isEditedTitle" :class="['portal-icon', item.isEdited ? 'icon-edited' : '']"></i>
				<template v-if="!item.isTag">
					<button type="button" class="bouton cursor-pointer" @click.stop="editClassment">
						<i class="portal-icon fa fa-caret-down"></i>
					</button>
					<div class="classement" :style="styleClassement" v-show="showFormClassement" @click.stop="">
						<input type="text" 
							v-model.trim="item.classement"
							list="classements" 
							name="classement" 
							id="classement"
							ref="input_classement" 
							v-on:keyup.enter="saveClassment">
						<datalist id="classements">
							<option v-for="(classement, key) in $cms.gabaritsClassements" :value="classement" :key="`classement_${key}`" />
						</datalist>
						<button type="button" class="bouton" @click="saveClassment">{{ $cms.getMessage('save') }}</button>
					</div>
				</template>
			</span>
		</div>
		<template v-if="item.children">
			<accordion_gabarit_content v-for="(child, key) in item.children"
				 :active="active"
				 :item="child"
				 :key="`gabarit_content_${key}`">
			 </accordion_gabarit_content>
		</template>
		<p v-else>{{ $cms.getMessage('no_frame') }}</p>
	</div>
</template>

<script>
	export default {
        name: 'accordion_gabarit_content',
		props: ['active', 'item'],
		data: function() {
			return {
			    showFormClassement: false,
			    styleClassement: ""
			}  
		},
		created: function() {
		    document.body.addEventListener('click', () => {this.showFormClassement = false;})
		    window.addEventListener("hiddenEditClassment", () => {this.showFormClassement = false;})
		},
		computed: {
		    gabarit: function () {
	            return (this.item.data && this.item.data.item) ? this.item.data.item : {};
		    },
		    classes: function () {
		        let classes = 'portal-accordion-sub-header';
		        classes += this.item.isTag ? ' is-tag' : ' is-entity';
		        classes += this.item.isEdited ? ' is-edited' : '';
	            return classes;
		    },
		    isUsed: function () {
	            return this.$cms.getPagesUsingGabarit(this.gabarit.id).length > 0;
		    },
		    isUsedTitle: function () {
	            return this.isUsed ? this.$cms.getMessage('gabarit_used') : "";
		    },
		    isEditedTitle: function () {
	            return this.item.isEdited ? this.$cms.getMessage('is_edited') : "";
		    }
		},
		methods: {
			openItemAccordion: function () {
				if(!this.item.isTag) {
					this.$cms.openItem(this.item.data, true);
				}
			},
			getPages: function() {
			    return this.$cms.pages ?? [];
			},
			editClassment: function(event) {
			    const isHidden = !this.showFormClassement;
				window.dispatchEvent(new Event("hiddenEditClassment"));
				if (isHidden) {
				    let target = event.target.nodeName == "I" ? event.target.parentNode : event.target;
				    let node = document.querySelector('div.portal-accordion-content.active');
				    let scrollTop = node ? node.scrollTop : 0;
				    let top = (target.offsetTop + target.offsetHeight) - scrollTop;
				    this.styleClassement = `top: ${top}px; left: ${target.offsetLeft}px`;
				    this.showFormClassement = true;
	                this.$nextTick(() => {
	                	this.$refs.input_classement.focus();
					});
				}
			},
			saveClassment: function(event) {
			    this.showFormClassement = false;
			    this.$cms.editGabaritClassement(this.item);
			}
		}
	}
</script>