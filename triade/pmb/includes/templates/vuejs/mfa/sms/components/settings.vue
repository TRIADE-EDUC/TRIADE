<template>
    <div>
		<form id="mfa-form-sms" @submit.prevent="submit($event)">
			<div class="mfa-sms-text row">
				<div class=" right">
					<a href="./admin.php?categ=param&form_type_param=empr&form_sstype_param=sms_activation#justmodified" target="_blank">
						{{ messages.get("mfa", "sms_param_link") }}
					</a>
				</div>
			</div>
			<div v-for="(element, index) in sms" :key="index" class="mfa-sms row">
				<h2>{{ element.context }}</h2>
				<div class="form-contenu" :id="'mfa-form-sms-' + index">
					<div class="row">
						<div class="mfa-sms-label colonne4">
							<label :for="'content_' + index">{{ messages.get("mfa", "mail_content") }} :</label>
						</div>
						<div class="mfa-sms-input colonne_suite">
							<div class="row">
								<select :name="'selvars_' + index" v-model="selvarSelected[index]">
									<option value="0">{{ messages.get("mfa", "mail_choice_selvars") }}</option>
									<optgroup v-for="group in selvars[index]" :label="group.msg">
										<option v-for="element in group.elements" :value="'!!' + element.code + '!!'">
											{{ element.msg }}
										</option>
									</optgroup>
								</select>
								<button class="bouton" @click.prevent="insertSelvar(index)" :disabled="selvarSelected[index] == 0">
									{{ messages.get("mfa", "mail_insert_selvars") }}
								</button>
							</div>
							<div class="row">
								<textarea :id="'content_' + index"
										:name="'content_' + index"
										:ref="'content_' + index"
										v-model="element.content"
										rows="20" cols="100"
										data-translation-fieldname="content">
								</textarea>
							</div>
						</div>
					</div>
				</div>
				<hr v-if="isLastSms(element)">
			</div>
			<div class="row">
				<input type="submit" class="bouton" :value="messages.get('common', 'submit')">
			</div>
		</form>
	</div>
</template>

<script>
    export default {
		props : ["sms", "selvars"],
		data: function() {
			return {
				selvarSelected: { "gestion": 0, "opac": 0 }
			}
		},
		methods: {
			isLastSms: function(element) {
				return Object.values(this.sms)[Object.values(this.sms).length-1].context != element.context;
			},
			submit: async function(e) {
				let response = await this.ws.post('MFASms', 'save', this.formattedFormData(e));
				this.notif.info(this.messages.get('common', 'success_save'));
			},
			insertSelvar: function(index) {
				if(this.selvarSelected[index]) {
					let element = this.$refs["content_" + index][0];
					let start = element.selectionStart;
					let start_text = this.sms[index].content.substring(0, start);
					let end_text = this.sms[index].content.substring(start);

					this.sms[index].content = start_text + this.selvarSelected[index] + end_text;
				}
			},
			formattedFormData: function(e) {
				const formData = new FormData(e.target);

				let object = {};
				formData.forEach((value, key) => object[key] = value);

				return { objects: this.sms, formData: object };
			}
		}
	}
</script>