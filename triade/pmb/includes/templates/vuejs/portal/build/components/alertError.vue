<template>
	<div class="portal-error" v-if="show">
		<p>{{ message }}</p>
		<i class="close fa fa-times" aria-hidden="true" @click="closeError"></i>
	</div>
</template>

<script>
	export default {
	    props: ['message'],
	    data: function () {
	        return {
	            show: true
	        }
	    },
	    watch: {
	        'errorMessage': function(newValue, oldValue) {
	            this.errorMessage = newValue;
		        this.openError();
	        }
	    },
	    methods: {
	        openError: function() {
	            this.show = true;
	        } ,
	        closeError: function() {
	            this.show = false;
	            this.$emit('close', true)
	        } 
	    }
	}
</script>