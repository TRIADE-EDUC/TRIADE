<template>
    <form v-if="entities && formData.messages">

        <div class="dsi-form-group">
            <label class="etiquette" for="viewEntityTypeList">{{ messages.get('dsi', 'view_form_entity_type') }}</label>
            <div class="dsi-form-group-content">
                <select id="viewEntityTypeList" name="viewEntityTypeList" class="saisie-50em" @focus="currentType = item.settings.entityType" v-model="item.settings.entityType">
                    <option value="0" disabled>{{ messages.get('dsi', 'view_form_default_entity_type') }}</option>
                    <option v-for="(entityType, index) in availableEntities"
                        :value="entityType.value"
                        :key="index" :disabled="entityType.disabled">
                            {{ entityType.label }}
                    </option>
                </select>
            </div>
        </div>

        <div class="dsi-form-group">
            <label class="etiquette" for="exportFormat">{{ formData.messages.export_format }}</label>
            <div class="dsi-form-group-content">
                <select id="exportFormat" name="exportFormat" class="saisie-50em" v-model="item.settings.exportFormat">
                    <option v-for="(exportOption, index) in formData.exports"
                        :value="exportOption.index" :key="index">
                            {{ exportOption.name }}
                    </option>
                </select>
            </div>
        </div>

        <div class="row">
            <hr>
        </div>

        <div class="row">
		    <h3>{{ formData.messages.export_links }}</h3>
		</div>

        <div class="dsi-form-group">
            <div class="colonne3">
                <input type="checkbox" class="switch" name="exportGenerateLink" id="exportGenerateLink" v-model="item.settings.exportGenerateLink">
                <label for="exportGenerateLink">&nbsp;</label>
            </div>
            <div class="colonne_suite">
                <label class="etiquette" for="exportGenerateLink">{{ formData.messages.export_generate_link }}</label>
            </div>
        </div>

        <Transition>
            <div v-show="item.settings.exportGenerateLink">
                <div class="row">
                    <h3>{{ formData.messages.export_record_links }}</h3>
                </div>

                <div class="dsi-form-group" v-for="(active, link) in item.settings.exportLinks" :key="link">
                    <div class="colonne3">
                        <input type="checkbox"
                            class="switch"
                            :name="`export${link}`"
                            :id="`export${link}`"
                            v-model="item.settings.exportLinks[link]"
                            :disabled="isDisabledLink(link)"
                            @change="checkLinks()">
                        <label :for="`export${link}`">&nbsp;</label>
                    </div>
                    <div class="colonne_suite">
                        <label class="etiquette" :for="`export${link}`">{{ getLinkMessages(link) }}</label>
                    </div>
                </div>

                <div class="row">
                    <h3>{{ formData.messages.export_series_links }}</h3>
                </div>

                <div class="dsi-form-group" v-for="(active, link) in item.settings.exportLinksSeries" :key="link">
                    <div class="colonne3">
                        <input type="checkbox"
                            class="switch"
                            :name="`export${link}`"
                            :id="`export${link}`"
                            v-model="item.settings.exportLinksSeries[link]"
                            :disabled="isDisabledLink(link)"
                            @change="checkLinks()">
                        <label :for="`export${link}`">&nbsp;</label>
                    </div>
                    <div class="colonne_suite">
                        <label class="etiquette" :for="`export${link}`">{{ getLinkMessages(link) }}</label>
                    </div>
                </div>
            </div>
        </Transition>
	</form>
</template>

