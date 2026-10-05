<template>
	<div>
		<form class="form-admin" action="" method="POST" @submit.prevent="submit">
			<template  v-for="(params, type) in parameters">
				<h2 v-if="type == 'pmb'">
					{{ messages.get("common", "gestion") }}
				</h2>
				<h2 v-else>
					{{ messages.get("common", "opac") }}
				</h2>
				<div class='form-contenu'>
					
					<div class='row'>
						<template v-for="(value, label) in params" >
							<div class="row">
			             		<div class="colonne3">
									<label :for="type+'_'+label">{{ messages.get("thumbnail", label) }}</label>
								</div>
								 <div class="colonne_suite">
									<input v-if="label == 'img_cache_size' || label == 'img_cache_clean_size'" :id="type+'_'+label" :name="type+'_'+label" class="saisie-5em" type="number" min='0' v-model="parameters[type][label]"/>
									<select v-else-if="label == 'img_cache_type'" :id="type+'_'+label" :name="type+'_'+label" v-model="parameters[type][label]">
										<option value="webp">WebP</option>
										<option value="png">PNG</option>
									</select>
									<input v-else :id="type+'_'+label" class="saisie-50em" :name="type+'_'+label" type="text" v-model="parameters[type][label]"/>
								</div>
							</div>
						</template>
					</div>
				</div>
				<hr v-if="type == 'pmb'"/>
			</template>
			<div class='row'>
				<br />
				<div class="left">
				   <button class="bouton" type="submit">
						{{ messages.get("common", "submit") }}
					</button>
			    </div>
				<div class="right">
					<button class="bouton" type="button" @click="clean">
						{{ messages.get("common", "clean_cache") }}
					</button>
			    </div>
			</div>
	    </form>
	</div>
</template>

<script>
	export default {
		props : ["action", "parameters"],
		data: function () {
			return {
			    data: {
			    }
			}
		},
		components : {
		},
		methods: {
			submit: async function () {
				let response = await this.ws.post("cache", "save", {values: this.parameters});
				if (response.error) {
                    if (response.errorMessage) {
                        console.error(response.errorMessage);
                    }
                    this.notif.error(this.messages.get("common", "failed_save"));
                } else {
                    this.notif.info(this.messages.get("common", "success_save"));
                }
			},
			clean: async function () {
				if (confirm(this.messages.get("common", "clean_cache_confirm"))) {
					let response = await this.ws.get("cache", "clean");
					if (response.error) {
	                    if (response.errorMessage) {
	                        console.error(response.errorMessage);
	                    }
	                    this.notif.error(this.messages.get("common", "failed_operation"));
	                } else {
	                    this.notif.info(this.messages.get("common", "successful_operation"));
	                }
				}
			},
		}
	}
</script>