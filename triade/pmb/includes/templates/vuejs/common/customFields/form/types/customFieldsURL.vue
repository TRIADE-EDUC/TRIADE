<template>
	<div class="container">
		<div v-if="field.options[0].repeatable[0].value" class="row">
			<input @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
		<div v-for="(customValue, index) in values" class="row">
			<img v-if="customValue.img" :src="customValue.img"/>
			<input :id="field.name + index" v-model="customValue.value" type="text" :size="field.options[0].size[0].value"/>
			<input class="bouton" type='button' :value="field.msg.urlCheck" @click="cp_chklnk(index);"/>
			{{ field.msg.urlLinklabel }}<input :id="field.name +'_displayLabel'+ index" v-model="customValue.displayLabel" type="text" :size="field.options[0].size[0].value"/>
			{{ field.msg.urlLinkTarget }}<input :id="field.name +'_linkTarget'+ index" type='checkbox' v-model="customValue.linkTarget"/>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
			<input v-if="field.options[0].repeatable[0].value && index == values.length - 1" @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values", "img", "csrftokens"],
		methods : {
			cp_chklnk : function(index){
				this.values[index].img = '';
				if(this.values[index].value != ''){
					this.values[index].img = this.img.patience;
					var testlink = encodeURIComponent(this.values[index].value);
		 			var check = new http_request();
		 			var csrftokens = this.csrftokens[0];
					this.csrftokens.splice(0, 1);
					
		 			if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&timeout="'+ this.field.options[0].timeout[0].value +'"&link='+testlink+'&csrf_token='+csrftokens)){
						// alert(check.get_text());
						this.values[index].img = this.img.error;
					}else{
						var result = check.get_text();
						var type_status=result.substr(0,1);
						
				    	if(type_status == '2' || type_status == '3'){
							if((this.values[index].value.substr(0,7) != 'http://') && (this.values[index].value.substr(0,8) != 'https://')) {
								this.values[index].value = 'http://'+this.values[index].value;
							}
							this.values[index].img = this.img.tick;
						}else{
							this.values[index].img = this.img.error;
						}
					}
				}else{
					this.values[index].img = this.img.error;
				}
			} 
		}
	}
</script>