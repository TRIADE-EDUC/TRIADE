<template>
	<div class="portal-list">
		<div class="portal-frame-list">
			<h3 v-if="title" class="portal-list-title">{{ title }}</h3>
			<button v-if="add_msg && isAddable" type="button" class="bouton" @click="add">
				{{ add_msg }}
			</button>
		</div>
		<div class="portal-list-container">
			<p v-if="empty_msg && items.length == 0">{{ empty_msg }}</p>
			<div class="portal-list-item cursor-pointer" v-for="(item, key) in items" :key="key" @click="itemClicked(item)" :title="item.name">
				<span class="portal-list-item-title">{{ item.name }}</span>
				<i v-if="isRemovable" 
					:title="$cms.getMessage('delete')" 
					class="fa fa-times remove-icon"
					@click.stop="itemRemove(item)">
				</i>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
	    props: [
        	'list', 
        	'title', 
        	'empty_msg',
        	'removable',
        	'addable',
        	'add_msg',
       	],
	    data: function () {
	        return {
	            items: []
	        }
	    },
	    created: function() {
	        this.updateItems();
	    },
	    watch: {
	        'list': function(newValue, oldValue) {
	            this.list = newValue;
		        this.updateItems();
	        }
	    },
	    computed: {
	        isRemovable: function() {
	            return (this.removable == "true" || this.removable == true) ? true : false;
	        },
	        isAddable: function() {
	            return (this.addable == "true" || this.addable == true) ? true : false;
	        }
	    },
	    methods: {
	        add: function () {
	            if (this.isAddable) {	                
		            this.$emit('add', true);
	            }
	        },
	        itemClicked: function (item) {
	            this.$emit('itemClicked', item);
	        },
	        itemRemove: function (item) {
	            if (this.isRemovable) {	                
		            this.$emit('itemRemove', item);
	            }
	        },
	        updateItems: function() {
	            if (this.list instanceof Promise) {
		            this.list.then((result) => {
				        this.items = result;
				    })
		        } else {
			        this.items = this.list;	            
		        }
	        }
	    }
	}
</script>