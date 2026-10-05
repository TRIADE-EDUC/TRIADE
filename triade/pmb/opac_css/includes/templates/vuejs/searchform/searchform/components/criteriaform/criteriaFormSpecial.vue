<template>
	<div v-model="criteria_id" class="rmc_criteria_form_special">
        <div v-if="html" v-html="html"></div>
	</div>
</template>

<script>
export default {
	name: "criteriaFormSpecial",
	props : ['criteria', 'criteria_id', 'index' ],
	data: function(){
		return {
			html: ""
		}
	},
	mounted: function(){
		this.getSpecialField()
	},
	updated: function() {
		this.getSpecialField()
	},
	computed: {
	    name: function() {
	        return `field_${this.index}_${this.criteria_id}[]`;
	    },
		hiddenData: function(){
			var data = [];
			for(let index in this.criteria.VALUES){
				data.push({value: this.criteria.VALUES[index], name: this.name })
			}
			return data;
		}
	},
	methods: {
		getSpecialField: function(){
			if(this.criteria.RESPONSIVE){
				var data = new FormData();
				for(let input of this.hiddenData){
					data.append(input.name, input.value);
				}
				fetch("./ajax.php?module=ajax&categ=search_field&sub=special&type="+ this.criteria.TYPE +"&n=" + this.index, {
					method: 'POST',
					body: data
				}).then((response)=> {
					if (response.ok) {
						response.text().then((result)=> {
						    this.html = result;
					    });
					} else {
						console.error("Error search special ajax");
					}
				}).catch((error) => {
					console.error(error.message);
				});
			}
		}
	}
}
</script>