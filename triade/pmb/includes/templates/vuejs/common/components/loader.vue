<template>
	<div class="loader" :title="message" v-if="show">
		<div class="loader-container">
			<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
			<span class="sr-only">{{ message }}</span>
		</div>
	</div>
</template>

<script>
	export default {
	    props: ['message'],
	    data: function () {
	        return {
	            show: false
	        }
	    },
	    created: function() {
		    window.addEventListener("showLoader", () => {this.showLoader()})
		    window.addEventListener("hiddenLoader", () => {this.hiddenLoader()})
	    },
	    methods: {
	        showLoader: function() {
	            this.show = true;
	        },
	        hiddenLoader: function() {
	            this.show = false;
	        }
	    }
	}
</script>