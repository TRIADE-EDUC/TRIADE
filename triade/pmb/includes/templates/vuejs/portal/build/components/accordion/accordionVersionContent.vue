<template>
	<div :class="['portal-accordion-sub-content', item.versions || active ? 'active' : '']">
		<div :class="classes" @click="openOrChangeVersion" :style="isUsedVersion ? 'font-weight: 600' : ''">
			<p class="portal-accordion-sub-title portal-accordion-sub-title-versions" :title="item.name">
				<span>{{ item.name }}{{ id }}</span>
				<span v-if="!item.versions">{{ item.create_at }}</span>
			</p>
			<!-- <span v-if="item.versions" class="portal-accordion-icon">
				<i :class="['portal-accordion-caret fa cursor-pointer', item.versions && active ? 'fa-caret-down' : 'fa-caret-left']" 
				aria-hidden="true"></i>
			</span> -->
			<span class="portal-accordion-icon" v-if="!item.versions">
				<button type="button" class="bouton cursor-pointer" @click.stop="openRenameFormVersion">
					<i class="portal-icon fa fa-caret-down"></i>
				</button>
				<div class="classement" :style="styleRenameFormVersion" v-if="showRenameFormVersion" @click.stop="">
					<input type="text" 
						v-model.trim="item.name"
						list="classements" 
						name="classement" 
						id="classement"
						ref="input_rename_version"
						v-on:keyup.enter="renameVersion">
					<button type="button" class="bouton" @click="renameVersion">{{ $cms.getMessage('save') }}</button>
				</div>
			</span>
		</div>
		<template v-if="item.versions && active">
			<accordion_frame_content v-for="(version, key) in item.versions"
				 :active="active"
				 :item="version"
				 :key="`frame_content_${key}`">
			 </accordion_frame_content>
		</template>
	</div>
</template>

<script>
	export default {
        name: 'accordion_frame_content',
		props: ['item'],
		data: function() {
			return {
				active: this.item.versions ? false : true,
				showRenameFormVersion: false,
				styleRenameFormVersion: ""
			}
		},
		created: function() {
		    document.body.addEventListener('click', () => {this.showRenameFormVersion = false;})
		    window.addEventListener("hiddenRenameFormVersion", () => {this.showRenameFormVersion = false;})
		},
		computed: {
			id: function() {
				return this.item.versions ? "" : " (" + this.item.id + ")";
			},
		    classes: function () {
		        let classes = 'portal-accordion-sub-header portal-accordion-sub-header-versions';
		        classes += this.item.versions ? ' is-tag' : ' is-entity';
	            return classes;
		    },
			isUsedVersion: function() {
				return this.$parent.item.version_num && this.item.id == this.$parent.item.version_num;
			}
		},
		methods: {
			openOrChangeVersion: async function() {
				if(this.item.versions) {
					this.active = !this.active;
				} else {
					if(this.item.id == this.$parent.item.version_num) {
						return;
					}

					if(confirm(this.$cms.getMessage('confirm_change_version'))) {
						await this.$cms.switchVersion(this.$parent.item.id, this.item.id);
						location.reload();
					}
				}
			},
			openRenameFormVersion: function(event) {
			    const isHidden = !this.showRenameFormVersion;
				window.dispatchEvent(new Event("hiddenRenameFormVersion"));
				if (isHidden) {
				    let target = event.target.nodeName == "I" ? event.target.parentNode : event.target;
				    let node = document.querySelector('div.portal-accordion-content.active');
				    let scrollTop = node ? node.scrollTop : 0;
				    let top = (target.offsetTop + target.offsetHeight) - scrollTop;
				    this.styleRenameFormVersion = `top: ${top}px; left: ${target.offsetLeft}px`;
				    this.showRenameFormVersion = true;
	                this.$nextTick(() => {
	                	this.$refs.input_rename_version.focus();
					});
				}
			},
			renameVersion: async function() {
				await this.$cms.renameVersion(this.item.id, this.item.name);
				this.showRenameFormVersion = false;
			}
		}
	}
</script>