<template>
	<polyline :points="pointsPolyline" class="link"/>
</template>

<script>
	export default {
		name: "links",
		props: [
		    "item",
		    "child",
	    ],
		props: {
		    item: {
		        type: Object,
		        required: true
		    },
		    child: {
		        type: Object,
		        required: true
		    }
		},
	    computed: {
	        pointsPolyline: function () {
	            return `${this.item.x}, ${this.item.y}, ${this.item.x},${this.child.y} ${this.child.x}, ${this.child.y}`;
	        }
	    },
	}
</script>