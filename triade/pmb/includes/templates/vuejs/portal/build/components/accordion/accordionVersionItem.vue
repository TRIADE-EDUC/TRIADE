<template>
	<div :class="['portal-accordion-item', item.active ? 'active' : '']">
		<h3 class="portal-accordion-title cursor-pointer" @click="$emit('showItem', index)">
			{{ item.title }}
			<span class="portal-accordion-icon">
				<i class="portal-accordion-icon-clean fa fa-trash-o"
					@click="cleanVersions" 
					aria-hidden="true"
					:title="$cms.getMessage('clean_versions')"></i>
				<i class="portal-accordion-icon-add fa fa-refresh"
					@click="refreshVersions" 
					aria-hidden="true"
					:title="$cms.getMessage('resfresh_versions')"></i>
				<i @click="$emit('showItem', index)" 
					:class="['fa cursor-pointer', item.active ? 'fa-caret-down' : 'fa-caret-left']" 
					aria-hidden="true"></i>
			</span>
		</h3>
		<div v-show="item.active"
			 :class="['portal-accordion-content', item.active ? 'active' : '']"
			 @scroll="hiddenRenameFormVersion">
			<accordion_version_content 
				v-for="(child, key) in item.children" 
				:item="child"
				:key="key">
			</accordion_version_content>
		</div>
	</div>
</template>

<script>
	import accordion_version_content from './accordionVersionContent.vue'
	export default {
		props: ['item', 'index'],
		data: function () {
			return {
			    display_type : 0
			}    
		},
		components: {
		    accordion_version_content
		},
		methods: {
			hiddenRenameFormVersion: function(event) {
				window.dispatchEvent(new Event("hiddenRenameFormVersion"));
		    },
			refreshVersions: async function(event) {
		        event.stopPropagation();
				let versions = await this.$cms.fetchVersions();

				this.$set(this.item, "children", versions);
				this.$forceUpdate();
		    },
		    cleanVersions: async function(event) {
		        event.stopPropagation();
		        let cleaning = await this.$cms.cleanVersions();
		        console.log(cleaning);
		    },
		}
	}
</script>