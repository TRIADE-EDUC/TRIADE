<template>
	<div class="role-loader" :title="messages.get('common', 'wait')" v-if="show">
		<div class="role-loader-container">
			<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
			<span class="sr-only">{{ messages.get('common', 'wait') }}</span>
		</div>
	</div>
</template>

<script>
	export default {
	    props: [],
	    data: function () {
	        return {
	            show: false
	        }
	    },
	    created: function() {
		    window.addEventListener("showLoader", () => {this.display()})
		    window.addEventListener("hiddenLoader", () => {this.hide()})
	    },
	    methods: {
	        display: function() {
	            this.show = true;
	        },
	        hide: function() {
	            this.show = false;
	        }
	    }
	}
</script>