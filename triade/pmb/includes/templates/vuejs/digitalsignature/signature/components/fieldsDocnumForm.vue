<template>
	<div id="listFields">
		<div class='row' v-if="fields">
			<template v-if="showField">
				<div class="row uk-clearfix">
					<label for="signature_fields" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_signature_fields") }}</label>
				</div>
				<div class="row uk-clearfix">
					<select id="signature_fields" class='saisie-50em' v-model="field">
						<option v-for="(field, index) in fields" :value="index">{{ field.label }}</option>
					</select>
				</div>	
				<template v-if="showSubField">
					<div class="row uk-clearfix">
						<label for="signature_fields" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_signature_sub_fields") }}</label>
					</div>
					<div class="row uk-clearfix">
						<select id="signature_fields" class='saisie-50em' v-model="subfield" required>
							<option v-for="(subfield, index) in fields[field].subfields" :value="index">{{ subfield }}</option>
						</select>
					</div>	
				</template>
				<br/>
				<i v-if="field" class="fa fa-plus" @click="updateField()"></i>
			</template>
			<div class='row uk-clearfix'>
				<label for="signature_list_fields" class='etiquette'>{{ pmb.getMessage("digital_signature", "admin_signature_list_fields") }} </label>
			</div>
			<div class='row uk-clearfix'>
				<ul>
					<li v-for="(data, index) in tab">
						{{ data.field.label }} 
						<template v-if="data.subField.label">
							/ {{ data.subField.label }}
						</template>
						<i class="fa fa-trash" @click="delField(index)"></i>
					</li>
				</ul>
			</div>
		</div>
		
	</div>
</template>

<script>
	export default {
		props : ["pmb", "action", "img", "tab"],
		
		data: function () {
			return {
				fields : [],
				field : 0,
				subfield : 0
			}
		},
		created : function(){
			var data = {type:"docnum"};
			var url = "./ajax.php?module=admin&categ=digital_signature&sub=signature&action=getdata&data="+JSON.stringify(data);
			
			fetch(url, {
				method : 'GET',
			}).then((response) => {
				if (response.ok) {
					response.json().then((data) => {
						this.fields = data;
					});
				} else {
					console.log(this.pmb.getMessage('digital_signature', 'admin_digital_signature_no_response'));
				}
			}).catch(function(error) {
				console.log(this.pmb.getMessage('digital_signature', 'admin_digital_signature_error_fetch') + error.message);
			});
		},
		computed : {
			showField: function(){
				if(this.fields){
					if((this.fields instanceof Array)) {
						return true
					} else if (Object.keys(this.fields).length > 0){
						return true
					}
				}
				return false;
			},
			showSubField: function(){
				if(this.fields){
					if(this.fields[this.field]){
						if(this.fields[this.field].subfields){
							if(Object.keys(this.fields[this.field].subfields).length > 0 ){
								return true
							}
						}
					}
				}
				return false;
			}
		},
		methods: {
			updateField : function(event){
				this.$emit('updateField', {
					"field" : {
						"label" : this.fields[this.field].label, 
						"id" : this.field
					}, 
					"subField" : {
						"label" : this.fields[this.field].subfields[this.subfield], 
						"id" : this.subfield
					}, 
				})
			},
			
			delField : function (index){
				this.tab.splice(index, 1);
			},
		}
	}
</script>