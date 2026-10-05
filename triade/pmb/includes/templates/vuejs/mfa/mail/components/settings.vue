<template>
    <form @submit.prevent="submit($event)">
		<div v-for="(mail, index) in mails" :key="index" class="mfa-mails row">
			<h2>{{ mail.context }}</h2>
			<div class="form-contenu" :id="'mfa-form-mail-' + index">
				<div class="row mfa-mail-row">
					<div class="mfa-mail-label colonne4">
						<label for="sender">{{ messages.get("mfa", "mail_sender") }} :</label>
					</div>
					<div class="mfa-mail-text colonne_suite">
						<span name="sender">{{ senders[index] }}</span>
						<a href="./admin.php?categ=mails&sub=settings" target="_blank">
							{{ messages.get("mfa", "mail_param_link") }}
						</a>
					</div>
				</div>
				<div class="row mfa-mail-row">
					<div class="mfa-mail-label colonne4">
						<label :for="'mailtpls_' + index">{{ messages.get("mfa", "mail_template") }} :</label>
					</div>
					<div class="mfa-mail-input colonne_suite">
						<select :name="'mailtpls_' + index" v-model="templateSelected[index]">
							<option value="0">{{ messages.get("mfa", "mail_choice_template") }}</option>
							<option v-for="mailtpl in mailtpls" :value="mailtpl.id">
								{{ mailtpl.name }}
							</option>
						</select>
						<button class="bouton" @click.prevent="insertTemplate(index)" :disabled="templateSelected[index] == 0">
							{{ messages.get("mfa", "mail_insert_template") }}
						</button>
					</div>
				</div>
				<div class="mfa-mail-main">
					<div class="row mfa-mail-row">
						<div class="mfa-mail-label colonne4">
							<label :for="'object_' + index">{{ messages.get("mfa", "mail_object") }} :</label>
						</div>
						<div class="mfa-mail-input colonne_suite">
							<input class="saisie-50em"
								   type="text"
								   :id="'object_' + index"
								   :name="'object_' + index"
								   v-model="mail.object"
								   data-translation-fieldname="object" />
						</div>
					</div>
					<div class="row mfa-mail-row">
						<div class="mfa-mail-label colonne4">
							<label :for="'content_' + index">{{ messages.get("mfa", "mail_content") }} :</label>
						</div>
						<div class="mfa-mail-input colonne_suite">
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
										v-model="mail.content"
										rows="20" cols="100"
										data-translation-fieldname="content">
								</textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr v-if="isLastMail(mail)">
		</div>
		<div class="row">
			<input type="submit" class="bouton" :value="messages.get('common', 'submit')">
		</div>
	</form>
</template>

<script>
    export default {
		props: ["mails", "mailtpls", "selvars", "senders"],
		data: function() {
			return {
				templateSelected: { "gestion": 0, "opac": 0 },
				selvarSelected: { "gestion": 0, "opac": 0 }
			}
		},
		methods: {
			isLastMail: function(mail) {
				return Object.values(this.mails)[Object.values(this.mails).length-1].context != mail.context;
			},
			submit: async function(e) {
				let response = await this.ws.post('MFAMail', 'save', this.formattedFormData(e));
				this.notif.info(this.messages.get('common', 'success_save'));
			},
			insertTemplate: function(index) {
				if(this.templateSelected[index]) {
					let mail = this.mailtpls.find(mail => mail.id == this.templateSelected[index]);

					this.mails[index].content = mail.tpl;
					this.mails[index].object = mail.object;
				}
			},
			insertSelvar: function(index) {
				if(this.selvarSelected[index]) {
					let element = this.$refs["content_" + index][0];
					let start = element.selectionStart;
					let start_text = this.mails[index].content.substring(0, start);
					let end_text = this.mails[index].content.substring(start);

					this.mails[index].content = start_text + this.selvarSelected[index] + end_text;
				}
			},
			formattedFormData: function(e) {
				const formData = new FormData(e.target);

				let object = {};
				formData.forEach((value, key) => object[key] = value);

				return { objects: this.mails, formData: object };
			}
		}
	}
</script>