<template>
	<div :class="['portal-accordion-sub-content', active ? 'active' : '']">
		<div :class="classes" :title="item.title" @click="openItemAccordion">
			<p class="portal-accordion-sub-title">
				{{ item.title }}
			</p>
			<span class="portal-accordion-icon" :title="title">
				<i :class="['portal-icon', item.isEdited ? 'icon-edited' : '']"></i>
			</span>
		</div>
		<accordion_content v-for="(child, key) in item.children"
			 :active="active"
			 :item="child"
			 :key="`content_${key}`">
		 </accordion_content>
	</div>
</template>

<script>
	export default {
        name: 'accordion_content',
		props: ['active', 'item'],
		computed: {
		    classes: function () {
		        let classes = 'portal-accordion-sub-header';
		        classes += this.item.isTag ? ' is-tag' : ' is-entity';
		        classes += this.item.isEdited ? ' is-edited' : '';
		        classes += this.item.data ? ' cursor-pointer' : '';
	            return classes;
		    },
		    title: function () {
	            return this.item.isEdited ? this.$cms.getMessage('is_edited') : "";
		    }
		},
		methods: {
			openItemAccordion: function () {
				if (this.item.data) {
					this.$cms.openItem(this.item.data, true);
				}
			}
		}
	}
</script>