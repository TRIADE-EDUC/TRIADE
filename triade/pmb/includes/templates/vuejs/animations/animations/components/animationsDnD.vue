 <template>
	<div class="container">
 		<div class="form-contenu">
			<h3>{{ pmb.getMessage("animation", "list_animation_dndAnimation") }}</h3>
			<div class="row uk-clearfix">
				<table class="uk-table uk-table-small uk-table-striped uk-table-middle">
					<tbody>
						<tr>
							<VueNestable
									v-model="animationItems"
									:max-depth="9"
									:hooks="{
										'beforeMove': saveOrder
										}"
									key-prop="key"
									children-prop="nested"
								>
								<td class="row" slot-scope="{ item }">
									<VueNestableHandle :item="item">
										<i class="fa fa-bars" /><span>
										{{ item.name }} 
										<template v-if="item.event.startDate == item.event.endDate">
										  ({{ item.event.startDate }})
										  </template>
										  <template v-else>
										  ({{ item.event.startDate }} - {{ item.event.endDate }})
										  </template>
										</span>
									</VueNestableHandle>
								</td>
							</VueNestable>
						</tr>
					</tbody>
				</table> 
			</div>
		</div>
	</div>
 </template>

<script>

	export default {
		props : ["animations", "pmb"],
		data: function () {
			return {
				hover : -1,
				animationItems : this.animations,
			}
		},
		methods: {
			saveOrder ({ dragItem, pathFrom, pathTo }) {
 				var idChildren = dragItem.id;
				var tempAnimation = null;
				
 				for (var i = 0; i < pathTo.length-1; i++) {
 				  var key = pathTo[i];
 				    if (!tempAnimation) {
 				    	tempAnimation  = this.animationItems[key]
 				    }else{
 				    	tempAnimation = tempAnimation.nested[key]
 				    }
 				}
 				
 				if(typeof tempAnimation === 'undefined' || tempAnimation == null){
 					var idParent = 0
 				}else{
 					var idParent = tempAnimation.id
 				}

 				let url = "./ajax.php?module=animations&categ=animations&action=saveParentChild";
				var data = new FormData();
				data.append('data', JSON.stringify({'idChildren' : idChildren , 'idParent' : idParent}));
 				fetch(url, {
					method: 'POST',
					body: data
				}).then(function(response) {
					if (response.ok) {
						response.text().then(function(animations) {
							animations = JSON.parse(animations);
					    });
					} else {
						console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
					}
				}).catch(function(error) {
					console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
				}); 
			  }
		},
	}
</script>
