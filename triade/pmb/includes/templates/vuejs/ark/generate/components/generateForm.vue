<template>
	<div>
		<form class="form-admin">
			<h3>
				{{ pmb.getMessage("ark", "admin_ark_generate_management") }}
			</h3>
			<div class='form-contenu'>
				<div v-if="'start' == action || 'generate_done' == action" class="row" style='width:560px'>
					<table border='0' class='' style='width:560px' cellpadding='0'>
						<tr>
							<td class='jauge' style='width:100%'>
								<div class='jauge'>
									<img :src='img.jauge' :style='"height:16px; width:"+state+"px"'>
								</div>
							</td>
						</tr>
					</table>
					<div class='center'>{{percent}}%</div>
				</div>
				<div class='row' v-if="'generate_done' == action">
					<label>{{count}} {{ pmb.getMessage("ark", "admin_ark_entities_generated") }}</label>
				</div>
				<div class='row'>
					<div class="left">
					    <input  @click="generate" class="bouton" type="button" :value="pmb.getMessage('ark', 'admin_ark_generate')" :disabled="('start' == action || 'generate_done' == action)"/>
				    </div>
				</div>
		    </div>
	    </form>
	</div>
</template>

<script>

	export default {
		
		props : ["pmb", "action", "img", "count", "next", "start"],
		data: function () {
			return {
				state : 0,
				percent : 0,
			}
		},
		components : {
		},
		methods:{
			generate : function() {
				var data = {
						count : this.count,	
						start : this.next,	
					};
				var dataString = JSON.stringify(data);
				document.location = './admin.php?categ=ark&sub=generate&action=start&data='+dataString;
			},
			generateDone : function() {
				var data = {
						count : this.count,	
						start : this.next,	
					};
				var dataString = JSON.stringify(data);
				document.location = './admin.php?categ=ark&sub=generate&action=generate_done&data='+dataString;
			},
			nextGenerate : function() {
				if (this.next < this.count) {
					this.generate();
				} else {
					this.generateDone();
				}
			}
		},
		created : function() {
		},
		mounted : function() {
			if (this.action == "start") {
				if (this.count) {
					this.state = Math.floor(this.start / (this.count / 560));
					this.percent = Math.floor((this.start/this.count)*100);
				}
				setTimeout(this.nextGenerate, 500);
			}
			if (this.action == "generate_done") {
				this.state = 560;
				this.percent = 100;
			}
		}
	}
</script>