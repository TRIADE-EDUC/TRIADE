<template>
	<div class="portal-nav-item">
		<h3 :class="['portal-nav-item-title', isActive ? 'active' : '']"
			@click="$emit('changeActive', index)">
			{{ item.title }}
		</h3>
		<span v-if="item && item.subNav" class="portal-sub-nav-icon cursor-pointer">				
			<i :title="$cms.getMessage('delete')" 
				class="fa fa-times remove-icon"
				@click.stop="$emit('closeSubNav', index)">
			</i>
		</span>
	</div>
</template>

<script>
	export default {
		props: ['item', 'isActive', 'index']
	}
</script>