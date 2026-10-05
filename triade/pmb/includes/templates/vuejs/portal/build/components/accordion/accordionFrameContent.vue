<template>
	<div :class="['portal-accordion-sub-content', active ? 'active' : '']">
		<div :class="classes" :title="item.title" @click="openItemAccordion">
			<p :class="'portal-accordion-sub-title'">
				{{ item.title }}
			</p>
			<span class="portal-accordion-icon" v-if="!item.isTag">
				<button type="button" class="bouton cursor-pointer" @click.stop="editClassement">
					<i class="portal-icon fa fa-caret-down"></i>
				</button>
				<div class="classement" :style="styleClassement" v-if="showFormClassement" @click.stop="">
					<input type="text" 
						v-model.trim="item.classement" 
						list="classements" 
						name="classement" 
						id="classement"
						ref="input_classement"
						v-on:keyup.enter="saveClassement">
					<datalist id="classements">
						<option v-for="(classement, key) in $cms.framesClassements" :value="classement" :key="`classement_${key}`" />
					</datalist>
					<button type="button" class="bouton" @click="saveClassement">{{ $cms.getMessage('save') }}</button>
				</div>
			</span>
		</div>
		<template v-if="item.children">
			<accordion_frame_content v-for="(child, key) in item.children"
				 :active="active"
				 :item="child"
				 :key="`frame_content_${key}`">
			 </accordion_frame_content>
		</template>
		<p v-else>{{ $cms.getMessage('no_frame') }}</p>
	</div>
</template>

<script>
	export default {
        name: 'accordion_frame_content',
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
		    classes: function () {
		        let classes = 'portal-accordion-sub-header';
		        classes += this.item.isTag ? ' is-tag' : ' is-entity';
		        classes += this.item.isEdited ? ' is-edited' : '';
	            return classes;
		    },
		    title: function () {
	            return this.item.isEdited ? this.$cms.getMessage('is_edited') : "";
		    }
		},
		methods: {
			openItemAccordion: function () {
				if(!this.item.isTag) {
					this.$cms.openItem(this.item.data, true);
				}
			},
			editClassement: function(event) {
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
			saveClassement: function(event) {
			    this.showFormClassement = false;
			    this.$cms.editFrameClassement(this.item);
			}
		}
	}
</script>