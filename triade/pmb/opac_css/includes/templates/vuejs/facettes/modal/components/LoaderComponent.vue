<template>
	<div class="loader_background" v-if="display">
		<div class="spinner"></div>
	</div>
</template>

<script>
import Vue from "vue";
Vue.prototype.showLoader = () => {
    window.dispatchEvent(new Event("show_loader"));
};
Vue.prototype.hiddenLoader = () => {
    window.dispatchEvent(new Event("hidden_loader"));
};

export default {
	name : "loader",
	data : function() {
		return {
			display : false
		}
	},
	mounted: function() {
		window.addEventListener("show_loader", this.show.bind(this));
		window.addEventListener("hidden_loader", this.hidden.bind(this));
	},
	destroy: function() {
		window.removeEventListener("show_loader", this.show.bind(this));
		window.removeEventListener("hidden_loader", this.hidden.bind(this));
	},
	methods : {
	    show: function() {
			this.display = true;
	    },
	    hidden: function() {
			this.display = false;
	    }
	}
}
</script>