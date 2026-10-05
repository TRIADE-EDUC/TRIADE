<template>
	<div v-if="formData && formData.messages">

		<div class="dsi-form-group">
			<label class="etiquette" for="display_nb_notice">
				{{ formData.messages['display_nb_result'] }}
			</label>
			<div class="dsi-form-group-content">
				<input type="checkbox" name="display_nb_notice" id="display_nb_notice" v-model="item.settings.displayNbNotice">
			</div>
		</div>

		<div class="dsi-form-group">
			<label class="etiquette" for="bannette_template">
				{{ formData.messages['bannette-tpl'] }}
			</label>
			<div class="dsi-form-group-content">
				<select id="bannette-template" name="bannette_template" v-model="item.settings.bannetteTemplate">
					<option value="0">{{ messages.get('dsi', 'parser_html_default_template') }}</option>
					<option
						v-for="(template, index) in formData.bannetteTemplates"
						:value="template.value"
						:key="index">
							{{ template.label }}
					</option>
				</select>
			</div>
		</div>

		<div class="dsi-form-group">
			<label class="etiquette" for="notice_template">
				{{ formData.messages['notices-tpl'] }}
			</label>
			<div class="dsi-form-group-content">
				<select id="notice-template" name="notice_template" v-model="item.settings.noticeTemplate">
					<option value="0">{{ messages.get('dsi', 'parser_html_default_template') }}</option>
					<option
						v-for="(template, index) in formData.noticeTemplates"
						:value="template.value"
						:key="index">
							{{ template.label }}
					</option>
				</select>
			</div>
		</div>

		<div class="dsi-form-group">
			<label class="etiquette" for="linked-grouping-view">{{ formData.messages['linked-grouping-view'] }}</label>
			<div class="dsi-form-group-content">
				<select id="linked-grouping-view" name="linked-grouping-view" v-model.number="item.settings.linkedView">
					<option value="0" disabled>{{ formData.messages['linked-grouping-view-select'] }}</option>
					<option v-for="(view, index) in formData.groupViews" :value="view.value" :key="index">
						{{ view.label }}
					</option>
				</select>
			</div>
		</div>

		<fieldset class="dsi-fieldset-setting">
			<legend class="dsi-legend-setting">
				{{ formData.messages['header-tpl'] }}
			</legend>
			<previousDSIEditor 
				:content="item.settings.headerTemplate" 
				:patterns="patterns"
				@changeContent="(content) => item.settings.headerTemplate = content">
			</previousDSIEditor>
		</fieldset>

		<fieldset class="dsi-fieldset-setting">
			<legend class="dsi-legend-setting">
				{{ formData.messages['footer-tpl'] }}
			</legend>
			<previousDSIEditor 
				:content="item.settings.footerTemplate" 
				:patterns="patterns"
				@changeContent="(content) => item.settings.footerTemplate = content">
			</previousDSIEditor>
		</fieldset>
	</div>
</template>

<script>
import previousDSIEditor from './previousDSIEditor.vue';
export default {
    props: {
		idDiffusion: {
			type: Number,
			default: () => { return 0 }
		},
		entities: {
			type: Object,
			default: () => { return {} }
		},
		item: {
			type: Object,
			default: () => {
				return {
					id: 0,
					idView: 0,
					model: true,
					name: "",
					tags: [],
					type: 15,
					settings: {
						bannetteTemplate: 0,
						noticeTemplate: 0,
						headerTemplate: "",
						footerTemplate: "",
						linkedView: 0,
						displayNbNotice: false
					}
				}
			}
		}
	},
	components: {
		previousDSIEditor
	},
	data: function () {
        return {
            formData: {
				bannetteTemplates: [],
				noticeTemplates: [],
				availableTypes: [],
				availableItems: [],
			},
			patterns: false,
        }
    },
    created : async function () {
        if (this.item.settings && !this.item.settings.bannetteTemplate) {
            this.$set(this.item.settings, "bannetteTemplate", 0);
        }

        if (this.item.settings && !this.item.settings.noticeTemplate) {
            this.$set(this.item.settings, "noticeTemplate", 0);
        }

		if (this.item.settings && !this.item.settings.headerTemplate) {
            this.$set(this.item.settings, "headerTemplate", "");
        }

        if (this.item.settings && !this.item.settings.footerTemplate) {
            this.$set(this.item.settings, "footerTemplate", "");
        }

		if (!this.item.settings.linkedView) {
            this.$set(this.item.settings, "linkedView", 0);
        }

		if (!this.item.settings.displayNbNotice) {
            this.$set(this.item.settings, "displayNbNotice", false);
        }

		await this.fetchPatterns();
    },
	mounted: async function () {
		let response = await this.ws.get("views", `form/data/${this.item.type}/${this.item.id}`);
		if (response.error) {
			this.notif.error(response.messages);
		} else {
			this.$set(this, "formData", response);
		}
	},
	methods: {
		fetchPatterns: async function() {
			let result = await this.ws.get('input', 'patterns');
			if (result.error) {
				this.notif.error(result.errorMessage);
			} else {
				this.$set(this, 'patterns', result);
			}
		}
	}
}
</script>