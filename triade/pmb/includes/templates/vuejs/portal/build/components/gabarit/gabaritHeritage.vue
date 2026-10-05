<template>
	<div class="portal-heritage">
		<div v-if="heritageList.length > 0">
			<select  id="heritage-selector" name="heritage_selector" v-model="heritageSelected" @change="changeHeritage">
				<option value="">{{ $cms.getMessage("make_no_heritage") }}</option>
				<option v-for="(gabarit, key) in heritageList" :key="key" :value="gabarit.id">
					{{ gabarit.name }}
				</option>
			</select>
		</div>
		<div v-else>
			<p>{{ $cms.getMessage("no_heritage") }}</p>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['data'],
	    data: function () {
	        return {
	            heritageSelected: ""
	        }
	    },
	    mounted: function() {
	        this.heritageSelected = this.currentHeritage;
	    },
	    watch: {
	        data: function(newValue, OldValue) {
	            if (newValue.item && newValue.item.legacy_layout &&newValue.item.legacy_layout.id) {
	                this.heritageSelected = newValue.item.legacy_layout.id;
				} else {
				    this.heritageSelected = "";
				}
	        }
	    },
	    computed: {
	        currentHeritage: function () {
				if (this.data.item && this.data.item.legacy_layout && this.data.item.legacy_layout.id) {
				    return this.data.item.legacy_layout.id;
				}
				return "";
	        },
	        id: function () {
				return (this.data.item && this.data.item.id) ? this.data.item.id : 0;
	        },
	    	heritageList: function() {
	    		let list = [];
	    		if (this.id) {
    				this.$cms.gabarits.forEach((gabarit) => {
    					if (this.id != gabarit.id) {
    						let nextGabarit = gabarit.legacy_layout;
    						let isLoop = false;
    						while (nextGabarit && nextGabarit.id) {
    							if (nextGabarit.id == this.id) {
    								isLoop = true;
    								break;
    							}
    							nextGabarit = nextGabarit.legacy_layout;
    						}
    						if (!isLoop) {
	    						list.push(gabarit);
    						}
    					}
    				});
    				return list;
	    		} else {
	    			return this.$cms.gabarits;
	    		}
	    	}
	    },
	    methods: {
	        changeHeritage: function() {
	            var newHeritage = null
	            if (this.heritageSelected) {	                
	                newHeritage = this.$cms.gabarits.find((gabarit) => gabarit.id == this.heritageSelected);
	            }
	            this.$emit('change', newHeritage);
	        }
	    }
	}
</script>