<script>
export default {
    name : "export",
    props : ["item", "entities"],
    data: function () {
        return {
            formData: {
				messages: null,
				availableTypes: [],
				availableItems: [],
				exports: [],
                lenders: [],
                docsTypes: [],
                statutLists: [],
			}
        }
    },
	computed: {
		availableEntities: function() {
			let availableEntities = [];
			for (const entityType in this.entities) {
                const find = this.formData.availableItems.find(availableItem => availableItem == entityType);
                availableEntities.push({
                    value: entityType,
                    disabled: find != undefined ? false : true,
                    label: this.entities[entityType]
                });
			}

			return availableEntities;
		},
		currentLender: function() {
            if (this.item.settings.exportLender === 0) {
                return false;
            }

            let lender = this.formData.lenders.find(lender => lender.value == this.item.settings.exportLender);
            lender.docsTypes = lender.docsTypes.map(typedoc => parseInt(typedoc));
            lender.statutLists = lender.statutLists.map(statut => parseInt(statut));
            return lender;
		},
		availableTypeDocs: function() {
            if (this.item.settings.exportLender == 0) {
                return this.formData.docsTypes;
            }

            return this.formData.docsTypes.filter((typedoc) => {
                return this.currentLender.docsTypes.includes(typedoc.value)
            });
		},
		availableSatutsDocs: function() {
            if (this.item.settings.exportLender == 0) {
                return this.formData.statutLists;
            }

            return this.formData.statutLists.filter((statut) => {
                return this.currentLender.statutLists.includes(statut.value)
            });
		}
	},
    created : async function () {
        this.getAdditionalData();

		if (!this.item.settings) {
            this.$set(this.item, "settings", {
				entityType: 0,
				exportLender: 0,
				exportExplTypeDocs: [],
				exportExplStatuts: [],
				exportFormat: 0,
				exportGenerateLink: false,
				exportSaveExpl: false,
				exportSaveExplNum: false,
                exportLinks: {
                    mere: false,
                    fille: false,
                    horizontale: false,
                    notice_mere: false,
                    notice_fille: false,
                    notice_horizontale: false,
                },
                exportLinksSeries: {
                    bull_link: true,
                    art_link: true,
                    perio_link: true,
                    bulletinage: false,
                    notice_perio: false,
                    notice_art: false,
                }
			});
		}

        if (this.item.settings && !this.item.settings.exportFormat) {
            this.$set(this.item.settings, "exportFormat", 0);
        }

        if (this.item.settings && !this.item.settings.exportLender) {
            this.$set(this.item.settings, "exportLender", 0);
        }

        if (this.item.settings && !this.item.settings.exportExplTypeDocs) {
            this.$set(this.item.settings, "exportExplTypeDocs", []);
        }

        if (this.item.settings && !this.item.settings.exportExplStatuts) {
            this.$set(this.item.settings, "exportExplStatuts", []);
        }

		if (this.item.settings && !this.item.settings.exportGenerateLink) {
            this.$set(this.item.settings, "exportGenerateLink", false);
        }

		if (this.item.settings && !this.item.settings.exportSaveExpl) {
            this.$set(this.item.settings, "exportSaveExpl", false);
        }
		if (this.item.settings && !this.item.settings.exportSaveExplNum) {
            this.$set(this.item.settings, "exportSaveExplNum", false);
        }
		if (this.item.settings && !this.item.settings.exportLinks) {
            this.$set(this.item.settings, "exportLinks", {
                mere: false,
                fille: false,
                horizontale: false,
                notice_mere: false,
                notice_fille: false,
                notice_horizontale: false
            });
        }
		if (this.item.settings && !this.item.settings.exportLinksSeries) {
            this.$set(this.item.settings, "exportLinksSeries", {
                bull_link: true,
                art_link: true,
                perio_link: true,
                bulletinage: false,
                notice_perio: false,
                notice_art: false,
            });
        }
    },
    methods : {
        getAdditionalData: async function() {
            let response = await this.ws.get("views", `form/data/${this.item.type}/${this.item.id}`);
            if (response.error) {
                this.notif.error(response.messages);
            } else {
                this.$set(this, "formData", response);
            }
        },
        getLinkMessages: function (link) {
            return this.formData.messages[`export_record_link_${link}`] ?? link;
        },
        isDisabledLink: function (link) {
            switch (link) {
                case "notice_horizontale":
                    return this.item.settings.exportLinks['horizontale'] === false;
                case "notice_fille":
                    return this.item.settings.exportLinks['fille'] === false;
                case "notice_mere":
                    return this.item.settings.exportLinks['mere'] === false;
                case "notice_perio":
                    return this.item.settings.exportLinksSeries['perio_link'] === false;
                case "notice_art":
                    return this.item.settings.exportLinksSeries['art_link'] === false;
                default:
                    return false;
            }
        },
        checkLinks: function () {

            if (!this.item.settings.exportLinks['horizontale']) {
                this.item.settings.exportLinks['notice_horizontale'] = false;
            }

            if (!this.item.settings.exportLinks['fille']) {
                this.item.settings.exportLinks['notice_fille'] = false;
            }

            if (!this.item.settings.exportLinks['mere']) {
                this.item.settings.exportLinks['notice_mere'] = false;
            }

            if (!this.item.settings.exportLinksSeries['perio_link']) {
                this.item.settings.exportLinksSeries['notice_perio'] = false;
            }

            if (!this.item.settings.exportLinksSeries['art_link']) {
                this.item.settings.exportLinksSeries['notice_art'] = false;
            }
        }
    }
}
</script>

<style>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s linear;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>